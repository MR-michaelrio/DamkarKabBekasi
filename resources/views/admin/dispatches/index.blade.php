@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">
            🚨 Dispatch Ambulans
        </h1>

        <div class="flex gap-2 w-full sm:w-auto">
            <!-- EXPORT PDF -->
            <a href="{{ route('admin.dispatches.export.pdf') }}"
               class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-sm shadow flex-1 sm:flex-none text-center">
                📄 Export PDF
            </a>

            <!-- DISPATCH BARU -->
            <a href="{{ route('admin.dispatches.create') }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm shadow flex-1 sm:flex-none text-center">
                ➕ Dispatch Baru
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left">Pasien</th>
                    <th class="px-4 py-3 text-left">Jadwal</th>
                    <th class="px-4 py-3 text-left">Lokasi</th>
                    <th class="px-4 py-3 text-left">Driver</th>
                    <th class="px-4 py-3 text-left">Ambulans</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($dispatches as $d)
                <tr class="border-t hover:bg-gray-50">

                    <!-- PASIEN -->
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $d->patient_name }}</div>
                        <div class="text-xs text-gray-500">
                            {{ strtoupper($d->patient_condition) }}
                        </div>
                    </td>

                    <!-- JADWAL -->
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $d->request_date?->format('d M Y') ?? '-' }}</div>
                        <div class="text-xs text-gray-500">{{ $d->pickup_time ?? '-' }}</div>
                    </td>

                    <!-- LOKASI -->
                    <td class="px-4 py-3">
                        {{ $d->pickup_address }}
                    </td>

                    <!-- DRIVER -->
                    <td class="px-4 py-3">
                        {{ $d->driver?->name ?? '-' }}
                    </td>

                    <!-- AMBULANS -->
                    <td class="px-4 py-3">
                        {{ $d->ambulance?->plate_number ?? '-' }}
                    </td>

                    <!-- STATUS -->
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 rounded text-xs font-semibold
                            @if($d->status === 'completed') bg-green-100 text-green-700
                            @elseif($d->status === 'pending') bg-blue-100 text-blue-700
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ str_replace('_',' ', strtoupper($d->status)) }}
                        </span>
                    </td>

                    <!-- AKSI -->
                    <td class="px-4 py-3 text-center">
                        <div class="flex justify-center gap-2">

                            <!-- DETAIL -->
                            <a href="{{ route('admin.dispatches.show', $d) }}"
                               class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-xs">
                                📄 Detail
                            </a>

                            @if($d->status !== 'completed')
                            <!-- NEXT -->
                            <form method="POST"
                                  action="{{ route('admin.dispatches.next', $d) }}">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                                    ▶ Next
                                </button>
                            </form>
                            @endif

                            <!-- DELETE -->
                            <form method="POST"
                                  action="{{ route('admin.dispatches.destroy', $d) }}"
                                  onsubmit="return confirm('Yakin hapus dispatch ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                                    🗑 Hapus
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Belum ada data dispatch
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
