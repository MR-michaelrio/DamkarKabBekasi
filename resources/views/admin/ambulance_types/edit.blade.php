@extends('layouts.app')

@section('title', 'Edit Tipe Armada | Damkar Dispatch')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            ✏️ Edit Tipe Armada Damkar: {{ $ambulanceType->name }}
        </h1>
    </div>

    <div class="bg-white shadow rounded-xl p-6">
        <form action="{{ route('admin.ambulance-types.update', $ambulanceType) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Tipe</label>
                <input type="text" name="name" value="{{ old('name', $ambulanceType->name) }}" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t flex justify-end gap-3">
                <a href="{{ route('admin.ambulance-types.index') }}"
                   class="px-4 py-2 text-gray-500 font-semibold hover:text-gray-700">
                    Batal
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
