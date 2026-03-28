@extends('layouts.app')

@section('title', 'Detail Laporan | Damkar Admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">
            📋 Detail Laporan Masyarakat
        </h1>
        <a href="{{ route('admin.patient-requests.pdf', $patientRequest->id) }}" 
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold shadow-md transition transform active:scale-95 flex items-center gap-2">
            📄 Cetak PDF
        </a>
    </div>

    <!-- Request Details Card -->
    <div class="bg-white shadow rounded-xl p-6 space-y-6 border border-gray-100">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Nama Pelapor</label>
                <p class="text-lg font-bold text-gray-900 mt-1">{{ $patientRequest?->patient_name }}</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Tanggal Kejadian</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    {{ $patientRequest?->request_date?->format('d F Y') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Jam Kejadian</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    {{ $patientRequest?->pickup_time ? \Carbon\Carbon::parse($patientRequest->pickup_time)->format('H:i') : '-' }} WIB
                </p>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">No. Telepon (WA)</label>
                <p class="text-lg font-bold text-red-600 mt-1">{{ $patientRequest?->phone }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-50 pt-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Jenis Kejadian</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    @if ($patientRequest?->service_type === 'kebakaran')
                        🔥 Kebakaran
                    @elseif ($patientRequest?->service_type === 'rescue')
                        🚒 Rescue
                    @else
                        {{ strtoupper($patientRequest?->service_type ?? '') }}
                    @endif
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Kondisi / Ket. Tambahan</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    @if($patientRequest->patient_condition === 'kebakaran') 🔥 @elseif($patientRequest->patient_condition === 'rescue') 🚒 @endif
                    {{ strtoupper(str_replace('_', ' ', $patientRequest->patient_condition ?? '-')) }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-50 pt-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Status Laporan</label>
                <div class="mt-2">
                    @if ($patientRequest->status === 'pending')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm font-bold shadow-sm">
                            ⏳ Pending
                        </span>
                    @elseif ($patientRequest->status === 'dispatched')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-bold shadow-sm">
                            ✅ Dispatched
                        </span>
                    @elseif ($patientRequest->status === 'completed')
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm font-bold shadow-sm">
                            🏁 Selesai
                        </span>
                    @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm font-bold shadow-sm">
                            ❌ Rejected
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="border-t border-gray-50 pt-6">
            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Alamat TKP (Lokasi Kejadian)</label>
            <p class="text-gray-900 mt-2 bg-gray-50 p-3 rounded-lg border border-gray-100 font-bold text-red-600">{{ $patientRequest->pickup_address }}</p>
            <div class="grid grid-cols-2 gap-4 mt-2 text-xs text-gray-500">
                <span>Blok: {{ $patientRequest->blok ?? '-' }}</span>
                <span>RT/RW: {{ $patientRequest->rt ?? '-' }}/{{ $patientRequest->rw ?? '-' }}</span>
                <span>Kel: {{ $patientRequest->kelurahan ?? '-' }}</span>
                <span>Kec: {{ $patientRequest->kecamatan ?? '-' }}</span>
            </div>
        </div>

    </div>

    <!-- Dispatches Card -->
    @if($patientRequest->dispatches->count() > 0)
    <div class="bg-white shadow rounded-xl p-6 mt-6 border border-gray-100">
        <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm mb-4">🚑 Unit Ditugaskan ({{ $patientRequest->dispatches->count() }})</h2>
        <div class="divide-y divide-gray-50">
            @foreach($patientRequest->dispatches->sortByDesc('assigned_at') as $d)
                <div class="py-4 flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-black text-gray-900">{{ $d->ambulance?->code ?? '?' }}</span>
                            <span class="text-gray-400 text-sm">{{ $d->ambulance?->plate_number }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">👤 {{ $d->driver?->name ?? 'No driver' }}</p>
                        @php
                            $statusColors = [
                                'pending'               => 'bg-blue-100 text-blue-700',
                                'on_the_way_scene'      => 'bg-yellow-100 text-yellow-700',
                                'on_scene'              => 'bg-green-100 text-green-700',
                                'on_the_way_kantor_pos' => 'bg-orange-100 text-orange-700',
                                'completed'             => 'bg-gray-100 text-gray-500',
                            ];
                            $sc = $statusColors[$d->status] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider {{ $sc }}">
                            {{ str_replace('_', ' ', $d->status) }}
                        </span>
                    </div>
                    <div class="text-right flex flex-col items-end">
                        <span class="text-xs text-gray-400 block">{{ $d->assigned_at?->format('d M H:i') }}</span>
                        <div class="mt-auto flex items-center gap-2 mt-2">
                            @if($d->status !== 'completed')
                            <form action="{{ route('admin.dispatches.destroy', $d) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus armada ini dari penugasan?\n\nArmada dan driver akan dikembalikan menjadi tersedia.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-bold bg-white border border-red-500 rounded px-2 py-0.5 transition active:scale-95">Hapus</button>
                            </form>
                            @endif
                            <a href="{{ route('admin.dispatches.show', $d) }}" class="text-emerald-600 hover:text-emerald-800 text-xs font-bold bg-emerald-50 rounded px-2 py-0.5 border border-emerald-100">Detail Dispatch →</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <a href="{{ route('admin.patient-requests.index') }}"
           class="text-gray-600 hover:text-gray-800 font-bold flex items-center w-full sm:w-auto">
            ← Kembali
        </a>

        @if (in_array($patientRequest->status, ['pending', 'dispatched']))
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.patient-requests.create-dispatch', $patientRequest) }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg text-center transition transform active:scale-95">
                    {{ $patientRequest->status === 'dispatched' ? '➕ Tambah Armada' : '✅ Buat Dispatch' }}
                </a>

                @if ($patientRequest->status === 'pending')
                <form method="POST" action="{{ route('admin.patient-requests.reject', $patientRequest) }}"
                      class="inline"
                      onsubmit="return confirm('Yakin ingin menolak laporan ini?')">
                    @csrf
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg w-full transition transform active:scale-95">
                        ❌ Tolak
                    </button>
                </form>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection
