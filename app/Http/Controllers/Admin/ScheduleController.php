<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        
        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        // Fetch All Dispatches
        // We use whereMonth on either request_date OR created_at (as fallback if request_date is null)
        $dispatches = Dispatch::with(['ambulance', 'driver'])
            ->where(function($q) use ($month, $year) {
                $q->whereMonth('request_date', $month)->whereYear('request_date', $year)
                  ->orWhere(function($sq) use ($month, $year) {
                      $sq->whereNull('request_date')
                         ->whereMonth('created_at', $month)
                         ->whereYear('created_at', $year);
                  });
            })
            ->get();

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
            ->get();

        // Combine and Deduplicate
        // If a request has a dispatch_id, and we have that dispatch in our collection, we skip the request.
        $dispatchIds = $dispatches->pluck('id')->toArray();
        
        $finalCollection = $dispatches->concat(
            $requests->filter(fn($r) => !in_array($r->dispatch_id, $dispatchIds))
        );

        // Group by the best available date
        $data = $finalCollection->sortBy('pickup_time')
            ->groupBy(function($item) {
                $date = $item->request_date ?? $item->created_at;
                if ($date instanceof Carbon) {
                    return $date->format('Y-m-d');
                }
                return Carbon::parse($date)->format('Y-m-d');
            });

        return view('admin.schedules.calendar', [
            'dispatches' => $data,
            'currentDate' => $currentDate,
        ]);
    }
}
