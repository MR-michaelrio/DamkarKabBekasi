<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Models\EventRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        $data = $this->getScheduleData($month, $year);

        return view('admin.schedules.calendar', [
            'dispatches' => $data['grouped'],
            'currentDate' => $data['currentDate'],
        ]);
    }

    public function public(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        $data = $this->getScheduleData($month, $year);

        return view('admin.schedules.calendar', [
            'dispatches' => $data['grouped'],
            'currentDate' => $data['currentDate'],
            'isPublic' => true, // Flag to hide admin-only buttons/info
        ]);
    }

    private function getScheduleData($month, $year)
    {
        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        // Fetch Dispatches
        $dispatches = Dispatch::with(['ambulance', 'driver'])
            ->where(function($q) use ($month, $year) {
                $q->whereMonth('request_date', $month)->whereYear('request_date', $year)
                  ->orWhere(function($sq) use ($month, $year) {
                      $sq->whereNull('request_date')
                         ->whereMonth('created_at', $month)
                         ->whereYear('created_at', $year);
                  });
            })
            ->get()
            ->filter(fn($d) => is_null($d->event_request_id))
            ->map(function($d) {
                $d->calendar_type = 'dispatch';
                return $d;
            });

        // Fetch Requests (not rejected)
        $requests = \App\Models\PatientRequest::where('status', '!=', 'rejected')
            ->where(function($q) use ($month, $year) {
                $q->whereMonth('request_date', $month)->whereYear('request_date', $year)
                  ->orWhere(function($sq) use ($month, $year) {
                      $sq->whereNull('request_date')
                         ->whereMonth('created_at', $month)
                         ->whereYear('created_at', $year);
                  });
            })
            ->get()->map(function($r) {
                $r->calendar_type = 'request';
                return $r;
            });

        // Fetch Approved Events
        $events = EventRequest::where('status', 'approved')
            ->where(function($q) use ($currentDate) {
                $q->whereBetween('start_date', [$currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth()])
                  ->orWhereBetween('end_date', [$currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth()])
                  ->orWhere(function($sq) use ($currentDate) {
                      $sq->where('start_date', '<=', $currentDate->copy()->startOfMonth())
                         ->where('end_date', '>=', $currentDate->copy()->endOfMonth());
                  });
            })
            ->with(['dispatches.ambulance', 'dispatches.driver'])
            ->get();

        // Expand events to each day they occur
        $expandedEvents = collect();
        foreach($events as $event) {
            $start = $event->start_date->copy();
            $end = $event->end_date->copy();
            
            // Loop through each day of the event
            for ($date = $start; $date->lte($end); $date->addDay()) {
                // Only if within current month
                if ($date->month == $month && $date->year == $year) {
                    $dateStr = $date->format('Y-m-d');
                    $cloned = clone $event;
                    $cloned->calendar_date = $dateStr;
                    $cloned->calendar_type = 'event';

                    // FILTER DISPATCHES: Only show units active on this specific date
                    // A unit is active if:
                    // 1. Its request_date <= current date
                    // 2. It hasn't been replaced by another unit that ALSO started on or before current date
                    $activeOnThisDay = $event->dispatches->filter(function($d) use ($dateStr) {
                        $started = $d->request_date->format('Y-m-d') <= $dateStr;
                        if (!$started) return false;

                        // Check if it's been replaced by something that has already started
                        $replacedByActive = $d->eventRequest->dispatches->first(function($replacement) use ($d, $dateStr) {
                            return $replacement->replaced_dispatch_id == $d->id && 
                                   $replacement->request_date->format('Y-m-d') <= $dateStr;
                        });

                        return !$replacedByActive;
                    });

                    $cloned->setRelation('dispatches', $activeOnThisDay);
                    $expandedEvents->push($cloned);
                }
            }
        }

        // Combine Dispatches & Requests (Deduplicate)
        $dispatchIds = $dispatches->pluck('id')->toArray();
        $finalCollection = $dispatches->concat(
            $requests->filter(fn($r) => !in_array($r->dispatch_id, $dispatchIds))
        );

        // Group regular items
        $grouped = $finalCollection->sortBy('pickup_time')
            ->groupBy(function($item) {
                $date = $item->request_date ?? $item->created_at;
                return Carbon::parse($date)->format('Y-m-d');
            });

        // Merge Events into Grouped
        foreach($expandedEvents as $ee) {
            $dateKey = $ee->calendar_date;
            if (!$grouped->has($dateKey)) {
                $grouped->put($dateKey, collect());
            }
            $grouped->get($dateKey)->push($ee);
        }

        return [
            'grouped' => $grouped,
            'currentDate' => $currentDate
        ];
    }
}
