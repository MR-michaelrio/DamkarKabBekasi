@extends('layouts.app')

@section('title', 'Ambulances | GMCI Dispatch')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            🚑 Manajemen Ambulans
        </h1>

        <a href="{{ route('admin.ambulances.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow w-full sm:w-auto text-center">
            ➕ Tambah Ambulans
        </a>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-600 uppercase text-xs">
                        <th class="px-6 py-3 whitespace-nowrap">Kode</th>
                        <th class="px-6 py-3 whitespace-nowrap">Plat</th>
                        <th class="px-6 py-3 whitespace-nowrap">Tipe</th>
                        <th class="px-6 py-3 whitespace-nowrap">Status</th>
                        <th class="px-6 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($ambulances as $ambulance)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap">
                                {{ $ambulance->code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ambulance->plate_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ambulance->type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ambulance->status === 'ready')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        Ready
                                    </span>
                                @elseif($ambulance->status === 'on_duty')
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                        On Duty
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                        Maintenance
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                                <a href="{{ route('admin.ambulances.maintenance.index', $ambulance) }}"
                                   class="text-green-600 hover:text-green-800 font-bold">
                                    Riwayat
                                </a>

                                <a href="{{ route('admin.ambulances.edit', $ambulance) }}"
                                   class="text-blue-600 hover:text-blue-800 font-bold">
                                    Edit
                                </a>

                                <form action="{{ route('admin.ambulances.destroy', $ambulance) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        onclick="return confirm('Hapus ambulans ini?')"
                                        class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                🚑 Belum ada data ambulans
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
