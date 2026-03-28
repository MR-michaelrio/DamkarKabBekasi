<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
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
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'nullable',
            'pleton' => 'nullable|string|max:50',
            'license_number' => 'nullable',
            'status' => 'required',
        ]);

        Driver::create($request->all());

        return redirect()->route('admin.drivers.index')
            ->with('success', 'Driver berhasil ditambahkan');
    }

    public function edit(Driver $driver)
    {
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'pleton' => 'nullable|string|max:50',
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
