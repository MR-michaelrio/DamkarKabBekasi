@extends('layouts.app')

@section('title', 'Peta Realtime | Damkar Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">🗺️ Peta Ambulans (Realtime)</h1>
            <p class="text-gray-500 text-xs mt-1">Gunakan unit panel di kiri untuk fokus ke lokasi</p>
        </div>
        
        <div class="flex items-center gap-3 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-200 w-full sm:w-auto justify-center">
            <div class="flex items-center gap-2">
                <div class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]"></div>
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Live</span>
            </div>
            <div class="w-px h-4 bg-gray-300"></div>
            <span class="text-sm font-medium text-gray-600">
                Refresh: <span id="refresh-timer" class="font-mono font-bold text-emerald-600">10</span>s
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar: Driver List -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[300px] sm:h-[400px] lg:h-[600px]">
            <div class="p-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-700 flex items-center gap-2">
                    🚑 Unit Aktif (<span id="active-count">0</span>)
                </h2>
            </div>
            <div id="ambulance-list" class="flex-1 overflow-y-auto p-2 space-y-2">
                <div class="p-8 text-center text-gray-400">
                    <p class="text-sm">Memuat data unit...</p>
                </div>
            </div>
        </div>

        <!-- Map -->
        <div class="lg:col-span-3">
            <div id="map" class="h-[400px] sm:h-[500px] lg:h-[600px] rounded-xl shadow border border-gray-100 overflow-hidden"></div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
const map = L.map('map').setView([-6.2707338, 107.0901348], 16);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap'
}).addTo(map);

let markers = {};

function focusAmbulance(id) {
    const marker = markers[id];
    if (marker) {
        map.flyTo(marker.getLatLng(), 15, {
            duration: 1.5
        });
        marker.openPopup();
    }
}

function updateAmbulances() {
    fetch('/admin/maps/ambulances')
        .then(response => response.json())
        .then(ambulances => {
            const listContainer = document.getElementById('ambulance-list');
            document.getElementById('active-count').textContent = ambulances.length;
            
            if (ambulances.length === 0) {
                listContainer.innerHTML = '<div class="p-8 text-center text-gray-400 text-sm">Tidak ada unit aktif</div>';
            } else {
                listContainer.innerHTML = '';
            }

            // Remove markers for ambulances that are no longer active
            Object.keys(markers).forEach(id => {
                if (!ambulances.find(a => a.id == id)) {
                    map.removeLayer(markers[id]);
                    delete markers[id];
                }
            });

            // Update or create markers
            ambulances.forEach(ambulance => {
                // Populate Sidebar List
                const listItem = document.createElement('div');
                listItem.className = 'p-3 rounded-lg border border-gray-100 hover:bg-slate-50 transition cursor-pointer group';
                listItem.onclick = () => focusAmbulance(ambulance.id);
                
                listItem.innerHTML = `
                    <div class="flex justify-between items-start mb-1">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800">${ambulance.plate_number}</span>
                            ${ambulance.dispatch && ambulance.dispatch.is_paused ? 
                                '<span class="text-[9px] font-bold text-yellow-600 animate-pulse">⏸️ SEDANG ISTIRAHAT</span>' : ''}
                        </div>
                        <span class="text-[10px] px-1.5 py-0.5 rounded font-bold uppercase ${ambulance.status === 'ready' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'}">
                            ${ambulance.status}
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-500 mb-2">${ambulance.code} • ${ambulance.type}</p>
                    <button class="w-full py-1.5 bg-indigo-50 text-indigo-600 text-xs font-bold rounded hover:bg-indigo-600 hover:text-white transition">
                        Fokus Lokasi
                    </button>
                `;
                listContainer.appendChild(listItem);

                const popupContent = `
                    <div class="p-2 min-w-[200px]">
                        <div class="flex justify-between items-start border-b pb-2 mb-2">
                             <div>
                                <h3 class="font-bold text-lg leading-tight">🚑 ${ambulance.plate_number}</h3>
                                <p class="text-[11px] text-gray-500">${ambulance.code} - ${ambulance.type}</p>
                             </div>
                             ${ambulance.dispatch && ambulance.dispatch.is_paused ? 
                                '<div class="bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-0.5 rounded border border-yellow-200 animate-pulse">PAUSED</div>' : ''}
                        </div>
                        
                        ${ambulance.dispatch ? `
                            <p class="text-sm"><strong>Pasien:</strong> ${ambulance.dispatch.patient_name}</p>
                            <p class="text-sm"><strong>Status:</strong> ${ambulance.dispatch.status.replace(/_/g, ' ')}</p>
                            <p class="text-sm line-clamp-2"><strong>Jemput:</strong> ${ambulance.dispatch.pickup_address}</p>
                            <p class="text-sm line-clamp-2"><strong>Tujuan:</strong> ${ambulance.dispatch.destination ?? '-'}</p>
                        ` : '<p class="text-sm text-gray-500 mt-2">Tidak ada dispatch aktif</p>'}
                        ${ambulance.last_update ? `<p class="text-[10px] text-gray-400 mt-2 border-t pt-1">Update: ${ambulance.last_update}</p>` : ''}
                    </div>
                `;

                if (markers[ambulance.id]) {
                    // Update existing marker
                    markers[ambulance.id].setLatLng([ambulance.latitude, ambulance.longitude]);
                    markers[ambulance.id].setPopupContent(popupContent);
                } else {
                    // Create new marker
                    markers[ambulance.id] = L.marker([ambulance.latitude, ambulance.longitude])
                        .addTo(map)
                        .bindPopup(popupContent);
                }
            });
        })
        .catch(error => console.error('Error fetching ambulances:', error));
}

// Initial load
updateAmbulances();

// Auto-refresh every 10 seconds
let countdown = 10;
setInterval(() => {
    countdown--;
    document.getElementById('refresh-timer').textContent = countdown;
    
    if (countdown === 0) {
        updateAmbulances();
        countdown = 10;
    }
}, 1000);
</script>
@endsection
