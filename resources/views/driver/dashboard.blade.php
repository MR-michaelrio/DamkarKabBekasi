<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Driver Dashboard | GMCI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-xl mx-auto px-4 py-6">

    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    🚑 Dashboard Ambulans
                </h2>
                <p class="text-sm text-gray-500">{{ auth('ambulance')->user()->plate_number }} ({{ auth('ambulance')->user()->username }})</p>
            </div>
            
            <form method="POST" action="{{ route('ambulance.logout') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">
                    Keluar Unit
                </button>
            </form>
        </div>
    </div>

    <!-- Active Dispatch -->
    @if($activeDispatch)
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="font-bold text-lg mb-3">📍 Dispatch Aktif</h2>
            
            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-gray-600">Pasien:</span>
                    <span class="font-semibold">{{ $activeDispatch->patient_name }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Kondisi:</span>
                    <span class="font-semibold">{{ ucfirst($activeDispatch->patient_condition) }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Jemput:</span>
                    <p class="text-gray-800">{{ $activeDispatch->pickup_address }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Tujuan:</span>
                    <p class="text-gray-800">{{ $activeDispatch->destination }}</p>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                        {{ ucfirst(str_replace('_', ' ', $activeDispatch->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- GPS Tracking & Journey Control -->
        <div class="bg-white rounded-lg shadow p-4 mb-4 relative overflow-hidden">
            @if($activeDispatch->is_paused)
                <div class="absolute inset-0 bg-yellow-50/80 backdrop-blur-[1px] flex items-center justify-center z-10">
                    <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full font-bold shadow-sm border border-yellow-200 animate-pulse">
                        ⏸️ SEDANG ISTIRAHAT
                    </div>
                </div>
            @endif

            <div class="flex justify-between items-center mb-3 relative z-20">
                <h2 class="font-bold text-lg">📋 Journey Control</h2>
                <button id="pause-btn" 
                        data-paused="{{ $activeDispatch->is_paused ? 'true' : 'false' }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $activeDispatch->is_paused ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
                    @if($activeDispatch->is_paused)
                        ▶️ Lanjut
                    @else
                        ⏸️ Istirahat
                    @endif
                </button>
            </div>
            
            <div id="tracking-status" class="mb-4">
                <div class="flex items-center gap-2">
                    <div id="status-indicator" class="w-3 h-3 {{ $activeDispatch->is_paused ? 'bg-yellow-400' : 'bg-gray-400' }} rounded-full"></div>
                    <span id="status-text" class="text-sm text-gray-600 font-medium">
                        {{ $activeDispatch->is_paused ? 'Tracking Dihentikan Sejenak' : 'Tracking belum dimulai' }}
                    </span>
                </div>
                <p id="last-update" class="text-xs text-gray-500 mt-1"></p>
            </div>

            @php
                $statusConfig = [
                    'assigned' => [
                        'label' => '🚀 Mulai Perjalanan',
                        'color' => 'bg-green-600 hover:bg-green-700',
                    ],
                    'enroute_pickup' => [
                        'label' => '📍 Sudah Sampai Lokasi Penjemputan / On Scene',
                        'color' => 'bg-blue-600 hover:bg-blue-700',
                    ],
                    'on_scene' => [
                        'label' => '🚚 OTW Titik Tuju',
                        'color' => 'bg-orange-600 hover:bg-orange-700',
                    ],
                    'enroute_destination' => [
                        'label' => '🏁 Sampai Titik Tuju',
                        'color' => 'bg-indigo-600 hover:bg-indigo-700',
                    ],
                    'arrived_destination' => [
                        'label' => '✅ Selesai',
                        'color' => 'bg-red-600 hover:bg-red-700',
                    ],
                ];
                $currentConfig = $statusConfig[$activeDispatch->status] ?? null;
            @endphp

            @if($currentConfig)
                <button id="journey-btn" 
                        data-status="{{ $activeDispatch->status }}"
                        {{ $activeDispatch->is_paused ? 'disabled' : '' }}
                        class="w-full {{ $currentConfig['color'] }} text-white font-bold py-4 px-6 rounded-xl shadow-lg transition duration-200 transform active:scale-95 flex items-center justify-center gap-2 {{ $activeDispatch->is_paused ? 'opacity-50 grayscale cursor-not-allowed' : '' }}">
                    {{ $currentConfig['label'] }}
                </button>
            @endif
        </div>

        <!-- Location Info -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="font-semibold mb-2">Lokasi Saat Ini</h3>
            <p id="current-location" class="text-sm text-gray-600">Menunggu GPS...</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-600">Tidak ada dispatch aktif</p>
        </div>
    @endif

</div>

<!-- Capacitor Library (Optional, usually injected by Capacitor but good for local dev/testing) -->
<script src="https://unpkg.com/@capacitor/core@latest/dist/capacitor.js"></script>

<script>
let trackingActive = false;
let watchId = null;
const ambulanceId = {{ auth('ambulance')->id() }};

const journeyBtn = document.getElementById('journey-btn');
const statusIndicator = document.getElementById('status-indicator');
const statusText = document.getElementById('status-text');
const lastUpdate = document.getElementById('last-update');
const currentLocation = document.getElementById('current-location');

// Capacitor Check
// Capacitor Check
const isCapacitor = window.hasOwnProperty('Capacitor') && window.Capacitor.hasOwnProperty('Plugins');
const CapacitorPlugins = isCapacitor ? window.Capacitor.Plugins : {};

// HTTPS / Secure Context Check
if (!window.isSecureContext && !isCapacitor && window.location.hostname !== 'localhost') {
    statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full animate-ping';
    statusText.innerHTML = '<span class="text-red-600 font-bold">⚠️ ERROR: Browser mewajibkan HTTPS untuk GPS.</span> Hubungi Admin untuk setup SSL.';
    console.error('Geolocation requires a secure context (HTTPS)');
}

async function initializeCapacitorTracking() {
    if (!isCapacitor || !CapacitorPlugins.BackgroundGeolocation) return;

    const { BackgroundGeolocation } = CapacitorPlugins;

    try {
        await BackgroundGeolocation.requestPermissions();
    } catch (e) {
        console.error('Failed to request Capacitor permissions:', e);
    }
}

if (isCapacitor) {
    statusText.textContent = 'Mobile App Mode: Ready';
    initializeCapacitorTracking();
}

const pauseBtn = document.getElementById('pause-btn');

pauseBtn?.addEventListener('click', async function() {
    const isCurrentlyPaused = this.getAttribute('data-paused') === 'true';
    
    this.disabled = true;
    const originalContent = this.innerHTML;
    this.innerHTML = `Wait...`;

    try {
        if (isCurrentlyPaused) {
            // Resume: Start tracking back
            await startTracking();
        } else {
            // Pause: Stop tracking
            await stopTracking();
        }

        const response = await fetch(`{{ route('driver.dispatches.toggle-pause', $activeDispatch->id ?? 0) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
            this.disabled = false;
            this.innerHTML = originalContent;
        }
    } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan: ' + e.message);
        this.disabled = false;
        this.innerHTML = originalContent;
    }
});

journeyBtn?.addEventListener('click', async function() {
    const currentStatus = this.getAttribute('data-status');
    
    // UI Loading state
    this.disabled = true;
    this.classList.add('opacity-75', 'cursor-not-allowed');
    const originalContent = this.innerHTML;
    this.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...`;

    try {
        // Special actions based on status
        if (currentStatus === 'assigned') {
            await startTracking();
        } else if (currentStatus === 'arrived_destination') {
            await stopTracking();
        }

        // Update status via API
        const response = await fetch(`{{ route('driver.dispatches.update-status', $activeDispatch->id ?? 0) }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Refresh page to show next state
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
            this.disabled = false;
            this.classList.remove('opacity-75', 'cursor-not-allowed');
            this.innerHTML = originalContent;
        }
    } catch (e) {
        console.error(e);
        alert('Terjadi kesalahan silakan coba lagi: ' + e.message);
        this.disabled = false;
        this.classList.remove('opacity-75', 'cursor-not-allowed');
        this.innerHTML = originalContent;
    }
});

async function startTracking() {
    if (isCapacitor && CapacitorPlugins.BackgroundGeolocation) {
        const { BackgroundGeolocation } = CapacitorPlugins;
        
        try {
            watchId = await BackgroundGeolocation.addWatcher(
                {
                    backgroundMessage: "GMCI sedang melacak lokasi ambulans...",
                    backgroundTitle: "Tracking Aktif",
                    requestPermissions: true,
                    stale: false,
                    distanceFilter: 10 // meters
                },
                (location, error) => {
                    if (error) {
                        console.error(error);
                        return;
                    }
                    if (location) {
                        updateUILocation(location.latitude, location.longitude);
                        sendLocation(location.latitude, location.longitude);
                    }
                }
            );
            trackingActive = true;
            updateUIStarted();
        } catch (e) {
            console.error('Capacitor Tracking Error:', e);
            // Fallback to browser geolocation if capacitor fails
            startBrowserTracking();
        }
    } else {
        startBrowserTracking();
    }
}

function startBrowserTracking() {
    if (!navigator.geolocation) {
        alert('GPS tidak didukung di browser ini');
        return;
    }

    watchId = navigator.geolocation.watchPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            updateUILocation(lat, lng);
            sendLocation(lat, lng);
        },
        (error) => {
            console.error('GPS Error:', error);
            statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
            statusText.textContent = 'Error GPS: ' + error.message;
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
    trackingActive = true;
    updateUIStarted();
}

async function stopTracking() {
    if (isCapacitor && CapacitorPlugins.BackgroundGeolocation) {
        const { BackgroundGeolocation } = CapacitorPlugins;
        if (watchId) {
            try {
                await BackgroundGeolocation.removeWatcher({ id: watchId });
            } catch (e) {
                console.error('Error removing Capacitor watcher:', e);
            }
            watchId = null;
        }
    } else {
        if (watchId) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }
    }

    trackingActive = false;
    updateUIStopped();
}

// Auto-start tracking if journey is already in progress
const autoStartStatuses = ['enroute_pickup', 'on_scene', 'enroute_destination'];
const currentDispatchStatus = "{{ $activeDispatch->status ?? '' }}";
const isPaused = {{ ($activeDispatch && $activeDispatch->is_paused) ? 1 : 0 }};

if (autoStartStatuses.includes(currentDispatchStatus) && !isPaused) {
    // Small delay to ensure everything is ready
    setTimeout(() => {
        console.log('Resuming tracking automatically...');
        startTracking();
    }, 1000);
}

function updateUILocation(lat, lng) {
    currentLocation.textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
    statusIndicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]';
    statusText.textContent = isCapacitor ? 'Mobile (App) Tracking Aktif' : 'Browser (Web) Tracking Aktif';
}

function updateUIStarted() {
    // No longer changing button here as page will reload
    statusIndicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
    statusText.textContent = 'Tracking Aktif';
}

function updateUIStopped() {
    statusIndicator.className = 'w-3 h-3 bg-gray-400 rounded-full';
    statusText.textContent = 'Tracking dihentikan';
}

function sendLocation(latitude, longitude) {
    fetch('/api/driver/location', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            ambulance_id: ambulanceId,
            latitude: latitude,
            longitude: longitude
        })
    })
    .then(async response => {
        if (!response.ok) {
            const errData = await response.json().catch(() => ({}));
            throw new Error(errData.message || `Server Error ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const now = new Date().toLocaleTimeString('id-ID');
            lastUpdate.textContent = `Terakhir update: ${now}`;
        }
    })
    .catch(error => {
        console.error('Error sending location:', error);
        statusText.innerHTML = `<span class="text-red-500 font-bold">⚠️ Gagal Kirim GPS:</span> ${error.message}`;
        statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
    });
}
</script>

</body>
</html>
