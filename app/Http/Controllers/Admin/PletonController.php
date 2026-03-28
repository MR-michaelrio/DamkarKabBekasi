<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pleton;
use Illuminate\Http\Request;

class PletonController extends Controller
{
    public function index()
    {
        $pletons = Pleton::withCount('drivers')->get();
        return view('admin.pletons.index', compact('pletons'));
    }

    public function create()
    {
        return view('admin.pletons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:pletons,name',
        ]);

        Pleton::create($request->all());

        return redirect()->route('admin.pletons.index')
            ->with('success', 'Pleton berhasil ditambahkan');
    }

    public function edit(Pleton $pleton)
    {
        return view('admin.pletons.edit', compact('pleton'));
    }

    public function update(Request $request, Pleton $pleton)
    {
        $request->validate([
            'name' => 'required|unique:pletons,name,' . $pleton->id,
        ]);

        $pleton->update($request->all());

        return redirect()->route('admin.pletons.index')
            ->with('success', 'Pleton berhasil diperbarui');
    }

    public function destroy(Pleton $pleton)
    {
        if ($pleton->drivers()->count() > 0) {
            return back()->with('error', 'Pleton tidak bisa dihapus karena masih memiliki anggota');
        }

        $pleton->delete();

        return redirect()->route('admin.pletons.index')
            ->with('success', 'Pleton berhasil dihapus');
    }
}
