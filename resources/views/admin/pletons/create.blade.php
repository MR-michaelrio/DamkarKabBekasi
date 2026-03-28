@extends('layouts.app')

@section('title', 'Tambah Pleton | Damkar Dispatch')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ➕ Tambah Pleton
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Buat kelompok pleton baru untuk personel
        </p>
    </div>

    <form action="{{ route('admin.pletons.store') }}" method="POST"
        class="bg-white p-6 rounded-xl shadow border border-gray-100 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pleton</label>
            <input name="name" placeholder="Contoh: Pleton 1" value="{{ old('name') }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" required>
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4 border-t border-gray-50">
            <a href="{{ route('admin.pletons.index') }}"
                class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                ← Kembali
            </a>
            <button
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                Simpan Pleton
            </button>
        </div>
    </form>
</div>
@endsection
