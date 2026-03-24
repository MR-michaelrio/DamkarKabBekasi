@extends('layouts.app')

@section('title', 'Edit Mobil Damkar | Damkar Dispatch')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ✏️ Edit Mobil Damkar
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Perbarui data mobil damkar
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

        <form method="POST" action="{{ route('admin.ambulances.update', $ambulance->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Code -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Kode Unit
                </label>
                <input type="text" name="code" required
                       value="{{ old('code', $ambulance->code) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- Plate -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Plat Nomor
                </label>
                <input type="text" name="plate_number" required
                       value="{{ old('plate_number', $ambulance->plate_number) }}"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Tipe Armada
                </label>
                <select name="type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="" disabled>Pilih Tipe</option>
                    @foreach($types as $type)
                        <option value="{{ $type->name }}" {{ old('type', $ambulance->type) == $type->name ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                    @if($types->isEmpty())
                        <option value="BASIC" {{ old('type', $ambulance->type) == 'BASIC' ? 'selected' : '' }}>BASIC</option>
                        <option value="Jenazah" {{ old('type', $ambulance->type) == 'Jenazah' ? 'selected' : '' }}>Jenazah</option>
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
                    <option value="ready" {{ old('status', $ambulance->status) === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="on_duty" {{ old('status', $ambulance->status) === 'on_duty' ? 'selected' : '' }}>On Duty</option>
                    <option value="maintenance" {{ old('status', $ambulance->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                </select>
            </div>

            <!-- Password (Optional) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Ganti Password (Opsional)
                </label>
                <input type="password" name="password"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       placeholder="Kosongkan jika tidak ingin mengubah">
                <p class="text-xs text-gray-500 mt-1 italic">
                    Gunakan password ini jika staff unit lupa password atau ingin direset oleh Admin.
                </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4">
                <a href="{{ route('admin.ambulances.index') }}"
                   class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                    ← Kembali
                </a>

                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                    Update Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
