@extends('layouts.app')

@section('title', 'Edit Laporan | Damkar Admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ✏️ Edit Laporan Masyarakat
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Perbarui data laporan kejadian dari masyarakat
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

        <form method="POST" action="{{ route('admin.patient-requests.update', $patientRequest->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nama Pelapor
                    </label>
                    <input type="text" name="patient_name" required
                           value="{{ old('patient_name', $patientRequest->patient_name) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nomor HP / WA
                    </label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $patientRequest->phone) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Service Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Jenis Kejadian
                    </label>
                    <select name="service_type" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="kebakaran" {{ old('service_type', $patientRequest->service_type) === 'kebakaran' ? 'selected' : '' }}>🔥 Kebakaran</option>
                        <option value="rescue" {{ old('service_type', $patientRequest->service_type) === 'rescue' ? 'selected' : '' }}>🚒 Rescue</option>
                    </select>
                </div>

                <!-- Request Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Tanggal Kejadian
                    </label>
                    <input type="date" name="request_date" required
                           value="{{ old('request_date', $patientRequest->request_date->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Jam -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Jam Kejadian
                    </label>
                    <input type="time" name="pickup_time"
                           value="{{ old('pickup_time', $patientRequest->pickup_time) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <!-- Pickup Address -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Alamat TKP (Lokasi Kejadian)
                </label>
                <textarea name="pickup_address" required rows="3"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">{{ old('pickup_address', $patientRequest->pickup_address) }}</textarea>
            </div>

            <!-- Damkar Specific Fields -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Blok</label>
                    <input type="text" name="blok" value="{{ old('blok', $patientRequest->blok) }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">RT</label>
                    <input type="text" name="rt" value="{{ old('rt', $patientRequest->rt) }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">RW</label>
                    <input type="text" name="rw" value="{{ old('rw', $patientRequest->rw) }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan</label>
                    <input type="text" name="kelurahan" value="{{ old('kelurahan', $patientRequest->kelurahan) }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" value="{{ old('kecamatan', $patientRequest->kecamatan) }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nomor</label>
                    <input type="text" name="nomor" value="{{ old('nomor', $patientRequest->nomor) }}" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.patient-requests.index') }}"
                   class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                    ← Kembali
                </a>

                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                    Update Laporan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
