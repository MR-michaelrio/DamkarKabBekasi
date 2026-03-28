@extends('layouts.app')

@section('title', 'Edit Driver | Damkar Dispatch')


@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ✏️ Edit Driver
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Perbarui data profil driver
        </p>
    </div>

    <form method="POST" action="{{ route('admin.drivers.update',$driver) }}"
        class="bg-white p-6 rounded-xl shadow border border-gray-100 space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Driver</label>
            <input name="name" value="{{ old('name', $driver?->name) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">No HP</label>
            <input name="phone" value="{{ old('phone', $driver->phone) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Pleton</label>
            <select name="pleton_id"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih Pleton --</option>
                @foreach($pletons as $pleton)
                    <option value="{{ $pleton->id }}" {{ old('pleton_id', $driver->pleton_id) == $pleton->id ? 'selected' : '' }}>
                        {{ $pleton->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">No SIM</label>
            <input name="license_number" value="{{ old('license_number', $driver->license_number) }}"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
            <select name="status"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="available" {{ old('status', $driver->status) === 'available' ? 'selected' : ''
                    }}>Available</option>
                <option value="on_duty" {{ old('status', $driver->status) === 'on_duty' ? 'selected' : '' }}>On Duty
                </option>
                <option value="inactive" {{ old('status', $driver->status) === 'inactive' ? 'selected' : '' }}>Inactive
                </option>
            </select>
        </div>

        <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4 border-t border-gray-50">
            <a href="{{ route('admin.drivers.index') }}"
                class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                ← Kembali
            </a>
            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                Update Driver
            </button>
        </div>
    </form>
</div>
@endsection