<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ambulance;
use Illuminate\Http\Request;

class AmbulanceController extends Controller
{
    /**
     * Tampilkan daftar ambulans
     */
    public function index()
    {
        $ambulances = Ambulance::orderBy('created_at', 'desc')->get();
        return view('admin.ambulances.index', compact('ambulances'));
    }

    /**
     * Form tambah ambulans
     */
    public function create()
    {
        return view('admin.ambulances.create');
    }

    /**
     * Simpan ambulans baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'          => 'required|unique:ambulances,code',
            'plate_number'  => 'required',
            'type'          => 'required',
            'status'        => 'required',
        ]);

        // Use code as username for easier login
        $username = $validated['code'];
        
        Ambulance::create($validated + [
            'username' => $username,
            'password' => bcrypt('password'), // Default password
        ]);

        return redirect()->route('admin.ambulances.index')
            ->with('success', "Ambulans berhasil ditambahkan. Username: $username, Password: password");
    }

    /**
     * Form edit ambulans
     */
    public function edit(Ambulance $ambulance)
    {
        return view('admin.ambulances.edit', compact('ambulance'));
    }

    /**
     * Update ambulans
     */
    public function update(Request $request, Ambulance $ambulance)
    {
        $validated = $request->validate([
            'code'          => 'required|unique:ambulances,code,' . $ambulance->id,
            'plate_number'  => 'required',
            'type'          => 'required',
            'status'        => 'required',
            'password'      => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $ambulance->update($validated);

        return redirect()
            ->route('admin.ambulances.index')
            ->with('success', 'Ambulans berhasil diperbarui');
    }

    /**
     * Hapus ambulans
     */
    public function destroy(Ambulance $ambulance)
    {
        $ambulance->delete();

        return redirect()
            ->route('admin.ambulances.index')
            ->with('success', 'Ambulans berhasil dihapus');
    }
}
