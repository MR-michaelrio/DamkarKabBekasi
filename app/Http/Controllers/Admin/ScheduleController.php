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
        
        // Fetch Dispatches
        $dispatches = Dispatch::with(['ambulance', 'driver'])
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->get();

        // Fetch Pending / Waiting Requests
        $requests = \App\Models\PatientRequest::where('status', 'pending')
            ->whereMonth('request_date', $month)
            ->whereYear('request_date', $year)
            ->get();

        // Merge and Group
        $data = $dispatches->concat($requests)
            ->sortBy('pickup_time')
            ->groupBy(function($item) {
                if ($item->request_date instanceof Carbon) {
                    return $item->request_date->format('Y-m-d');
                }
                return Carbon::parse($item->request_date)->format('Y-m-d');
            });

        return view('admin.schedules.calendar', [
            'dispatches' => $data,
            'currentDate' => $currentDate,
        ]);
    }
}
