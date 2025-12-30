<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispatch;
use App\Models\Driver;
use App\Models\Ambulance;
use App\Models\DispatchLog;
use PDF;

class DispatchController extends Controller
{
    /**
     * LIST DISPATCH
     */
    public function index()
    {
        $dispatches = Dispatch::with(['driver','ambulance'])
            ->latest()
            ->get();

        return view('admin.dispatches.index', compact('dispatches'));
    }

    /**
     * FORM CREATE
     */
    public function create()
    {
        return view('admin.dispatches.create', [
            'drivers' => Driver::where('status','available')->get(),
            'ambulances' => Ambulance::where('status','ready')->get(),
        ]);
    }

    /**
     * STORE DISPATCH
     */
    public function store(Request $request)
    {
        $dispatch = Dispatch::create($request->validate([
            'patient_name' => 'required',
            'patient_condition' => 'required',
            'patient_phone' => 'nullable',
            'pickup_address' => 'required',
            'destination' => 'nullable',
            'driver_id' => 'required',
            'ambulance_id' => 'required',
        ]) + [
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        Driver::where('id',$dispatch->driver_id)
            ->update(['status'=>'on_duty']);

        Ambulance::where('id',$dispatch->ambulance_id)
            ->update(['status'=>'on_duty']);

        return redirect()
            ->route('admin.dispatches.index')
            ->with('success','Dispatch berhasil dibuat');
    }

    /**
     * NEXT STATUS
     */
    public function next(Dispatch $dispatch)
    {
        $flow = [
            'assigned',
            'enroute_pickup',
            'on_scene',
            'enroute_hospital',
            'completed'
        ];

        $currentIndex = array_search($dispatch->status, $flow);
        $nextStatus = $flow[$currentIndex + 1] ?? null;

        if ($nextStatus) {
            $dispatch->update(['status'=>$nextStatus]);
        }

        return back();
    }

    /**
     * DELETE DISPATCH
     */
    public function destroy(Dispatch $dispatch)
    {
        $dispatch->delete();
        return back()->with('success','Dispatch dihapus');
    }

    /**
     * EXPORT PDF
     */
    public function exportPdf()
    {
        $dispatches = Dispatch::with(['driver','ambulance'])
            ->latest()
            ->get();

        $pdf = PDF::loadView('admin.dispatches.pdf', compact('dispatches'))
            ->setPaper('a4','landscape');

        return $pdf->download('laporan-dispatch.pdf');
    }
}

