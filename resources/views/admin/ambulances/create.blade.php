@extends('layouts.app')

@section('title', 'Tambah Mobil Damkar | Damkar Dispatch')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-gray-50">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ➕ Tambah Mobil Damkar
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Masukkan data mobil damkar baru
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-xl p-6 border border-gray-100">

        <!-- Error Validation -->
        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.ambulances.store') }}" class="space-y-5">
            @csrf

            <!-- Code -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Kode Unit
                </label>
                <input type="text" name="code" required
                       placeholder="DAMKAR-01"
                       value="{{ old('code') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- Plate -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Plat Nomor
                </label>
                <input type="text" name="plate_number" required
                       placeholder="B 1234 ABC"
                       value="{{ old('plate_number') }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Tipe Armada
                </label>
                <select name="type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="" disabled {{ old('type') ? '' : 'selected' }}>Pilih Tipe</option>
                    @foreach($types as $type)
                        <option value="{{ $type->name }}" {{ old('type') == $type->name ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                    @if($types->isEmpty())
                        <option value="BASIC" {{ old('type') == 'BASIC' ? 'selected' : '' }}>BASIC</option>
                        <option value="Jenazah" {{ old('type') == 'Jenazah' ? 'selected' : '' }}>Jenazah</option>
                    @endif
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Atur tipe di menu <a href="{{ route('admin.ambulance-types.index') }}" class="text-blue-600 underline">Tipe Armada</a>
                </p>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Status
                </label>
                <select name="status"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="ready" {{ old('status') == 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="on_duty" {{ old('status') == 'on_duty' ? 'selected' : '' }}>On Duty</option>
                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4">
                <a href="{{ route('admin.ambulances.index') }}"
                   class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                    ← Kembali
                </a>

                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                    Simpan Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
