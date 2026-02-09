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

<div class="max-w-md mx-auto px-4 py-6">

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

        <!-- GPS Tracking Control -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="font-bold text-lg mb-3">📡 GPS Tracking</h2>
            
            <div id="tracking-status" class="mb-4">
                <div class="flex items-center gap-2">
                    <div id="status-indicator" class="w-3 h-3 bg-gray-400 rounded-full"></div>
                    <span id="status-text" class="text-sm text-gray-600">Tracking belum dimulai</span>
                </div>
                <p id="last-update" class="text-xs text-gray-500 mt-1"></p>
            </div>

            <button id="toggle-tracking" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition duration-200">
                🚀 Mulai Tracking
            </button>
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

const toggleBtn = document.getElementById('toggle-tracking');
const statusIndicator = document.getElementById('status-indicator');
const statusText = document.getElementById('status-text');
const lastUpdate = document.getElementById('last-update');
const currentLocation = document.getElementById('current-location');

// Capacitor Check
const isCapacitor = window.hasOwnProperty('Capacitor');

async function initializeCapacitorTracking() {
    if (!isCapacitor) return;

    // We use a dynamic import or global check for the plugin
    // In a real Capacitor build, the plugin is registered on window.Capacitor.Plugins
    const { BackgroundGeolocation } = window.Capacitor.Plugins;

    if (!BackgroundGeolocation) {
        console.warn('Background Geolocation Plugin not found');
        return;
    }

    // Request permissions
    await BackgroundGeolocation.requestPermissions();
}

if (isCapacitor) {
    statusText.textContent = 'Mobile App Mode: Ready';
    initializeCapacitorTracking();
}

toggleBtn?.addEventListener('click', async function() {
    if (trackingActive) {
        await stopTracking();
    } else {
        await startTracking();
    }
});

async function startTracking() {
    if (isCapacitor) {
        const { BackgroundGeolocation } = window.Capacitor.Plugins;
        
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
            alert("Gagal memulai background tracking: " + e.message);
        }
    } else {
        // Fallback to standard browser geolocation
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
                statusText.textContent = 'Error: ' + error.message;
            },
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
        trackingActive = true;
        updateUIStarted();
    }
}

async function stopTracking() {
    if (isCapacitor) {
        const { BackgroundGeolocation } = window.Capacitor.Plugins;
        if (watchId) {
            await BackgroundGeolocation.removeWatcher({ id: watchId });
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

function updateUILocation(lat, lng) {
    currentLocation.textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
    statusIndicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
    statusText.textContent = isCapacitor ? 'Mobile Tracking Aktif' : 'Web Tracking Aktif';
}

function updateUIStarted() {
    toggleBtn.textContent = '⏸️ Stop Tracking';
    toggleBtn.className = 'w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition duration-200';
}

function updateUIStopped() {
    statusIndicator.className = 'w-3 h-3 bg-gray-400 rounded-full';
    statusText.textContent = 'Tracking dihentikan';
    toggleBtn.textContent = '🚀 Mulai Tracking';
    toggleBtn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition duration-200';
}

function sendLocation(latitude, longitude) {
    fetch('/api/driver/location', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            ambulance_id: ambulanceId,
            latitude: latitude,
            longitude: longitude
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const now = new Date().toLocaleTimeString('id-ID');
            lastUpdate.textContent = `Terakhir update: ${now}`;
        }
    })
    .catch(error => {
        console.error('Error sending location:', error);
    });
}
</script>

</body>
</html>
