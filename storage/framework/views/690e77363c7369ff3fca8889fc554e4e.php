<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
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
                <p class="text-sm text-gray-500"><?php echo e(auth('ambulance')->user()->plate_number); ?> (<?php echo e(auth('ambulance')->user()->username); ?>)</p>
            </div>
            
            <form method="POST" action="<?php echo e(route('ambulance.logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold text-sm">
                    Keluar Unit
                </button>
            </form>
        </div>
    </div>

    <!-- Active Dispatch -->
    <?php if($activeDispatch): ?>
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h2 class="font-bold text-lg mb-3">📍 Dispatch Aktif</h2>
            
            <div class="space-y-2 text-sm">
                <div>
                    <span class="text-gray-600">Pasien:</span>
                    <span class="font-semibold"><?php echo e($activeDispatch->patient_name); ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Kondisi:</span>
                    <span class="font-semibold"><?php echo e(ucfirst($activeDispatch->patient_condition)); ?></span>
                </div>
                <div>
                    <span class="text-gray-600">Jemput:</span>
                    <p class="text-gray-800"><?php echo e($activeDispatch->pickup_address); ?></p>
                </div>
                <div>
                    <span class="text-gray-600">Tujuan:</span>
                    <p class="text-gray-800"><?php echo e($activeDispatch->destination); ?></p>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-semibold">
                        <?php echo e(ucfirst(str_replace('_', ' ', $activeDispatch->status))); ?>

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
    <?php else: ?>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <p class="text-gray-600">Tidak ada dispatch aktif</p>
        </div>
    <?php endif; ?>

</div>

<script>
let trackingActive = false;
let watchId = null;
const ambulanceId = <?php echo e(auth('ambulance')->id()); ?>;

const toggleBtn = document.getElementById('toggle-tracking');
const statusIndicator = document.getElementById('status-indicator');
const statusText = document.getElementById('status-text');
const lastUpdate = document.getElementById('last-update');
const currentLocation = document.getElementById('current-location');

toggleBtn?.addEventListener('click', function() {
    if (trackingActive) {
        stopTracking();
    } else {
        startTracking();
    }
});

function startTracking() {
    if (!navigator.geolocation) {
        alert('GPS tidak didukung di browser ini');
        return;
    }

    watchId = navigator.geolocation.watchPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            
            // Update UI
            currentLocation.textContent = `Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`;
            statusIndicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
            statusText.textContent = 'Tracking aktif';
            
            // Send to server
            sendLocation(lat, lng);
        },
        (error) => {
            console.error('GPS Error:', error);
            statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full';
            statusText.textContent = 'Error: ' + error.message;
        },
        {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        }
    );

    trackingActive = true;
    toggleBtn.textContent = '⏸️ Stop Tracking';
    toggleBtn.className = 'w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition duration-200';
}

function stopTracking() {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }

    trackingActive = false;
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
<?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/driver/dashboard.blade.php ENDPATH**/ ?>