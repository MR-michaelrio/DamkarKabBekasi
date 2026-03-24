@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            📜 Log Dispatch
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Riwayat aktivitas penugasan secara realtime
        </p>
    </div>

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-600 uppercase text-xs">
                        <th class="px-6 py-4 whitespace-nowrap">Waktu</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Dispatch ID</th>
                        <th class="px-6 py-4 whitespace-nowrap">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap">Catatan aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $l)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-mono text-xs">
                            {{ $l->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-indigo-600">
                            #{{ $l->dispatch_id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded text-[10px] font-bold tracking-wider
                                @if($l->status === 'completed') bg-green-100 text-green-700
                                @elseif($l->status === 'pending') bg-blue-100 text-blue-700
                                @else bg-yellow-100 text-yellow-700 @endif border border-current opacity-80">
                                {{ strtoupper(str_replace('_',' ',$l->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $l->note }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                            Belum ada log aktivitas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection
