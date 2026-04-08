<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Unit | Damkar Kabupaten Bekasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

    <div class="max-w-xl mx-auto px-4 py-6">

        <!-- Header -->
        <div class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        🚒 Dashboard Unit Damkar
                    </h2>
                    <p class="text-sm text-gray-500">{{ auth('ambulance')->user()->plate_number }} ({{
                        auth('ambulance')->user()->username }})</p>
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

            <div class="space-y-4 text-sm">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span
                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Pelapor</span>
                        <span class="font-bold text-gray-800">{{ $activeDispatch->patient_name }}</span>
                    </div>
                    <div>
                        <span
                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Layanan</span>
                        <span class="font-bold text-gray-800">
                            @if($activeDispatch->event_request_id)
                            🎪 Event/Bencana
                            @else
                            {{ $activeDispatch->patient_condition === 'jenazah' ? '⚰️ Jenazah' : '🚒 Penanganan' }}
                            @endif
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-3">
                    <div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Jam
                            Jemput</span>
                        <span class="font-bold text-blue-600">
                            {{ $activeDispatch->pickup_time ?
                            \Carbon\Carbon::parse($activeDispatch->pickup_time)->format('H:i') : '-' }} WIB
                        </span>
                    </div>
                    <div>
                        <span
                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Tipe</span>
                        <span class="font-bold text-gray-800">
                            {{ $activeDispatch->trip_type === 'round_trip' ? '🔄 Balik' : '➡️ 1 Way' }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-50 pt-3">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Alamat
                        Jemput</span>
                    <p class="font-medium text-gray-800 leading-snug">{{ $activeDispatch->pickup_address }}</p>
                </div>

                <div class="border-t border-gray-50 pt-3">
                    <span
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Tujuan</span>
                    <p class="font-medium text-gray-800 leading-snug">{{ $activeDispatch->destination }}</p>
                </div>

                <div>
                    <span
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-1">Status</span>
                    <span
                        class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-[10px] font-black uppercase tracking-wider">
                        {{ str_replace('_', ' ', $activeDispatch->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- GPS Tracking & Journey Control -->
        <div class="bg-white rounded-lg shadow p-4 mb-4 relative overflow-hidden">
            @if($activeDispatch->is_paused)
            <div class="absolute inset-0 bg-yellow-50/80 backdrop-blur-[1px] flex items-center justify-center z-10">
                <div
                    class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-full font-bold shadow-sm border border-yellow-200 animate-pulse">
                    ⏸️ SEDANG ISTIRAHAT
                </div>
            </div>
            @endif

            <div class="flex justify-between items-center mb-3 relative z-20">
                <h2 class="font-bold text-lg">📋 Journey Control</h2>
                <button id="pause-btn" data-paused="{{ $activeDispatch->is_paused ? 'true' : 'false' }}"
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
                    <div id="status-indicator"
                        class="w-3 h-3 {{ $activeDispatch->is_paused ? 'bg-yellow-400' : 'bg-gray-400' }} rounded-full">
                    </div>
                    <span id="status-text" class="text-sm text-gray-600 font-medium">
                        {{ $activeDispatch->is_paused ? 'Tracking Dihentikan Sejenak' : 'Tracking belum dimulai' }}
                    </span>
                </div>
                <p id="last-update" class="text-xs text-gray-500 mt-1"></p>
            </div>

            @php
            $statusConfig = [
            'pending' => [
            'label' => '🚀 OTW ke TKP',
            'color' => 'bg-green-600 hover:bg-green-700',
            ],
            'on_the_way_scene' => [
            'label' => '📍 Sampai di TKP',
            'color' => 'bg-blue-600 hover:bg-blue-700',
            ],
            'on_scene' => [
            'label' => '🚒 OTW MAKO / POS',
            'color' => 'bg-orange-600 hover:bg-orange-700',
            ],
            'on_the_way_kantor_pos' => [
            'label' => '🏁 Selesai',
            'color' => 'bg-red-600 hover:bg-red-700',
            ],
            ];
            $currentConfig = $statusConfig[$activeDispatch->status] ?? null;
            @endphp

            @if($currentConfig)
            <button id="journey-btn" data-status="{{ $activeDispatch->status }}" {{ $activeDispatch->is_paused ?
                'disabled' : '' }}
                class="w-full {{ $currentConfig['color'] }} text-white font-bold py-4 px-6 rounded-xl shadow-lg
                transition duration-200 transform active:scale-95 flex items-center justify-center gap-2 {{
                $activeDispatch->is_paused ? 'opacity-50 grayscale cursor-not-allowed' : '' }}">
                {{ $currentConfig['label'] }}
            </button>
            @endif
        </div>

        @if($activeDispatch->trip_type === 'round_trip' && $activeDispatch->return_address)
        <div class="px-6 pb-6 pt-2 border-t border-gray-50">
            <div class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div class="w-2 h-2 bg-blue-500 rounded-full my-4"></div>
                    <div class="w-0.5 h-full bg-blue-100 mb-4"></div>
                    <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                </div>
                <div class="flex-1 space-y-4 pt-2">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Tujuan Utama</p>
                        <p class="text-sm font-bold text-gray-700 leading-tight">
                            {{ $activeDispatch->destination }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Alamat Pulang</p>
                        <p class="text-sm font-bold text-gray-700 leading-tight">
                            {{ $activeDispatch->return_address }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Location Info -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <h3 class="font-semibold mb-2">Lokasi Saat Ini</h3>
            <p id="current-location" class="text-sm text-gray-600">Menunggu GPS...</p>
        </div>

        <!-- Activity Photo Report Section -->
        <div class="bg-white rounded-lg shadow p-4 mb-4">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-2xl">📸</span>
                <h2 class="font-bold text-lg">Laporan Foto Kegiatan</h2>
            </div>
            
            <!-- Activity Photo Uploader -->
            <div id="photo-uploader-container">
                <!-- Action Buttons -->
                <div class="flex gap-2 mb-4">
                    <button id="camera-btn" type="button" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        📷 Ambil Foto
                    </button>
                    <button id="gallery-btn" type="button" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        🖼️ Pilih dari Galeri
                    </button>
                </div>
                
                <!-- File Input (Hidden) -->
                <input type="file" id="photo-input" accept="image/*" multiple hidden>
                <input type="file" id="camera-input" accept="image/*" capture="environment" hidden>
                
                <!-- Photo Preview Area -->
                <div id="photo-list" class="space-y-2"></div>
                
                <!-- Upload Status -->
                <div id="upload-status" class="mt-3 text-sm text-gray-600"></div>
            </div>
        </div>

        @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">
                📭
            </div>
            <h3 class="font-bold text-gray-800 text-lg">Tidak Ada Penugasan Aktif</h3>
            <p class="text-gray-500 text-sm mt-1 mb-6">Unit damkar Anda sedang tidak dalam tugas. Silakan cek laporan
                masyarakat yang masuk.</p>

            <a href="{{ route('driver.dispatching') }}"
                class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition transform active:scale-95">
                📋 Lihat Laporan Masyarakat
            </a>
        </div>
        @endif

        <!-- Menu Section (Optional extra shortcut) -->
        <div class="mt-6 grid grid-cols-2 gap-4">
            <a href="{{ route('driver.dispatching') }}"
                class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center gap-2 text-center transition hover:bg-gray-50 active:scale-95">
                <span class="text-2xl">📋</span>
                <span class="text-xs font-bold text-gray-700">Laporan Masuk</span>
            </a>
            <button onclick="window.location.reload()"
                class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center gap-2 text-center transition hover:bg-gray-50 active:scale-95">
                <span class="text-2xl">🔄</span>
                <span class="text-xs font-bold text-gray-700">Refresh</span>
            </button>
        </div>

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
        const { PushNotifications, TextToSpeech } = CapacitorPlugins;

        // HTTPS / Secure Context Check
        if (!window.isSecureContext && !isCapacitor && window.location.hostname !== 'localhost') {
            statusIndicator.className = 'w-3 h-3 bg-red-500 rounded-full animate-ping';
            statusText.innerHTML = '<span class="text-red-600 font-bold">⚠️ ERROR: Browser mewajibkan HTTPS untuk GPS.</span> Hubungi Admin untuk setup SSL.';
            console.error('Geolocation requires a secure context (HTTPS)');
        }

        async function initializeCapacitorTracking() {
            if (!isCapacitor) return;

            if (CapacitorPlugins.BackgroundGeolocation) {
                try {
                    await CapacitorPlugins.BackgroundGeolocation.requestPermissions();
                } catch (e) {
                    console.error('Failed to request Geolocation permissions:', e);
                }
            }

            if (PushNotifications) {
                try {
                // Create Notification Channel for Sound
                if (PushNotifications.createChannel) {
                    await PushNotifications.createChannel({
                        id: 'damkar-emergency',
                        name: 'Damkar Emergency',
                        description: 'Notifications with emergency sound',
                        importance: 5,
                        visibility: 1,
                        sound: 'emergency' 
                    });
                }

                    let permStatus = await PushNotifications.checkPermissions();
                    if (permStatus.receive === 'prompt') {
                        permStatus = await PushNotifications.requestPermissions();
                    }
                    if (permStatus.receive !== 'granted') {
                        console.warn('Push registration failed: permission denied');
                    } else {
                        PushNotifications.addListener('registration', (token) => {
                            console.log('Push registration success, token: ' + token.value);
                            fetch('/driver/fcm-token', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ token: token.value })
                            }).catch(err => console.error('Failed to save FCM token:', err));
                        });

                        PushNotifications.addListener('registrationError', (error) => {
                            console.error('Push registration error: ', JSON.stringify(error));
                        });

                        PushNotifications.addListener('pushNotificationReceived', (notification) => {
                            console.log('Push received: ', notification);

                            // Audio is now handled by Native Java code (FCMService.java)
                            // for both background and foreground to ensure reliability.
                            
                            const title = notification.data.title || notification.title || "Notifikasi Baru";
                            const body = notification.data.body || notification.body || "";
                            
                            alert("Notifikasi Baru:\n" + title + "\n" + body);
                        });

                        await PushNotifications.register();
                    }
                } catch (e) {
                    console.error('Failed to initialize push notifications:', e);
                }
            }
        }

        if (isCapacitor) {
            statusText.textContent = 'Mobile App Mode: Ready';
            initializeCapacitorTracking();
        }

        const pauseBtn = document.getElementById('pause-btn');

        pauseBtn?.addEventListener('click', async function () {
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

        journeyBtn?.addEventListener('click', async function () {
            const currentStatus = this.getAttribute('data-status');

            // UI Loading state
            this.disabled = true;
            this.classList.add('opacity-75', 'cursor-not-allowed');
            const originalContent = this.innerHTML;
            this.innerHTML = `<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...`;

            try {
                // Special actions based on status
                if (currentStatus === 'pending') {
                    await startTracking();
                } else if (currentStatus === 'on_the_way_scene') {
                    await stopTracking(); // Arrived at scene, stop tracking while working
                } else if (currentStatus === 'on_scene') {
                    await startTracking(); // Start journey back to station
                } else if (currentStatus === 'on_the_way_kantor_pos') {
                    await stopTracking(); // Back at station
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
                            backgroundMessage: "Damkar sedang melacak lokasi unit...",
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
        const autoStartStatuses = ['on_the_way_scene', 'on_the_way_kantor_pos'];
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

    <!-- Photo Upload Handler with Camera Support -->
    <script>
        const activityId = {{ $activityLog->id ?? 'null' }};
        let uploadedPhotos = [];
        
        document.addEventListener('DOMContentLoaded', async function() {
            if (!activityId) {
                console.error('Activity ID not found');
                return;
            }
            
            const cameraBtn = document.getElementById('camera-btn');
            const galleryBtn = document.getElementById('gallery-btn');
            const photoInput = document.getElementById('photo-input');
            const cameraInput = document.getElementById('camera-input');
            const photoList = document.getElementById('photo-list');
            const uploadStatus = document.getElementById('upload-status');
            
            const isCapacitorApp = window.hasOwnProperty('Capacitor') && window.Capacitor.hasOwnProperty('Plugins');
            
            // Camera button
            cameraBtn?.addEventListener('click', async () => {
                if (isCapacitorApp) {
                    try {
                        await captureWithCapacitor();
                    } catch (err) {
                        console.error('Capacitor error:', err);
                        // Fallback to native input
                        cameraInput.click();
                    }
                } else {
                    // Web fallback
                    cameraInput.click();
                }
            });
            
            // Gallery button
            galleryBtn?.addEventListener('click', () => {
                photoInput.click();
            });
            
            // Handle photo input
            photoInput?.addEventListener('change', (e) => {
                handleFiles(e.target.files);
                e.target.value = ''; // Reset
            });
            
            // Handle camera input
            cameraInput?.addEventListener('change', (e) => {
                handleFiles(e.target.files);
                e.target.value = ''; // Reset
            });
            
            // Capacitor camera capture
            async function captureWithCapacitor() {
                const { Camera } = window.Capacitor.Plugins;
                const image = await Camera.getPhoto({
                    quality: 90,
                    allowEditing: false,
                    resultType: 'base64',
                    source: 'CAMERA'
                });
                
                // Convert base64 to blob and upload
                const response = await fetch(`data:${image.format};base64,${image.base64String}`);
                const blob = await response.blob();
                const file = new File([blob], `photo_${Date.now()}.jpg`, { type: 'image/jpeg' });
                
                await uploadPhoto(file);
            }
            
            // Handle files
            async function handleFiles(files) {
                if (!files || !files.length) return;
                
                const maxPhotos = 5;
                const currentCount = uploadedPhotos.length;
                
                if (currentCount >= maxPhotos) {
                    updateStatus('❌ Sudah mencapai maksimum 5 foto', 'error');
                    return;
                }
                
                let allowedCount = Math.min(files.length, maxPhotos - currentCount);
                updateStatus(`⏳ Mengunggah ${allowedCount} foto...`, 'loading');
                
                for (let i = 0; i < allowedCount; i++) {
                    const file = files[i];
                    if (file.type.startsWith('image/')) {
                        await uploadPhoto(file);
                    }
                }
            }
            
            // Upload photo
            async function uploadPhoto(file) {
                try {
                    const fd = new FormData();
                    fd.append('file', file);
                    fd.append('sequence', uploadedPhotos.length);
                    fd.append('description', `Foto aktivitas - ${new Date().toLocaleTimeString()}`);
                    
                    const response = await fetch(`/api/activity-photos/${activityId}/upload`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: fd
                    });
                    
                    // Check response status FIRST before trying to parse JSON
                    if (!response.ok) {
                        const contentType = response.headers.get('content-type');
                        let errorMsg = `Server error: ${response.status}`;
                        
                        if (contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            errorMsg = data.message || errorMsg;
                        } else {
                            const text = await response.text();
                            console.error('Non-JSON response:', text.substring(0, 200));
                            errorMsg = `Server error (${response.status}): Check console`;
                        }
                        throw new Error(errorMsg);
                    }
                    
                    // Parse successful response
                    const data = await response.json();
                    
                    if (data.success) {
                        uploadedPhotos.push(data.photo);
                        displayPhoto(data.photo);
                        updateStatus(`✅ Foto berhasil diunggah (${uploadedPhotos.length}/5)`, 'success');
                        
                        // Auto-clear success message after 3 seconds
                        setTimeout(() => {
                            if (uploadStatus.textContent.includes('✅')) {
                                updateStatus('', 'normal');
                            }
                        }, 3000);
                    } else {
                        throw new Error(data.message || 'Upload gagal');
                    }
                    
                } catch (error) {
                    console.error('Upload error:', error);
                    updateStatus(`❌ Gagal: ${error.message}`, 'error');
                }
            }
            
            // Display photo
            function displayPhoto(photo) {
                const photoId = photo.id;
                const photoUrl = photo.photo_url || `/storage/${photo.storage_path}`;
                
                const div = document.createElement('div');
                div.className = 'flex items-center gap-2 p-2 bg-gray-50 rounded-lg';
                div.innerHTML = `
                    <img src="${photoUrl}" alt="Photo" class="w-12 h-12 rounded object-cover">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-700">${photo.photo_name || 'Foto aktivitas'}</p>
                        <p class="text-xs text-gray-500">${new Date().toLocaleTimeString()}</p>
                    </div>
                    <button type="button" class="text-red-600 hover:text-red-800 font-bold px-2 py-1 text-sm" onclick="deletePhoto(${photoId})">
                        🗑️ Hapus
                    </button>
                `;
                photoList.appendChild(div);
            }
            
            // Update status message
            function updateStatus(message, type = 'normal') {
                uploadStatus.textContent = message;
                uploadStatus.className = 'mt-3 text-sm ';
                if (type === 'error') {
                    uploadStatus.className += 'text-red-600 font-semibold';
                } else if (type === 'success') {
                    uploadStatus.className += 'text-green-600 font-semibold';
                } else if (type === 'loading') {
                    uploadStatus.className += 'text-blue-600 font-semibold';
                } else {
                    uploadStatus.className += 'text-gray-600';
                }
            }
            
            // Load existing photos
            await loadPhotos();
        });
        
        // Load photos from server
        async function loadPhotos() {
            try {
                const response = await fetch(`/api/activity-photos/${activityId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        throw new Error(data.message || `Load failed: ${response.status}`);
                    } else {
                        throw new Error(`Server error: ${response.status}`);
                    }
                }
                
                const data = await response.json();
                if (data.success && data.photos) {
                    uploadedPhotos = data.photos;
                    const photoList = document.getElementById('photo-list');
                    photoList.innerHTML = '';
                    
                    data.photos.forEach(photo => {
                        const photoUrl = photo.photo_url || `/storage/${photo.storage_path || photo.photo_path}`;
                        const div = document.createElement('div');
                        div.className = 'flex items-center gap-2 p-2 bg-gray-50 rounded-lg';
                        div.innerHTML = `
                            <img src="${photoUrl}" alt="Photo" class="w-12 h-12 rounded object-cover">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-700">${photo.photo_name || 'Foto aktivitas'}</p>
                                <p class="text-xs text-gray-500">${new Date(photo.created_at).toLocaleTimeString()}</p>
                            </div>
                            <button type="button" class="text-red-600 hover:text-red-800 font-bold px-2 py-1 text-sm" onclick="deletePhoto(${photo.id})">
                                🗑️ Hapus
                            </button>
                        `;
                        photoList.appendChild(div);
                    });
                }
            } catch (err) {
                console.error('Failed to load photos:', err);
            }
        }
        
        // Delete photo
        async function deletePhoto(photoId) {
            if (!confirm('Hapus foto ini?')) return;
            
            try {
                const response = await fetch(`/api/activity-photos/${photoId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                
                if (!response.ok) {
                    const contentType = response.headers.get('content-type');
                    let errorMsg = `Delete failed: ${response.status}`;
                    if (contentType && contentType.includes('application/json')) {
                        const data = await response.json();
                        errorMsg = data.message || errorMsg;
                    }
                    throw new Error(errorMsg);
                }
                
                const data = await response.json();
                if (data.success) {
                    uploadedPhotos = uploadedPhotos.filter(p => p.id !== photoId);
                    location.reload(); // Reload to refresh photo list
                } else {
                    alert('❌ ' + (data.message || 'Gagal menghapus foto'));
                }
            } catch (err) {
                console.error('Delete error:', err);
                alert('❌ Gagal menghapus foto: ' + err.message);
            }
        }
    </script>

</body>

</html>