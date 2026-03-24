<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead
                class="bg-gray-50 text-gray-600 uppercase text-[10px] font-black tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left">Pelapor / Kejadian</th>
                    <th class="px-6 py-4 text-left">Armada / Driver</th>
                    <th class="px-6 py-4 text-left">Waktu</th>
                    <th class="px-6 py-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($dispatches as $d)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900">{{ $d->patient_name }}</div>
                        <div
                            class="text-[10px] @if($d->patient_condition === 'emergency') text-red-600 @else text-gray-500 @endif font-bold uppercase tracking-tighter">
                            {{ $d->patient_condition }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs font-bold text-gray-800">{{ $d->ambulance?->plate_number ?? '-'
                                }}</span>
                        </div>
                        <div class="text-[10px] text-gray-500 italic">{{ $d->driver?->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs text-gray-700 font-medium">{{ $d->created_at->format('H:i') }}</div>
                        <div class="text-[10px] text-gray-400">{{ $d->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                            @if($d->status === 'completed') bg-emerald-50 text-emerald-700
                            @elseif($d->status === 'pending') bg-blue-50 text-blue-700
                            @else bg-amber-50 text-amber-700 @endif">
                            {{ str_replace('_', ' ', $d->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic text-sm">
                        Tidak ada data dispatch untuk periode ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>