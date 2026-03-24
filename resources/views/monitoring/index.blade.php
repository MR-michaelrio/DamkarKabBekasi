<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Real-Time - Damkar Bekasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        body,
        html {
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }

        #map {
            height: 100%;
        }

        /* Mobile height adjustments */
        .data-table {
            max-height: 40vh;
            overflow-y: auto;
        }

        @media (min-width: 1024px) {
            .data-table {
                max-height: calc(50vh - 80px);
            }
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

<body class="bg-gray-50 flex flex-col h-screen overflow-hidden">

    <!-- Header -->
    <div class="bg-white shadow-sm border-b px-4 py-3 flex items-center justify-between gap-4 flex-shrink-0">
        <div class="flex items-center gap-3">
            <a href="/portal" class="text-red-600 hover:text-red-800 font-bold flex items-center gap-1">
                <span class="text-lg">←</span> <span class="hidden sm:inline">Portal</span>
            </a>
            <div class="h-6 w-px bg-gray-300"></div>
            <h1 class="text-lg font-extrabold text-gray-800 truncate">🗺️ Monitoring</h1>
        </div>
        <div class="flex items-center gap-3 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                <span class="text-[10px] font-black text-gray-700 uppercase tracking-tighter">Live</span>
            </div>
            <div class="w-px h-3 bg-gray-300"></div>
            <span class="text-xs font-bold text-gray-600">
                <span id="countdown" class="font-mono font-black text-red-600">10</span>s
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row flex-1 overflow-hidden">

        <!-- Map Section (Top on mobile, 60% on desktop) -->
        <div class="h-[40vh] lg:h-full lg:w-3/5 relative border-b lg:border-b-0">
            <div id="map"></div>
        </div>

        <!-- Data Section (Bottom on mobile, 40% on desktop) -->
        <div class="flex-1 lg:w-2/5 bg-white lg:border-l flex flex-col overflow-y-auto lg:overflow-hidden">

            <!-- Active Dispatches -->
            <div class="flex-1 flex flex-col min-h-[300px] lg:min-h-0 border-b">
                <div class="bg-red-50 px-4 py-3 border-b flex justify-between items-center sticky top-0 z-10">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        🚨 Dispatch Aktif
                    </h2>
                    <span id="dispatch-count"
                        class="text-xs font-black bg-red-600 text-white px-2 py-0.5 rounded-full shadow-sm">0</span>
                </div>
                <div class="data-table p-4 flex-1">
                    <div id="dispatches-list" class="space-y-3">
                        <p class="text-gray-400 text-sm text-center py-8">Memuat data...</p>
                    </div>
                </div>
            </div>

            <!-- Patient Requests -->
            <div class="flex-1 flex flex-col min-h-[300px] lg:min-h-0">
                <div class="bg-green-50 px-4 py-3 border-b flex justify-between items-center sticky top-0 z-10">
                    <h2 class="font-bold text-gray-800 flex items-center gap-2">
                        📋 Laporan Masyarakat
                    </h2>
                    <span id="request-count"
                        class="text-xs font-black bg-emerald-600 text-white px-2 py-0.5 rounded-full shadow-sm">0</span>
                </div>
                <div class="data-table p-4 flex-1">
                    <div id="requests-list" class="space-y-3">
                        <p class="text-gray-400 text-sm text-center py-8">Memuat data...</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <script>
        // Initialize map
        const map = L.map('map').setView([-6.2707338, 107.0901348], 11);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);

        let markers = {};

        // Status badges
        const statusBadges = {
            'pending': '<span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Pending</span>',
            'on_the_way_scene': '<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Menuju TKP</span>',
            'on_scene': '<span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded">Di TKP</span>',
            'on_the_way_kantor_pos': '<span class="text-xs bg-orange-100 text-orange-800 px-2 py-1 rounded">Menuju Kantor/Pos</span>',
            'completed': '<span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Selesai</span>',
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
                <h3 class="font-bold text-lg">🚒 ${ambulance.plate_number}</h3>
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
                🚒 ${d.ambulance}
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
                ${r.service_type === 'ambulance' ? '🚒 Unit Damkar' : '🚒 Rescue'}
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