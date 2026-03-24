@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            🚒 Dispatch Unit Damkar
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Buat penugasan unit damkar baru berdasarkan laporan
        </p>
    </div>

    <form method="POST" action="{{ route('admin.dispatches.store') }}"
          class="bg-white p-6 rounded-xl shadow border border-gray-100 space-y-6">
        @csrf

        @if(isset($patientRequest) && $patientRequest)
            <input type="hidden" name="patient_request_id" value="{{ $patientRequest->id }}">
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pelapor</label>
                <input type="text" name="patient_name" required 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="{{ old('patient_name', $patientRequest->patient_name ?? '') }}">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">No HP Pelapor</label>
                <input type="text" name="patient_phone" 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="{{ old('patient_phone', $patientRequest->phone ?? '') }}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Kejadian</label>
                <input type="date" name="request_date" required 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="{{ old('request_date', isset($patientRequest) ? $patientRequest->request_date->format('Y-m-d') : date('Y-m-d')) }}">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Jam Kejadian</label>
                <input type="time" name="pickup_time" required 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="{{ old('pickup_time', $patientRequest->pickup_time ?? '') }}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Kejadian</label>
                <select name="patient_condition" id="patient_condition" required 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="kebakaran" {{ old('patient_condition', $patientRequest->service_type ?? '') === 'kebakaran' ? 'selected' : '' }}>🔥 Kebakaran</option>
                    <option value="rescue" {{ old('patient_condition', $patientRequest->service_type ?? '') === 'rescue' ? 'selected' : '' }}>🚒 Rescue</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Laporan</label>
                <input type="text" name="nomor" placeholder="Contoh: 001/Laporan"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="{{ old('nomor', $patientRequest->nomor ?? '') }}">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-gray-50 pt-6">
            <div class="md:col-span-3">
                <label class="block text-sm font-bold text-gray-700 mb-1">Alamat TKP (Lokasi Kejadian)</label>
                <textarea name="pickup_address" required rows="2"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 font-bold text-red-600">{{ old('pickup_address', $patientRequest->pickup_address ?? '') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Blok</label>
                <input type="text" name="blok" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="{{ old('blok', $patientRequest->blok ?? '') }}">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">RT</label>
                <input type="text" name="rt" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="{{ old('rt', $patientRequest->rt ?? '') }}">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">RW</label>
                <input type="text" name="rw" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="{{ old('rw', $patientRequest->rw ?? '') }}">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan</label>
                <input type="text" name="kelurahan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="{{ old('kelurahan', $patientRequest->kelurahan ?? '') }}">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                <input type="text" name="kecamatan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="{{ old('kecamatan', $patientRequest->kecamatan ?? '') }}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-50 pt-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Petugas (Driver)</label>
                <select name="driver_id" required 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Petugas --</option>
                    @foreach($drivers as $d)
                        <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->status }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Unit Mobil Damkar</label>
                <select name="ambulance_id" required 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Unit --</option>
                    @foreach($ambulances as $a)
                        <option value="{{ $a->id }}">{{ $a->plate_number }} ({{ $a->status }})</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6">
            <a href="{{ route('admin.dispatches.index') }}" 
               class="text-gray-600 hover:text-gray-800 font-bold w-full sm:w-auto text-center">
                ← Batal
            </a>
            <button class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg w-full sm:w-auto transition transform active:scale-95">
                🚒 Kirim Unit Damkar
            </button>
        </div>

    </form>
</div>
@endsection
