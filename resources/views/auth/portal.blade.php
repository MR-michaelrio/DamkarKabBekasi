<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portal GMCI | Global Medical Care Indonesia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .fade-up {
            animation: fadeUp 0.8s ease-out both;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-slate-100 min-h-screen text-gray-800 flex flex-col">

    <!-- Header -->
    <header class="bg-white/80 backdrop-blur border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <a href="/" class="flex items-center gap-3 group">
                <span class="text-emerald-600 group-hover:-translate-x-1 transition-transform">←</span>
                <span class="font-semibold text-gray-700">Kembali ke Beranda</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center p-6">
        <div class="max-w-4xl w-full">
            
            <div class="text-center mb-12 fade-up">
                <img src="/logo.png" alt="GMCI Logo" class="h-16 mx-auto mb-4">
                <h1 class="text-3xl font-bold text-gray-900">Portal Operasional</h1>
                <p class="text-gray-500">Silakan pilih akses yang sesuai dengan peran Anda</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <!-- Admin Login -->
                <a href="{{ route('login') }}" 
                   class="fade-up group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-emerald-500 hover:shadow-xl transition-all duration-300 text-center"
                   style="animation-delay: 0.1s">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🔐</div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Login Admin</h2>
                    <p class="text-sm text-gray-500 mb-6">Akses dashboard manajemen, driver, dan laporan dispatch.</p>
                    <div class="inline-flex items-center text-emerald-600 font-semibold">
                        Masuk Admin →
                    </div>
                </a>

                <!-- Ambulance Login -->
                <a href="{{ route('ambulance.login') }}" 
                   class="fade-up group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-orange-500 hover:shadow-xl transition-all duration-300 text-center"
                   style="animation-delay: 0.2s">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🚑</div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Login Unit</h2>
                    <p class="text-sm text-gray-500 mb-6">Akses dashboard unit ambulans untuk update lokasi dan tugas.</p>
                    <div class="inline-flex items-center text-orange-600 font-semibold">
                        Masuk Unit →
                    </div>
                </a>

                <!-- Monitoring -->
                <a href="{{ route('monitoring') }}" 
                   class="fade-up group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-500 hover:shadow-xl transition-all duration-300 text-center"
                   style="animation-delay: 0.3s">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🗺️</div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Monitoring</h2>
                    <p class="text-sm text-gray-500 mb-6">Pantau pergerakan ambulans dan status dispatch secara publik.</p>
                    <div class="inline-flex items-center text-blue-600 font-semibold">
                        Lihat Peta →
                    </div>
                </a>

                <!-- Public Calendar -->
                <a href="{{ route('portal.jadwal') }}" 
                   class="fade-up group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-indigo-500 hover:shadow-xl transition-all duration-300 text-center"
                   style="animation-delay: 0.4s">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">📅</div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Jadwal Unit</h2>
                    <p class="text-sm text-gray-500 mb-6">Lihat jadwal penggunaan unit ambulans dan mobil jenazah.</p>
                    <div class="inline-flex items-center text-indigo-600 font-semibold">
                        Buka Jadwal →
                    </div>
                </a>

                <!-- Event Request -->
                <a href="{{ route('portal.event-request.create') }}" 
                   class="fade-up group bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:border-pink-500 hover:shadow-xl transition-all duration-300 text-center"
                   style="animation-delay: 0.5s">
                    <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">🎪</div>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Request Event</h2>
                    <p class="text-sm text-gray-500 mb-6">Pengajuan bantuan medis atau standby unit untuk kegiatan event.</p>
                    <div class="inline-flex items-center text-pink-600 font-semibold">
                        Minta Bantuan →
                    </div>
                </a>

            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="py-8 text-center text-xs text-gray-400">
        © {{ date('Y') }} Global Medical Care Indonesia.
    </footer>

    <script>
        const isCapacitor = window.hasOwnProperty('Capacitor') && window.Capacitor.hasOwnProperty('Plugins');
        const CapacitorPlugins = isCapacitor ? window.Capacitor.Plugins : {};

        async function initializePublicPushNotifications() {
            if (!isCapacitor || !CapacitorPlugins.PushNotifications) return;

            const { PushNotifications } = CapacitorPlugins;
            try {
                let permStatus = await PushNotifications.checkPermissions();
                if (permStatus.receive === 'prompt') {
                    permStatus = await PushNotifications.requestPermissions();
                }
                if (permStatus.receive === 'granted') {
                    PushNotifications.addListener('registration', (token) => {
                        fetch('/public-fcm-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ token: token.value })
                        }).catch(err => console.error(err));
                    });

                    PushNotifications.addListener('pushNotificationReceived', (notification) => {
                         alert("Informasi Baru:\n" + notification.title + "\n" + notification.body);
                    });

                    await PushNotifications.register();
                }
            } catch (e) {
                console.error('Push error:', e);
            }
        }

        if (isCapacitor) {
            initializePublicPushNotifications();
        }
    </script>
</body>
</html>
