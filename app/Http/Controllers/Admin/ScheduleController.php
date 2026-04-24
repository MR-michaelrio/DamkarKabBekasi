<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        
        // Fetch Approved Events Only
        $events = EventRequest::where('status', 'approved')
            ->where(function($q) use ($currentDate) {
                $q->whereBetween('start_date', [$currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth()])
                  ->orWhereBetween('end_date', [$currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth()])
                  ->orWhere(function($sq) use ($currentDate) {
                      $sq->where('start_date', '<=', $currentDate->copy()->startOfMonth())
                         ->where('end_date', '>=', $currentDate->copy()->endOfMonth());
                  });
            })
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
                    $expandedEvents->push($cloned);
                }
            }
        }

        // Group events by date
        $grouped = $expandedEvents->groupBy(function($event) {
            return $event->calendar_date;
        });

        return [
            'grouped' => $grouped,
            'currentDate' => $currentDate
        ];
    }
}
