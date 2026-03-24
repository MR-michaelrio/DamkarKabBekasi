<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmbulanceType;
use Illuminate\Http\Request;

class AmbulanceTypeController extends Controller
{
    public function index()
    {
        $types = AmbulanceType::all();
        return view('admin.ambulance_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.ambulance_types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:ambulance_types,name',
        ]);

        AmbulanceType::create($validated);

        return redirect()->route('admin.ambulance-types.index')
            ->with('success', 'Tipe armada berhasil ditambahkan');
    }

    public function edit(AmbulanceType $ambulanceType)
    {
        return view('admin.ambulance_types.edit', compact('ambulanceType'));
    }

    public function update(Request $request, AmbulanceType $ambulanceType)
    {
        $validated = $request->validate([
            'name' => 'required|unique:ambulance_types,name,' . $ambulanceType->id,
        ]);

        $ambulanceType->update($validated);

        return redirect()->route('admin.ambulance-types.index')
            ->with('success', 'Tipe armada berhasil diperbarui');
    }

    public function destroy(AmbulanceType $ambulanceType)
    {
        $ambulanceType->delete();
        return redirect()->route('admin.ambulance-types.index')
            ->with('success', 'Tipe armada berhasil dihapus');
    }
}
