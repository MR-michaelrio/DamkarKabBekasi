@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="mb-6 flex items-start justify-between">
        <div>
            <a href="{{ route('admin.event-requests.index') }}" class="text-sm text-gray-400 hover:text-gray-600 font-bold">← Kembali</a>
            <h1 class="text-2xl font-black text-gray-900 mt-1 flex items-center gap-3">
                @if($eventRequest->type === 'disaster')
                    <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-sm rounded-lg font-black">🚨 BENCANA</span>
                @else
                    <span class="px-2 py-0.5 bg-pink-100 text-pink-700 text-sm rounded-lg font-black">🎪 EVENT</span>
                @endif
                {{ $eventRequest->event_name }}
            </h1>
            <p class="text-gray-500 font-medium mt-1">
                {{ $eventRequest->start_date->format('d M Y') }} — {{ $eventRequest->end_date->format('d M Y') }}
                <span class="ml-2 text-xs font-black uppercase text-gray-400">({{ $eventRequest->start_date->diffInDays($eventRequest->end_date) + 1 }} hari)</span>
            </p>
        </div>
        <div class="flex gap-2">
            @if($eventRequest->status === 'approved' && $eventRequest->end_date->isAfter(now()->startOfDay()))
                <form action="{{ route('admin.event-requests.finish', $eventRequest) }}" method="POST" onsubmit="return confirm('Selesaikan event ini sekarang? Semua unit yang ditugaskan akan otomatis dibebaskan.')">
                    @csrf
                    <button type="submit" class="text-sm font-bold text-emerald-600 border border-emerald-200 rounded-lg px-3 py-2 hover:bg-emerald-50 transition">
                        ✅ Selesaikan Event
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.event-requests.edit', $eventRequest) }}" class="text-sm font-bold text-blue-600 border border-blue-200 rounded-lg px-3 py-2 hover:bg-blue-50 transition">Edit</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 font-bold rounded-r-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Assign New Unit --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-100 shadow p-6">
                <h2 class="font-black text-gray-800 mb-4 uppercase tracking-tight text-sm">➕ Assign Unit Baru</h2>
                <form action="{{ route('admin.event-requests.assign-unit', $eventRequest) }}" method="POST" id="assign-form" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Ambulance / Unit</label>
                        <select name="ambulance_id" required class="w-full rounded-lg border-gray-300 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Pilih Unit --</option>
                            @foreach($availableAmbulances as $amb)
                                <option value="{{ $amb->id }}">{{ $amb->code }} — {{ $amb->plate_number }}</option>
                            @endforeach
                        </select>
                        @if($availableAmbulances->isEmpty())
                            <p class="text-xs text-orange-500 font-bold mt-1">⚠️ Semua unit sedang bertugas</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Driver</label>
                        <select name="driver_id" required class="w-full rounded-lg border-gray-300 text-sm font-bold focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">-- Pilih Driver --</option>
                            @foreach($availableDrivers as $drv)
                                <option value="{{ $drv->id }}">{{ $drv->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" id="assign-btn"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3 rounded-xl shadow transition active:scale-95 disabled:opacity-50 text-sm">
                        <span id="assign-text">✅ TUGASKAN UNIT</span>
                        <span id="assign-loading" class="hidden">⌛ MENUGASKAN...</span>
                    </button>
                </form>
            </div>

            {{-- Event Info Card --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow p-6 mt-4">
                <h2 class="font-black text-gray-800 mb-3 uppercase tracking-tight text-sm">📋 Detail Permintaan</h2>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Status</span>
                        @if($eventRequest->status === 'approved')
                            <span class="text-emerald-600 font-black">● Disetujui</span>
                        @elseif($eventRequest->status === 'pending')
                            <span class="text-yellow-600 font-black">● Menunggu</span>
                        @else
                            <span class="text-red-600 font-black">● Ditolak</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Kebutuhan</span>
                        <p class="font-medium text-gray-700 leading-snug">{{ $eventRequest->needs ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block">Total Unit Aktif</span>
                        <p class="font-black text-gray-900 text-2xl">
                            {{ $eventRequest->dispatches->whereNotIn('status', ['completed'])->count() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Assigned Units Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-100 shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="font-black text-gray-800 uppercase tracking-tight text-sm">🚑 Unit Ditugaskan ({{ $eventRequest->dispatches->count() }})</h2>
                </div>

                @if($eventRequest->dispatches->isEmpty())
                    <div class="p-12 text-center text-gray-400">
                        <div class="text-4xl mb-2">🚫</div>
                        <p class="font-bold">Belum ada unit ditugaskan</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach($eventRequest->dispatches->sortByDesc('assigned_at') as $d)
                            <div class="p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($d->is_replacement)
                                                <span class="text-[9px] px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded font-black uppercase">PENGGANTI</span>
                                            @endif
                                            <span class="font-black text-gray-900">{{ $d->ambulance?->code ?? '?' }}</span>
                                            <span class="text-gray-400 text-sm">{{ $d->ambulance?->plate_number }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600">
                                            👤 {{ $d->driver?->name ?? 'No driver' }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-2">
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
                                            <span class="text-[10px] text-gray-400">{{ $d->assigned_at?->format('d M H:i') }}</span>
                                        </div>
                                    </div>

                                    {{-- Replace Unit Button — triggers a fixed modal --}}
                                    @if(!in_array($d->status, ['completed']))
                                        <button
                                            onclick="openReplaceModal({{ $d->id }}, '{{ $d->ambulance?->code ?? '?' }}')"
                                            class="text-xs font-black text-orange-600 border border-orange-200 rounded-lg px-3 py-1.5 hover:bg-orange-50 transition shrink-0">
                                            🔄 Ganti Unit
                                        </button>

                                        {{-- Hidden form for this dispatch — submitted via modal --}}
                                        <form id="replace-form-{{ $d->id }}"
                                              action="{{ route('admin.event-requests.replace-unit', [$eventRequest, $d]) }}"
                                              method="POST" class="hidden">
                                            @csrf
                                            <input type="hidden" name="ambulance_id"     id="modal-ambulance-{{ $d->id }}">
                                            <input type="hidden" name="driver_id"        id="modal-driver-{{ $d->id }}">
                                            <input type="hidden" name="replacement_date" id="modal-date-{{ $d->id }}">
                                            <input type="hidden" name="reason"           id="modal-reason-{{ $d->id }}">
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

{{-- ============================================================
     REPLACE UNIT MODAL — fixed, always on top, never clipped
     ============================================================ --}}
<div id="replace-modal"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 hidden"
     onclick="if(event.target===this) closeReplaceModal()">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

    {{-- Modal Box --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 z-10">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-black text-gray-900 text-base">🔄 Ganti Unit: <span id="modal-unit-label" class="text-orange-600"></span></h3>
            <button onclick="closeReplaceModal()" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Unit Baru</label>
                <select id="modal-amb-select" class="w-full rounded-xl border-gray-300 font-bold focus:ring-orange-500 focus:border-orange-500">
                    <option value="">-- Tetap Gunakan Unit Saat Ini --</option>
                    @foreach($availableAmbulances as $amb)
                        <option value="{{ $amb->id }}">{{ $amb->code }} — {{ $amb->plate_number }}</option>
                    @endforeach
                </select>
                @if($availableAmbulances->isEmpty())
                    <p class="text-xs text-orange-500 font-bold mt-1">⚠️ Semua unit sedang bertugas</p>
                @endif
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Driver Baru</label>
                <select id="modal-drv-select" class="w-full rounded-xl border-gray-300 font-bold focus:ring-orange-500 focus:border-orange-500">
                    <option value="">-- Tetap Gunakan Driver Saat Ini --</option>
                    @foreach($availableDrivers as $drv)
                        <option value="{{ $drv->id }}">{{ $drv->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Tanggal Efektif Penggantian</label>
                <input type="date" id="modal-date-input" 
                       min="{{ $eventRequest->start_date->format('Y-m-d') }}" 
                       max="{{ $eventRequest->end_date->format('Y-m-d') }}"
                       class="w-full rounded-xl border-gray-300 font-bold focus:ring-orange-500 focus:border-orange-500">
            </div>
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Alasan Penggantian (Opsional)</label>
                <input type="text" id="modal-reason-input" placeholder="Contoh: Unit mengalami kerusakan"
                       class="w-full rounded-xl border-gray-300 font-bold focus:ring-orange-500 focus:border-orange-500">
            </div>
            <button onclick="submitReplaceModal()"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-black py-3 rounded-xl shadow-lg transition active:scale-95 text-sm mt-2">
                KONFIRMASI GANTI UNIT
            </button>
        </div>
    </div>
</div>

<script>
    let activeDispatchId = null;

    function openReplaceModal(dispatchId, unitCode) {
        activeDispatchId = dispatchId;
        document.getElementById('modal-unit-label').textContent = unitCode;
        document.getElementById('modal-amb-select').value  = '';
        document.getElementById('modal-drv-select').value  = '';
        document.getElementById('modal-date-input').value = new Date().toISOString().split('T')[0];
        document.getElementById('modal-reason-input').value = '';
        document.getElementById('replace-modal').classList.remove('hidden');
    }

    function closeReplaceModal() {
        document.getElementById('replace-modal').classList.add('hidden');
        activeDispatchId = null;
    }

    function submitReplaceModal() {
        const ambVal    = document.getElementById('modal-amb-select').value;
        const drvVal    = document.getElementById('modal-drv-select').value;
        const dateVal   = document.getElementById('modal-date-input').value;
        const reasonVal = document.getElementById('modal-reason-input').value;

        if (!dateVal) {
            alert('Pilih tanggal efektif terlebih dahulu.');
            return;
        }

        // Inject values into the hidden form for this dispatch
        document.getElementById('modal-ambulance-' + activeDispatchId).value = ambVal;
        document.getElementById('modal-driver-'    + activeDispatchId).value = drvVal;
        document.getElementById('modal-date-'      + activeDispatchId).value = dateVal;
        document.getElementById('modal-reason-'    + activeDispatchId).value = reasonVal;

        document.getElementById('replace-form-' + activeDispatchId).submit();
    }

    // Assign form loading state
    document.getElementById('assign-form').addEventListener('submit', function() {
        document.getElementById('assign-btn').disabled = true;
        document.getElementById('assign-text').classList.add('hidden');
        document.getElementById('assign-loading').classList.remove('hidden');
    });
</script>
@endsection
