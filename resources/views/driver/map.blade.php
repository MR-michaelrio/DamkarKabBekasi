@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6">

    <!-- HEADER -->
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-gray-800">
            📍 Realtime Lokasi Armada
        </h1>
        <p class="text-sm text-gray-500">
            Posisi armada diperbarui secara realtime melalui browser driver
        </p>
    </div>

    <!-- MAP -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div id="map" class="w-full h-[500px]"></div>
    </div>

</div>

<!-- LEAFLET MAP -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Default map (Jakarta sebagai fallback)
    const map = L.map('map').setView([-6.200000, 106.816666], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Marker ambulance
    let ambulanceMarker = null;

    function updateAmbulancePosition(lat, lng) {
        if (!ambulanceMarker) {
            ambulanceMarker = L.marker([lat, lng]).addTo(map);
        } else {
            ambulanceMarker.setLatLng([lat, lng]);
        }
        map.setView([lat, lng], 15);
    }

    // Ambil lokasi dari browser driver
    if ("geolocation" in navigator) {
        navigator.geolocation.watchPosition(
            function (position) {
                updateAmbulancePosition(
                    position.coords.latitude,
                    position.coords.longitude
                );
            },
            function (error) {
                console.error("GPS Error:", error.message);
            },
            {
                enableHighAccuracy: true,
                maximumAge: 5000,
                timeout: 10000
            }
        );
    } else {
        alert("Browser tidak mendukung GPS");
    }
</script>
@endsection