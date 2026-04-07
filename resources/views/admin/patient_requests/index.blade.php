@extends('layouts.app')

@section('title', 'Laporan Masyarakat | Damkar Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            📋 Laporan Masyarakat
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Kelola laporan kejadian dari masyarakat
        </p>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-xl overflow-hidden"
        x-data="{
            loading: false,
            lastRequestId: {{ $requests->first()?->id ?? 0 }},
            refresh() {
                if (this.loading) return;
                this.loading = true;
                fetch(window.location.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    this.$refs.tableContainer.innerHTML = html;
                    // Check for new requests after refresh
                    this.checkForNewRequests();
                })
                .finally(() => {
                    this.loading = false;
                });
            },
            checkForNewRequests() {
                fetch('/api/check-new-requests?last_id=' + this.lastRequestId)
                .then(response => response.json())
                .then(data => {
                    if (data.new_requests && data.new_requests.length > 0) {
                        // Update last request ID
                        this.lastRequestId = Math.max(...data.new_requests.map(r => r.id));
                        // Trigger notifications for new requests
                        data.new_requests.forEach(request => {
                            this.triggerNotifications(request);
                        });
                    }
                })
                .catch(error => console.log('Error checking for new requests:', error));
            },
            triggerNotifications(request) {
                console.log('New patient request detected:', request);

                // Show browser notification
                if ('Notification' in window) {
                    if (Notification.permission === 'default') {
                        Notification.requestPermission().then(permission => {
                            if (permission === 'granted') {
                                this.showNotification(request);
                            }
                        });
                    } else if (Notification.permission === 'granted') {
                        this.showNotification(request);
                    }
                }

                // Play audio immediately if user has interacted, otherwise it will play on notification click
                if (window.audioContext) {
                    if (window.audioContext.userInteracted) {
                        window.audioContext.playEmergency();
                        window.audioContext.playTTS(request.tts_url);
                    }
                }
            },
            showNotification(request) {
                const notification = new Notification('🚨 Permintaan Baru Masuk!', {
                    body: `${request.patient_name} - ${request.service_type} di ${request.pickup_address}`,
                    icon: '{{ asset('logo-damkar.png') }}',
                    tag: 'new-patient-request-' + request.id,
                    requireInteraction: true
                });

                notification.onclick = function() {
                    window.focus();
                    // Play audio on notification click (this is user interaction)
                    if (window.audioContext) {
                        window.audioContext.playEmergency();
                        window.audioContext.playTTS(request.tts_url);
                    }
                    notification.close();
                };

                // Auto-close after 15 seconds
                setTimeout(() => {
                    notification.close();
                }, 15000);
            }
        }"
        x-init="setInterval(() => refresh(), 10000)"
        @new-patient-request.window="refresh()">
        <div class="overflow-x-auto" x-ref="tableContainer">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            <a href="{{ route('admin.patient-requests.index', ['direction' => ($direction === 'asc' ? 'desc' : 'asc')]) }}" class="flex items-center gap-1 hover:text-blue-600 transition">
                                Tanggal
                                @if($direction === 'asc')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Pelapor</th>
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
                                @if ($request->service_type === 'kebakaran')
                                    🔥 Kebakaran
                                @elseif ($request->service_type === 'rescue')
                                    🚒 Rescue
                                @else
                                    {{ strtoupper($request->service_type) }}
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm flex items-center gap-3">
                                <a href="{{ route('admin.patient-requests.show', $request) }}"
                                   class="text-blue-600 hover:text-blue-800 font-bold">
                                    Lihat
                                </a>
                                <a href="{{ route('admin.patient-requests.edit', $request) }}"
                                   class="text-amber-600 hover:text-amber-800 font-bold">
                                    Edit
                                </a>
                                <form action="{{ route('admin.patient-requests.destroy', $request) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus permintaan ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </form>
                                @if (in_array($request->status, ['pending', 'dispatched']))
                                    <a href="{{ route('admin.patient-requests.create-dispatch', $request) }}"
                                       class="text-green-600 hover:text-green-800 font-bold">
                                        {{ $request->status === 'dispatched' ? 'Tambah Armada' : 'Dispatch' }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                Belum ada laporan masuk
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
