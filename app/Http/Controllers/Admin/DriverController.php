<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Pleton;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::latest()->get();
        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        $pletons = Pleton::orderBy('name')->get();
        return view('admin.drivers.create', compact('pletons'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'pleton_id' => 'nullable|exists:pletons,id',
            'license_number' => 'nullable',
            'status' => 'required',
        ]);

        Driver::create($request->all());

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver berhasil ditambahkan');
    }

    public function edit(Driver $driver)
    {
        $pletons = Pleton::orderBy('name')->get();
        return view('admin.drivers.edit', compact('driver', 'pletons'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'pleton_id' => 'nullable|exists:pletons,id',
        ]);

        $driver->update($request->all());

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver berhasil diperbarui');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();

        return back()->with('success', 'Driver dihapus');
    }
}
