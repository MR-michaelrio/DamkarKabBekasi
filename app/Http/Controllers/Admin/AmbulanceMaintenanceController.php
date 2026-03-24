<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use App\Models\AmbulanceMaintenance;
use Illuminate\Http\Request;

class AmbulanceMaintenanceController extends Controller
{
    public function index(Ambulance $ambulance)
    {
        $maintenances = $ambulance->maintenances()->orderBy('maintenance_date', 'desc')->get();
        return view('admin.ambulances.maintenance.index', compact('ambulance', 'maintenances'));
    }

    public function store(Request $request, Ambulance $ambulance)
    {
        $validated = $request->validate([
            'maintenance_date' => 'required|date',
            'maintenance_type' => 'required|string',
            'workshop'         => 'required|string',
            'cost'             => 'required|numeric',
            'spare_parts'      => 'nullable|array',
            'odometer'         => 'required|integer',
        ]);

        $ambulance->maintenances()->create($validated);

        return redirect()->back()->with('success', 'Riwayat perbaikan berhasil ditambahkan');
    }

    public function destroy(AmbulanceMaintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->back()->with('success', 'Riwayat perbaikan berhasil dihapus');
    }
}
