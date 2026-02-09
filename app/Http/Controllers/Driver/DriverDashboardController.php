<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Dispatch;
use Illuminate\Http\Request;

class DriverDashboardController extends Controller
{
    public function index()
    {
        // Get authenticated ambulance
        $ambulance = auth('ambulance')->user();
        
        // Get active dispatch for this ambulance
        $activeDispatch = Dispatch::where('ambulance_id', $ambulance->id)
            ->whereIn('status', ['assigned', 'enroute_pickup', 'on_scene', 'enroute_hospital'])
            ->first();

        return view('driver.dashboard', compact('activeDispatch', 'ambulance'));
    }
}
