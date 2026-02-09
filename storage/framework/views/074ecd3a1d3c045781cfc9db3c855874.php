<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Real-Time - GMCI Ambulance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        #map {
            height: 100%;
        }
        .data-table {
            max-height: calc(50vh - 80px);
            overflow-y: auto;
        }
        .data-table::-webkit-scrollbar {
            width: 6px;
        }
        .data-table::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Header -->
<div class="bg-white shadow-sm border-b px-6 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="/portal" class="text-gray-600 hover:text-gray-800">← Kembali</a>
        <div class="h-6 w-px bg-gray-300"></div>
        <h1 class="text-xl font-bold text-gray-800">🗺️ Monitoring Real-Time</h1>
    </div>
    <div class="flex items-center gap-2">
        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
        <span class="text-sm text-gray-600">Auto-refresh: <span id="countdown">10</span>s</span>
    </div>
</div>

<!-- Main Content -->
<div class="flex h-[calc(100vh-60px)]">
    
    <!-- Map Section (60%) -->
    <div class="w-3/5 relative">
        <div id="map"></div>
    </div>

    <!-- Data Section (40%) -->
    <div class="w-2/5 bg-white border-l flex flex-col">
        
        <!-- Active Dispatches (Top Half) -->
        <div class="flex-1 border-b">
            <div class="bg-blue-50 px-4 py-3 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    🚨 Dispatch Aktif
                    <span id="dispatch-count" class="text-xs bg-blue-600 text-white px-2 py-1 rounded-full">0</span>
                </h2>
            </div>
            <div class="data-table p-4">
                <div id="dispatches-list" class="space-y-2">
                    <p class="text-gray-400 text-sm text-center py-8">Memuat data...</p>
                </div>
            </div>
        </div>

        <!-- Patient Requests (Bottom Half) -->
        <div class="flex-1">
            <div class="bg-green-50 px-4 py-3 border-b">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    📋 Permintaan Pasien
                    <span id="request-count" class="text-xs bg-green-600 text-white px-2 py-1 rounded-full">0</span>
                </h2>
            </div>
            <div class="data-table p-4">
                <div id="requests-list" class="space-y-2">
                    <p class="text-gray-400 text-sm text-center py-8">Memuat data...</p>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
// Initialize map
const map = L.map('map').setView([-6.200000, 106.816666], 11);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let markers = {};

// Status badges
const statusBadges = {
    'assigned': '<span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Ditugaskan</span>',
    'enroute_pickup': '<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Menuju Lokasi</span>',
    'on_scene': '<span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Di Lokasi</span>',
    'enroute_hospital': '<span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">Menuju RS</span>',
    'pending': '<span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Pending</span>',
    'dispatched': '<span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Dispatched</span>',
};

function updateData() {
    fetch('/monitoring/data')
        .then(response => response.json())
        .then(data => {
            updateMap(data.ambulances);
            updateDispatches(data.dispatches);
            updateRequests(data.requests);
        })
        .catch(error => console.error('Error:', error));
}

function updateMap(ambulances) {
    // Remove old markers
    Object.keys(markers).forEach(id => {
        if (!ambulances.find(a => a.id == id)) {
            map.removeLayer(markers[id]);
            delete markers[id];
        }
    });

    // Update/create markers
    ambulances.forEach(ambulance => {
        const popupContent = `
            <div class="p-2">
                <h3 class="font-bold text-lg">🚑 ${ambulance.plate_number}</h3>
                <p class="text-sm text-gray-600">${ambulance.code || '-'} - ${ambulance.type || '-'}</p>
                <p class="text-sm mt-1"><strong>Status:</strong> ${ambulance.status}</p>
                ${ambulance.dispatch ? `
                    <hr class="my-2">
                    <p class="text-sm"><strong>Pasien:</strong> ${ambulance.dispatch.patient_name}</p>
                    <p class="text-sm">${statusBadges[ambulance.dispatch.status] || ambulance.dispatch.status}</p>
                ` : '<p class="text-sm text-gray-500 mt-2">Tidak ada dispatch aktif</p>'}
                ${ambulance.last_update ? `<p class="text-xs text-gray-400 mt-2">${ambulance.last_update}</p>` : ''}
            </div>
        `;

        if (markers[ambulance.id]) {
            markers[ambulance.id].setLatLng([ambulance.latitude, ambulance.longitude]);
            markers[ambulance.id].setPopupContent(popupContent);
        } else {
            markers[ambulance.id] = L.marker([ambulance.latitude, ambulance.longitude])
                .addTo(map)
                .bindPopup(popupContent);
        }
    });
}

function updateDispatches(dispatches) {
    const container = document.getElementById('dispatches-list');
    document.getElementById('dispatch-count').textContent = dispatches.length;

    if (dispatches.length === 0) {
        container.innerHTML = '<p class="text-gray-400 text-sm text-center py-8">Tidak ada dispatch aktif</p>';
        return;
    }

    container.innerHTML = dispatches.map(d => `
        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <div class="font-semibold text-gray-800">${d.patient_name}</div>
                <div class="text-xs text-gray-500">${d.created_at}</div>
            </div>
            <div class="text-sm text-gray-600 mb-2">
                🚑 ${d.ambulance}
            </div>
            <div class="flex justify-between items-center">
                ${statusBadges[d.status] || d.status}
                ${d.patient_condition ? `<span class="text-xs text-red-600 font-semibold">${d.patient_condition}</span>` : ''}
            </div>
        </div>
    `).join('');
}

function updateRequests(requests) {
    const container = document.getElementById('requests-list');
    document.getElementById('request-count').textContent = requests.length;

    if (requests.length === 0) {
        container.innerHTML = '<p class="text-gray-400 text-sm text-center py-8">Tidak ada permintaan</p>';
        return;
    }

    container.innerHTML = requests.map(r => `
        <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
            <div class="flex justify-between items-start mb-2">
                <div class="font-semibold text-gray-800">${r.patient_name}</div>
                <div class="text-xs text-gray-500">${r.request_date}</div>
            </div>
            <div class="text-sm text-gray-600 mb-2">
                ${r.service_type === 'ambulance' ? '🚑 Ambulance' : '⚰️ Jenazah'}
                ${r.patient_condition ? ` - ${r.patient_condition}` : ''}
            </div>
            <div>
                ${statusBadges[r.status] || r.status}
            </div>
        </div>
    `).join('');
}

// Initial load
updateData();

// Auto-refresh countdown
let countdown = 10;
setInterval(() => {
    countdown--;
    document.getElementById('countdown').textContent = countdown;
    
    if (countdown === 0) {
        updateData();
        countdown = 10;
    }
}, 1000);
</script>

</body>
</html>
<?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/monitoring/index.blade.php ENDPATH**/ ?>