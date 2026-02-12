@extends('layouts.app')

@section('title', 'Permintaan Pasien | GMCI Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            📋 Permintaan Pasien
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Kelola permintaan layanan dari pasien/keluarga
        </p>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Requests Table -->
    <div class="bg-white shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Pasien</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($requests as $request)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                {{ $request->request_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                {{ $request->patient_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                @if ($request->service_type === 'ambulance')
                                    🚑 Ambulance
                                @else
                                    ⚰️ Jenazah
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                @if ($request->patient_condition === 'emergency')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">
                                        🚨 Emergency
                                    </span>
                                @elseif ($request->patient_condition === 'kontrol')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">
                                        🏥 Kontrol
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($request->status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">
                                        ⏳ Pending
                                    </span>
                                @elseif ($request->status === 'dispatched')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">
                                        ✅ Dispatched
                                    </span>
                                @elseif ($request->status === 'completed')
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">
                                        🏁 Selesai
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">
                                        ❌ Rejected
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm space-x-3">
                                <a href="{{ route('admin.patient-requests.show', $request) }}"
                                   class="text-blue-600 hover:text-blue-800 font-bold">
                                    Lihat
                                </a>
                                @if ($request->status === 'pending')
                                    <a href="{{ route('admin.patient-requests.create-dispatch', $request) }}"
                                       class="text-green-600 hover:text-green-800 font-bold">
                                        Dispatch
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                Belum ada permintaan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
