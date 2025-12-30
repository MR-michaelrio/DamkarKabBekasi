@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6">

    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold">🚨 Dispatch Ambulans</h1>

        <a href="{{ route('admin.dispatches.create') }}"
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
            + Dispatch Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3">Pasien</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Driver</th>
                    <th class="px-4 py-3">Ambulans</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
            @forelse($dispatches as $d)
                <tr class="border-t">
                    <td class="px-4 py-3">
                        <strong>{{ $d->patient_name }}</strong><br>
                        <span class="text-xs">{{ $d->patient_condition }}</span>
                    </td>

                    <td class="px-4 py-3">{{ $d->pickup_address }}</td>
                    <td class="px-4 py-3">{{ $d->driver?->name ?? '-' }}</td>
                    <td class="px-4 py-3">{{ $d->ambulance?->plate_number ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $d->status }}</td>

                    <td class="px-4 py-3 text-center space-x-2">
                        @if($d->status !== 'completed')
                        <form method="POST" action="{{ route('admin.dispatches.next', $d) }}" class="inline">
                            @csrf
                            <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                NEXT
                            </button>
                        </form>
                        @endif

                        <form method="POST" action="{{ route('admin.dispatches.destroy', $d) }}"
                              class="inline"
                              onsubmit="return confirm('Hapus dispatch ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-gray-600 text-white px-3 py-1 rounded text-xs">
                                HAPUS
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Belum ada dispatch
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
