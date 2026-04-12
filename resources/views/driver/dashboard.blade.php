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
            'label' => '✅ Laporan Telah Ditangani',
            'color' => 'bg-emerald-600 hover:bg-emerald-700',
            ],
            'handled' => [
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
            @if($activeDispatch->status === 'handled')
            <div class="flex gap-3">
                <button id="journey-btn" data-status="handled" {{ $activeDispatch->is_paused ? 'disabled' : '' }}
                    class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition duration-200 transform active:scale-95 flex items-center justify-center gap-2">
                    🏠 Kembali ke MAKO
                </button>
                <a href="{{ route('driver.dispatching') }}"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition duration-200 transform active:scale-95 flex items-center justify-center gap-2">
                    📋 Ambil Request Lain
                </a>
            </div>
            @else
            <button id="journey-btn" data-status="{{ $activeDispatch->status }}" {{ $activeDispatch->is_paused ?
                'disabled' : '' }}
                class="w-full {{ $currentConfig['color'] }} text-white font-bold py-4 px-6 rounded-xl shadow-lg
                transition duration-200 transform active:scale-95 flex items-center justify-center gap-2 {{
                $activeDispatch->is_paused ? 'opacity-50 grayscale cursor-not-allowed' : '' }}">
                {{ $currentConfig['label'] }}
            </button>
            @endif
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

            <div id="photo-uploader-container">
                <!-- Action Buttons -->
                <div class="flex gap-2 mb-4">
                    <button id="camera-btn" type="button"
                        class="flex-1 bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        📷 Ambil Foto
                    </button>
                    <button id="gallery-btn" type="button"
                        class="flex-1 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white font-bold py-3 px-4 rounded-lg transition flex items-center justify-center gap-2">
                        🖼️ Dari Galeri
                    </button>
                </div>

                <!-- Hidden File Inputs -->
                <!-- camera: pakai kamera belakang langsung -->
                <input type="file" id="camera-input" accept="image/*" capture="environment" hidden>
                <!-- gallery: pilih dari galeri, tanpa capture -->
                <input type="file" id="gallery-input" accept="image/*" hidden>

                <!-- Pending Photo Preview (muncul setelah pilih foto, sebelum upload) -->
                <div id="pending-photo-section" class="hidden bg-gray-50 rounded-xl p-3 mb-4 border border-gray-200">
                    <div class="flex gap-3 items-start">
                        <!-- Thumbnail Preview -->
                        <div class="flex-shrink-0">
                            <img id="preview-thumbnail" src="" alt="Preview"
                                class="w-20 h-20 rounded-lg object-cover border-2 border-gray-300 shadow-sm">
                        </div>
                        <div class="flex-1 min-w-0">
                            <!-- Ukuran file -->
                            <div class="text-xs text-gray-500 mb-0.5">
                                <span class="font-semibold text-gray-700">Ukuran asli:</span>
                                <span id="original-size" class="ml-1">-</span>
                            </div>
                            <div class="text-xs text-gray-500 mb-2">
                                <span class="font-semibold text-gray-700">Setelah kompresi:</span>
                                <span id="compressed-size" class="ml-1">-</span>
                            </div>
                            <!-- Status Badge -->
                            <span id="status-badge" class="hidden items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold"></span>
                        </div>
                    </div>

                    <!-- Progress Bar Kompresi -->
                    <div id="progress-container" class="hidden mt-3">
                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div id="progress-bar"
                                class="bg-yellow-400 h-2 rounded-full transition-all duration-200"
                                style="width: 0%"></div>
                        </div>
                        <p class="text-xs text-yellow-700 mt-1 font-medium">Mengompres foto...</p>
                    </div>

                    <!-- Tombol Upload (aktif hanya setelah kompresi selesai) -->
                    <button id="upload-btn" type="button" disabled
                        class="mt-3 w-full bg-gray-300 text-gray-400 font-bold py-2.5 px-4 rounded-lg transition cursor-not-allowed flex items-center justify-center gap-2 text-sm">
                        ⬆️ Upload Foto
                    </button>
                </div>

                <!-- Daftar Foto yang Sudah Diunggah -->
                <div id="photo-list" class="space-y-2"></div>

                <!-- Status Notifikasi Upload -->
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
                } else if (currentStatus === 'on_scene') {
                    await stopTracking(); // Working on scene
                } else if (currentStatus === 'handled') {
                    await startTracking(); // Start journey after handled
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

    <!-- Photo Upload Handler with Camera Support + Client-Side Compression -->
    <script>
        const activityId = {{ $activityLog->id ?? 'null' }};
        let uploadedPhotos = [];

        // ── Helpers ────────────────────────────────────────────────────────────

        function formatBytes(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
        }

        function csrfToken() {
            return document.querySelector('meta[name="csrf-token"]').content;
        }

        function updateStatus(message, type = 'normal') {
            const el = document.getElementById('upload-status');
            el.textContent = message;
            el.className = 'mt-3 text-sm font-semibold';
            const map = { error: 'text-red-600', success: 'text-green-600', loading: 'text-blue-600', normal: 'text-gray-600' };
            el.classList.add(map[type] ?? 'text-gray-600');
        }

        function setStatusBadge(state) {
            const badge = document.getElementById('status-badge');
            badge.className = 'inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-bold';
            if (state === 'compressing') {
                badge.classList.add('bg-yellow-100', 'text-yellow-700');
                badge.textContent = '⏳ Mengompres...';
            } else if (state === 'ready') {
                badge.classList.add('bg-green-100', 'text-green-700');
                badge.textContent = '✅ Siap upload';
            } else if (state === 'failed') {
                badge.classList.add('bg-red-100', 'text-red-700');
                badge.textContent = '❌ Gagal';
            }
            badge.classList.remove('hidden');
        }

        function setProgress(percent) {
            const container = document.getElementById('progress-container');
            const bar = document.getElementById('progress-bar');
            container.classList.toggle('hidden', percent === null);
            if (percent !== null) bar.style.width = percent + '%';
        }

        // ── Client-Side Compression (Canvas API) ───────────────────────────────

        /**
         * Kompres gambar ke ≤ 100 KB menggunakan Canvas.
         * - Resize ke max 1280×1280 (jaga aspect ratio)
         * - Mulai kualitas 0.9, turun 0.05 tiap iterasi
         * @param {File} file
         * @returns {Promise<{blob: Blob, dataUrl: string, originalSize: number, compressedSize: number}>}
         */
        async function compressImage(file) {
            const MAX_SIDE  = 1280;
            const MAX_BYTES = 100 * 1024; // 100 KB
            const MIN_QUALITY = 0.1;

            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onerror = () => reject(new Error('Gagal membaca file'));
                reader.onload = (e) => {
                    const img = new Image();
                    img.onerror = () => reject(new Error('Gagal memuat gambar'));
                    img.onload = () => {
                        // Hitung dimensi baru
                        let { width, height } = img;
                        if (width > MAX_SIDE || height > MAX_SIDE) {
                            const ratio = Math.min(MAX_SIDE / width, MAX_SIDE / height);
                            width  = Math.round(width  * ratio);
                            height = Math.round(height * ratio);
                        }

                        const canvas  = document.createElement('canvas');
                        canvas.width  = width;
                        canvas.height = height;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        const originalSize = file.size;
                        let quality = 0.9;
                        let blob, dataUrl;

                        // Progress step: 20% → 90% selama loop kompresi
                        let step = 0;
                        const maxSteps = Math.ceil((quality - MIN_QUALITY) / 0.05) + 1;

                        const tryCompress = () => {
                            dataUrl = canvas.toDataURL('image/jpeg', quality);

                            // Estimasi ukuran dari data URL (base64 → bytes)
                            const base64 = dataUrl.split(',')[1];
                            const byteLen = Math.ceil((base64.length * 3) / 4);

                            // Update progress bar
                            const pct = 20 + Math.round((step / maxSteps) * 70);
                            setProgress(Math.min(pct, 90));
                            step++;

                            if (byteLen <= MAX_BYTES || quality <= MIN_QUALITY) {
                                // Konversi ke Blob
                                canvas.toBlob((b) => {
                                    if (!b) { reject(new Error('Gagal konversi canvas ke Blob')); return; }
                                    resolve({ blob: b, dataUrl, originalSize, compressedSize: b.size });
                                }, 'image/jpeg', quality);
                            } else {
                                quality = Math.max(MIN_QUALITY, quality - 0.05);
                                // Jalankan iterasi berikutnya di microtask agar UI bisa update
                                setTimeout(tryCompress, 0);
                            }
                        };

                        setProgress(20);
                        setTimeout(tryCompress, 0);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            });
        }

        // ── State yang menyimpan blob hasil kompresi (pending upload) ───────────
        let pendingBlob = null;

        async function handleSelectedFile(file) {
            if (!file || !file.type.startsWith('image/')) return;

            if (uploadedPhotos.length >= 5) {
                updateStatus('❌ Sudah mencapai maksimum 5 foto', 'error');
                return;
            }

            // Tampilkan section pending
            const section = document.getElementById('pending-photo-section');
            section.classList.remove('hidden');

            // Reset state
            pendingBlob = null;
            document.getElementById('upload-btn').disabled = true;
            document.getElementById('upload-btn').className =
                'mt-3 w-full bg-gray-300 text-gray-400 font-bold py-2.5 px-4 rounded-lg transition cursor-not-allowed flex items-center justify-center gap-2 text-sm';
            document.getElementById('original-size').textContent = formatBytes(file.size);
            document.getElementById('compressed-size').textContent = '-';
            document.getElementById('preview-thumbnail').src = '';
            setProgress(5);
            setStatusBadge('compressing');
            updateStatus('', 'normal');

            try {
                const result = await compressImage(file);
                pendingBlob = result.blob;

                // Preview thumbnail
                document.getElementById('preview-thumbnail').src = result.dataUrl;
                document.getElementById('compressed-size').textContent = formatBytes(result.compressedSize);
                setProgress(100);

                // Setelah sebentar, sembunyikan progress bar & tampilkan badge siap
                setTimeout(() => {
                    setProgress(null);
                    setStatusBadge('ready');
                    // Aktifkan tombol upload
                    const btn = document.getElementById('upload-btn');
                    btn.disabled = false;
                    btn.className =
                        'mt-3 w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm';
                }, 400);

            } catch (err) {
                console.error('Compression error:', err);
                setProgress(null);
                setStatusBadge('failed');
                updateStatus('❌ Gagal mengompres: ' + err.message, 'error');
            }
        }

        // ── Upload blob hasil kompresi ke server ───────────────────────────────

        async function uploadCompressedPhoto() {
            if (!pendingBlob) return;

            const btn = document.getElementById('upload-btn');
            btn.disabled = true;
            btn.className =
                'mt-3 w-full bg-gray-400 text-white font-bold py-2.5 px-4 rounded-lg transition cursor-not-allowed flex items-center justify-center gap-2 text-sm';
            btn.textContent = '⏳ Mengunggah...';
            updateStatus('⏳ Mengunggah...', 'loading');

            try {
                const shortName = (Math.random().toString(36).substring(2, 7)).toUpperCase() + '.jpg';
                const file = new File([pendingBlob], shortName, { type: 'image/jpeg' });

                const fd = new FormData();
                fd.append('file', file);
                fd.append('sequence', uploadedPhotos.length);
                fd.append('description', `Foto aktivitas - ${new Date().toLocaleTimeString()}`);

                const response = await fetch(`/api/activity-photos/${activityId}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json'
                    },
                    body: fd
                });

                if (!response.ok) {
                    const ct = response.headers.get('content-type') || '';
                    let msg = `Server error: ${response.status}`;
                    if (ct.includes('application/json')) {
                        const d = await response.json();
                        msg = d.message || msg;
                    } else {
                        const txt = await response.text();
                        console.error('Non-JSON response:', txt.substring(0, 300));
                        msg = `Server error (${response.status}): lihat console`;
                    }
                    throw new Error(msg);
                }

                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Upload gagal');

                // Sukses
                uploadedPhotos.push(data.photo);
                displayUploadedPhoto(data.photo);
                pendingBlob = null;

                document.getElementById('pending-photo-section').classList.add('hidden');
                updateStatus(`✅ Foto berhasil diunggah (${uploadedPhotos.length}/5)`, 'success');
                setTimeout(() => {
                    const el = document.getElementById('upload-status');
                    if (el.textContent.includes('✅')) updateStatus('', 'normal');
                }, 3000);

            } catch (err) {
                console.error('Upload error:', err);
                setStatusBadge('failed');
                btn.textContent = '⬆️ Upload Foto';
                btn.disabled = false;
                btn.className =
                    'mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg transition flex items-center justify-center gap-2 text-sm';
                updateStatus('❌ Gagal upload: ' + err.message, 'error');
            }
        }

        // ── Render foto yang sudah diunggah ─────────────────────────────────────

        function displayUploadedPhoto(photo) {
            const photoId  = photo.id;
            const photoUrl = photo.photo_url || `/storage/${photo.storage_path}`;
            const list     = document.getElementById('photo-list');
            const div      = document.createElement('div');
            div.id         = `photo-item-${photoId}`;
            div.className  = 'flex items-center gap-3 p-2 bg-gray-50 rounded-xl border border-gray-100';
            div.innerHTML  = `
                <img src="${photoUrl}" alt="Foto" class="w-14 h-14 rounded-lg object-cover flex-shrink-0 border border-gray-200">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-700 truncate">${photo.photo_name || 'Foto aktivitas'}</p>
                    <p class="text-xs text-gray-400">${new Date(photo.created_at || Date.now()).toLocaleTimeString('id-ID')}</p>
                </div>
                <button type="button"
                    class="flex-shrink-0 text-red-500 hover:text-red-700 font-bold p-1 rounded"
                    onclick="deletePhoto(${photoId})">
                    🗑️
                </button>
            `;
            list.appendChild(div);
        }

        // ── Load foto dari server ───────────────────────────────────────────────

        async function loadPhotos() {
            if (!activityId) return;
            try {
                const response = await fetch(`/api/activity-photos/${activityId}`, {
                    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error(`Load failed: ${response.status}`);
                const data = await response.json();
                if (data.success && data.photos) {
                    uploadedPhotos = data.photos;
                    const list = document.getElementById('photo-list');
                    list.innerHTML = '';
                    data.photos.forEach(displayUploadedPhoto);
                }
            } catch (err) {
                console.error('Failed to load photos:', err);
            }
        }

        // ── Hapus foto ──────────────────────────────────────────────────────────

        async function deletePhoto(photoId) {
            if (!confirm('Hapus foto ini?')) return;
            try {
                const response = await fetch(`/api/activity-photos/${photoId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' }
                });

                if (!response.ok) {
                    const ct = response.headers.get('content-type') || '';
                    let msg = `Delete failed: ${response.status}`;
                    if (ct.includes('application/json')) {
                        const d = await response.json();
                        msg = d.message || msg;
                    }
                    throw new Error(msg);
                }

                const data = await response.json();
                if (data.success) {
                    uploadedPhotos = uploadedPhotos.filter(p => p.id !== photoId);
                    const item = document.getElementById(`photo-item-${photoId}`);
                    if (item) item.remove();
                    updateStatus(`✅ Foto dihapus (${uploadedPhotos.length}/5)`, 'success');
                    setTimeout(() => {
                        const el = document.getElementById('upload-status');
                        if (el.textContent.includes('✅')) updateStatus('', 'normal');
                    }, 3000);
                } else {
                    throw new Error(data.message || 'Gagal menghapus foto');
                }
            } catch (err) {
                console.error('Delete error:', err);
                alert('❌ Gagal menghapus foto:\n' + err.message);
            }
        }

        // ── DOMContentLoaded: pasang event listener ─────────────────────────────

        document.addEventListener('DOMContentLoaded', async function () {
            if (!activityId) {
                console.error('Activity ID not found');
                return;
            }

            const cameraInput  = document.getElementById('camera-input');
            const galleryInput = document.getElementById('gallery-input');
            const cameraBtn    = document.getElementById('camera-btn');
            const galleryBtn   = document.getElementById('gallery-btn');
            const uploadBtn    = document.getElementById('upload-btn');

            const isCapacitorApp = window.hasOwnProperty('Capacitor') &&
                                   window.Capacitor.hasOwnProperty('Plugins');

            // Tombol kamera
            cameraBtn?.addEventListener('click', async () => {
                if (isCapacitorApp && window.Capacitor.Plugins.Camera) {
                    try {
                        const { Camera } = window.Capacitor.Plugins;
                        const image = await Camera.getPhoto({
                            quality: 90,
                            allowEditing: false,
                            resultType: 'base64',
                            source: 'CAMERA'
                        });
                        const res  = await fetch(`data:${image.format};base64,${image.base64String}`);
                        const blob = await res.blob();
                        const file = new File([blob], 'capture.jpg', { type: 'image/jpeg' });
                        await handleSelectedFile(file);
                    } catch (err) {
                        console.error('Capacitor Camera error:', err);
                        cameraInput.click(); // fallback
                    }
                } else {
                    cameraInput.click();
                }
            });

            // Tombol galeri
            galleryBtn?.addEventListener('click', () => galleryInput.click());

            // Input kamera (web fallback)
            cameraInput?.addEventListener('change', async (e) => {
                if (e.target.files[0]) await handleSelectedFile(e.target.files[0]);
                e.target.value = '';
            });

            // Input galeri
            galleryInput?.addEventListener('change', async (e) => {
                if (e.target.files[0]) await handleSelectedFile(e.target.files[0]);
                e.target.value = '';
            });

            // Tombol upload
            uploadBtn?.addEventListener('click', uploadCompressedPhoto);

            // Muat foto yang sudah ada
            await loadPhotos();
        });
    </script>

    <!-- Completion Dialog Modal -->
    <div id="completion-modal" class="hidden"></div>

    <!-- Request History Modal (shown after return to base) -->
    <div id="request-history-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-sm w-full max-h-[80vh] overflow-y-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white sticky top-0">
                <div class="text-3xl text-center mb-2">📊</div>
                <h2 class="text-lg font-bold text-center">Riwayat Penugasan</h2>
                <p class="text-sm text-purple-100 text-center mt-1" id="history-count">Semua tugas selesai</p>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div id="history-list" class="space-y-2">
                    <!-- Will be populated by JavaScript -->
                </div>
            </div>

            <!-- Button -->
            <div class="px-6 pb-6 border-t border-gray-200">
                <button id="close-history-btn" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                    ✓ Selesai
                </button>
            </div>
        </div>
    </div>

    <script>
        // Completion Modal Handlers
        const completionModal = document.getElementById('completion-modal');
        const acceptNextBtn = document.getElementById('accept-next-btn');
        const returnBaseBtn = document.getElementById('return-base-btn');
        const historyModal = document.getElementById('request-history-modal');
        const closeHistoryBtn = document.getElementById('close-history-btn');
        const historyList = document.getElementById('history-list');
        const historyCount = document.getElementById('history-count');

        acceptNextBtn?.addEventListener('click', async function () {
            acceptNextBtn.disabled = true;
            returnBaseBtn.disabled = true;
            acceptNextBtn.innerHTML = '⏳ Memproses...';

            try {
                // Fetch available requests
                const response = await fetch('{{ route("driver.available-requests") }}?limit=5', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.success && data.requests.length > 0) {
                    // Show requests section
                    document.getElementById('requests-section').classList.remove('hidden');
                    
                    // Populate request list
                    const requestsList = document.getElementById('available-requests-list');
                    requestsList.innerHTML = data.requests.map(req => `
                        <button class="quick-accept-btn w-full text-left bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-3 transition" data-id="${req.id}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-gray-600 uppercase">📋 ${req.patient_condition || 'Penanganan'}</p>
                                    <p class="text-sm font-bold text-gray-800">${req.patient_name}</p>
                                    <p class="text-xs text-gray-600 mt-1">📍 ${req.pickup_address}</p>
                                    <p class="text-xs text-blue-600 font-bold mt-1">${req.created_at}</p>
                                </div>
                                <span class="text-lg ml-2">→</span>
                            </div>
                        </button>
                    `).join('');

                    // Change button text
                    acceptNextBtn.innerHTML = '<span>✓ Pilih request di atas</span>';

                    // Attach click handlers to request buttons
                    document.querySelectorAll('.quick-accept-btn').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            e.preventDefault();
                            const patientRequestId = btn.getAttribute('data-id');
                            await acceptRequest(patientRequestId);
                        });
                    });
                } else {
                    alert('⚠️ Tidak ada request yang tersedia saat ini');
                    acceptNextBtn.disabled = false;
                    returnBaseBtn.disabled = false;
                    acceptNextBtn.innerHTML = '<span>📋 Ambil Request Lain</span>';
                }
            } catch (e) {
                console.error('Error:', e);
                alert('❌ Gagal memuat request: ' + e.message);
                acceptNextBtn.disabled = false;
                returnBaseBtn.disabled = false;
                acceptNextBtn.innerHTML = '<span>📋 Ambil Request Lain</span>';
            }
        });

        // Function to accept a specific request
        async function acceptRequest(patientRequestId) {
            const acceptNextBtn = document.getElementById('accept-next-btn');
            const returnBaseBtn = document.getElementById('return-base-btn');
            
            acceptNextBtn.disabled = true;
            returnBaseBtn.disabled = true;
            acceptNextBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

            try {
                const response = await fetch(`/driver/quick-accept-request/${patientRequestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Hide modal and reload dashboard with new request
                    document.getElementById('completion-modal').classList.add('hidden');
                    
                    // Show success message
                    alert(`✅ ${data.message}\n\n${data.dispatch.patient_name}\n${data.dispatch.pickup_address}`);
                    
                    // Reload page to show new active dispatch
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    alert('❌  ' + (data.message || 'Gagal accept request'));
                    acceptNextBtn.disabled = false;
                    returnBaseBtn.disabled = false;
                    acceptNextBtn.innerHTML = '<span>📋 Ambil Request Lain</span>';
                }
            } catch (e) {
                console.error('Accept error:', e);
                alert('❌ Error: ' + e.message);
                acceptNextBtn.disabled = false;
                returnBaseBtn.disabled = false;
                acceptNextBtn.innerHTML = '<span>📋 Ambil Request Lain</span>';
            }
        }

        returnBaseBtn?.addEventListener('click', async function () {
            acceptNextBtn.disabled = true;
            returnBaseBtn.disabled = true;
            returnBaseBtn.innerHTML = '⏳ Memproses...';

            try {
                const response = await fetch('{{ route("driver.return-to-base") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    completionModal.classList.add('hidden');
                    
                    // Show history modal
                    if (data.request_history && data.request_history.length > 0) {
                        historyCount.innerHTML = `Total ${data.total_requests_handled} tugas diselesaikan`;
                        
                        historyList.innerHTML = data.request_history.map((item, index) => `
                            <div class="bg-purple-50 rounded-lg p-3 border-l-4 border-purple-500">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-bold text-gray-800">Request #${item.sequence}</p>
                                        <p class="text-xs text-gray-600">${item.dispatch?.patient_name || 'Unknown'}</p>
                                    </div>
                                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded font-bold">✓</span>
                                </div>
                            </div>
                        `).join('');
                    }
                    
                    historyModal.classList.remove('hidden');
                } else {
                    alert('❌ ' + (data.message || 'Gagal kembali ke MAKO'));
                    acceptNextBtn.disabled = false;
                    returnBaseBtn.disabled = false;
                    returnBaseBtn.innerHTML = '<span>🏠 Kembali ke MAKO</span>';
                }
            } catch (e) {
                console.error('Return to base error:', e);
                alert('❌ Terjadi kesalahan: ' + e.message);
                acceptNextBtn.disabled = false;
                returnBaseBtn.disabled = false;
                returnBaseBtn.innerHTML = '<span>🏠 Kembali ke MAKO</span>';
            }
        });

        closeHistoryBtn?.addEventListener('click', () => {
            historyModal.classList.add('hidden');
            setTimeout(() => {
                window.location.href = '{{ route("driver.dashboard") }}';
            }, 500);
        });

        // Show completion modal when journey button detects completion
        function showCompletionModal(customerName = '') {
            const msgEl = document.getElementById('completion-message');
            if (customerName) {
                msgEl.textContent = `Terima kasih telah melayani: ${customerName}`;
            }
            completionModal.classList.remove('hidden');
        }

        // Override journey button to show modal on completion
        const originalJourneyClick = journeyBtn?.onclick;
        if (journeyBtn) {
            journeyBtn.addEventListener('click', async function (e) {
                const currentStatus = this.getAttribute('data-status');
                if (currentStatus === 'on_the_way_kantor_pos') {
                    // This is the completion action
                    // Let the normal flow continue but intercept the response
                    e.preventDefault = () => {};
                }
            });
        }
    </script>

</body>

</html>