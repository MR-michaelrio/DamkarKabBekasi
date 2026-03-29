<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use App\Models\Ambulance;
use App\Models\Driver;
use Carbon\Carbon;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Parameter Filter Bulan
        $selectedMonth = $request->get('month', Carbon::now()->month);
        $selectedYear = $request->get('year', Carbon::now()->year);

        // Detect if filter is active
        $isFiltered = $request->has('month') || $request->has('year');

        try {
            $filterDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        } catch (\Exception $e) {
            $filterDate = Carbon::now();
            $selectedMonth = $filterDate->month;
            $selectedYear = $filterDate->year;
            $isFiltered = false;
        }
        // =====================
        // TIMEFRAME DISPATCHES
        // =====================
        $todayDispatches = Dispatch::with(['driver', 'ambulance'])
            ->whereDate('created_at', Carbon::today())
            ->orderByDesc('created_at')
            ->get();

        $weekDispatches = Dispatch::with(['driver', 'ambulance'])
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->orderByDesc('created_at')
            ->get();

        $monthDispatches = Dispatch::with(['driver', 'ambulance'])
            ->whereMonth('created_at', $selectedMonth)
            ->whereYear('created_at', $selectedYear)
            ->orderByDesc('created_at')
            ->get();

        // =====================
        // AMBULANCE ANALYTICS (Filtered Month)
        // =====================
        $startOfMonth = $filterDate->copy()->startOfMonth();
        $endOfMonth = $filterDate->copy()->endOfMonth();

        $ambulanceAnalytics = Ambulance::withCount(['dispatches' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('created_at', [$startOfMonth, $endOfMonth]);
        }])->get();

        // =====================
        // SUMMARY STATS (Keep existing for potential use)
        // =====================
        $totalDispatch = Dispatch::count();
        $dispatchActive = Dispatch::whereNotIn('status', ['completed', 'cancelled'])->count();
        $dispatchEmergency = Dispatch::where('patient_condition', 'emergency')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        $ambulanceReady = Ambulance::where('status', 'ready')->count();
        $ambulanceOnDuty = Ambulance::where('status', 'on_duty')->count();

        return view('admin.dashboard', compact(
            'todayDispatches',
            'weekDispatches',
            'monthDispatches',
            'ambulanceAnalytics',
            'totalDispatch',
            'dispatchActive',
            'dispatchEmergency',
            'ambulanceReady',
            'ambulanceOnDuty',
            'selectedMonth',
            'selectedYear',
            'filterDate',
            'isFiltered'
        ));
    }
}
