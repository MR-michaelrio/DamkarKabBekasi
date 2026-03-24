<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Damkar Bekasi | Dinas Pemadam Kebakaran dan Penyelamatan</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Animasi halus -->
    <style>
        .fade-up {
            animation: fadeUp 1s ease-out both;
        }
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 via-white to-slate-100 text-gray-800">

    <!-- HEADER -->
    <header class="bg-white/80 backdrop-blur border-b shadow-sm">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">

            <!-- LOGO + NAME -->
            <div class="flex items-center gap-4">
                <img src="/logo.png" alt="GMCI Logo" class="h-10">
            </div>

            <!-- LOGIN -->
            <a href="<?php echo e(route('portal')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm shadow">
                🔐 Portal Login
            </a>
        </div>
    </header>

    <!-- HERO -->
    <section class="max-w-7xl mx-auto px-6 py-20 grid md:grid-cols-2 gap-12 items-center">

        <!-- TEXT -->
        <div class="fade-up">
            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight text-gray-900 mb-6">
                Sistem Dispatch Damkar<br>
                <span class="text-red-600">Dinas Pemadam Kebakaran Kab. Bekasi</span>
            </h1>

            <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                Aplikasi ini digunakan untuk mengelola dan memantau
                <strong>penugasan unit damkar secara real-time</strong>,
                mulai dari penanganan kebakaran, rescue,
                hingga bantuan darurat lainnya.
            </p>

            <p class="text-gray-600 mb-8">
                Mendukung pelayanan respons cepat 
                <strong>Dinas Pemadam Kebakaran dan Penyelamatan Kabupaten Bekasi</strong>
                dalam melindungi dan melayani masyarakat.
            </p>

            <div class="flex flex-wrap gap-4">
                <a href="<?php echo e(route('patient-request.create')); ?>"
                   class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow">
                    🚒 Buat Laporan Kejadian
                </a>

                <a href="#tentang"
                   class="px-6 py-3 rounded-xl border border-gray-300 hover:bg-gray-100 text-gray-700 font-semibold">
                    ℹ️ Tentang Kami
                </a>
            </div>
        </div>

        <!-- ILLUSTRATION -->
        <div class="fade-up text-center">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <div class="text-6xl mb-4">🚒</div>
                <div class="font-bold text-xl mb-2">Fire Dispatch System</div>
                <div class="text-gray-500 text-sm">
                    Real-time • Terkoordinasi • Patriotik
                </div>
            </div>
        </div>
    </section>

    <!-- TENTANG -->
    <section id="tentang" class="bg-white border-t">
        <div class="max-w-7xl mx-auto px-6 py-16 grid md:grid-cols-3 gap-8">

            <div class="fade-up">
                <div class="text-3xl mb-3">⚡</div>
                <h3 class="font-bold text-lg mb-2">Respon Cepat</h3>
                <p class="text-gray-600 text-sm">
                    Sistem dirancang untuk mempercepat proses
                    penugasan ambulans dan koordinasi lapangan.
                </p>
            </div>

            <div class="fade-up">
                <div class="text-3xl mb-3">🛰️</div>
                <h3 class="font-bold text-lg mb-2">Monitoring Real-time</h3>
                <p class="text-gray-600 text-sm">
                    Status ambulans, driver, dan dispatch
                    dapat dipantau secara langsung.
                </p>
            </div>

            <div class="fade-up">
                <div class="text-3xl mb-3">❤️</div>
                <h3 class="font-bold text-lg mb-2">Misi Kemanusiaan</h3>
                <p class="text-gray-600 text-sm">
                    Mendukung pelayanan kesehatan dan sosial
                    bagi masyarakat yang membutuhkan.
                </p>
            </div>

        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-slate-900 text-slate-300">
        <div class="max-w-7xl mx-auto px-6 py-8 text-center text-sm">
            © <?php echo e(date('Y')); ?> Dinas Pemadam Kebakaran dan Penyelamatan Kabupaten Bekasi.<br>
            Sistem Dispatch Damkar — Melayani dengan Hati.<br>
            <div class="mt-4 font-bold text-slate-100">Layanan 24 Jam: +62 812-8685-8680</div>
        </div>
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
                                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
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
</html><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/home.blade.php ENDPATH**/ ?>