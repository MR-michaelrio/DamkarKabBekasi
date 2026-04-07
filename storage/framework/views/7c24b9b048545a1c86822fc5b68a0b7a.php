<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Damkar Kabupaten Bekasi'); ?></title>

    <!-- Tailwind CSS (CDN, tanpa Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (Core) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Laravel Echo & Pusher -->
    <script src="https://js.pusherapp.com/8.2.0/pusher.min.js"></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>

    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('logo-damkar.png')); ?>" type="image/png">
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen">

    
    <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- Notification Script -->
    <?php if(auth()->guard()->check()): ?>
        <script>
            // Request notification permission
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }

            let lastRequestId = 0;

            // Function to check for new requests
            function checkForNewRequests() {
                fetch('/api/check-new-requests?last_id=' + lastRequestId, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.new_requests && data.new_requests.length > 0) {
                        data.new_requests.forEach(request => {
                            // Show browser notification
                            if ('Notification' in window && Notification.permission === 'granted') {
                                const notification = new Notification('Permintaan Baru Masuk!', {
                                    body: `${request.patient_name} - ${request.service_type} di ${request.pickup_address}`,
                                    icon: '<?php echo e(asset("logo-damkar.png")); ?>',
                                    tag: 'new-patient-request'
                                });

                                notification.onclick = function () {
                                    window.focus();
                                    notification.close();
                                };
                            }

                            // Play emergency sound
                            const audio = new Audio('<?php echo e(asset("emergency.mp3")); ?>');
                            audio.volume = 0.8;

                            const playPromise = audio.play();
                            if (playPromise !== undefined) {
                                playPromise.then(() => {
                                    console.log('Emergency sound played successfully');
                                }).catch(error => {
                                    console.log('Audio play failed (likely due to autoplay policy):', error);
                                    if ('vibrate' in navigator) {
                                        navigator.vibrate(500);
                                    }
                                });
                            }

                            // Trigger table refresh if on patient requests or dispatch page
                            if (window.location.pathname.includes('laporan-masyarakat') || window.location.pathname.includes('dispatches')) {
                                window.dispatchEvent(new CustomEvent('newPatientRequest'));
                            }

                            // Update last request ID
                            if (request.id > lastRequestId) {
                                lastRequestId = request.id;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.log('Error checking for new requests:', error);
                });
            }

            // Check for new requests every 30 seconds
            setInterval(checkForNewRequests, 30000);

            // Also try to use WebSockets if available
            if (typeof window.Echo !== 'undefined') {
                window.Echo.channel('patient-requests')
                    .listen('.new-request', (e) => {
                        console.log('New patient request via WebSocket:', e);

                        // Show browser notification
                        if ('Notification' in window && Notification.permission === 'granted') {
                            const notification = new Notification('Permintaan Baru Masuk!', {
                                body: `${e.patient_name} - ${e.service_type} di ${e.pickup_address}`,
                                icon: '<?php echo e(asset("logo-damkar.png")); ?>',
                                tag: 'new-patient-request'
                            });

                            notification.onclick = function () {
                                window.focus();
                                notification.close();
                            };
                        }

                        // Play emergency sound
                        const audio = new Audio('<?php echo e(asset("emergency.mp3")); ?>');
                        audio.volume = 0.8;

                        const playPromise = audio.play();
                        if (playPromise !== undefined) {
                            playPromise.then(() => {
                                console.log('Emergency sound played successfully');
                            }).catch(error => {
                                console.log('Audio play failed (likely due to autoplay policy):', error);
                                if ('vibrate' in navigator) {
                                    navigator.vibrate(500);
                                }
                            });
                        }

                        // Trigger table refresh if on patient requests or dispatch page
                        if (window.location.pathname.includes('laporan-masyarakat') || window.location.pathname.includes('dispatches')) {
                            window.dispatchEvent(new CustomEvent('newPatientRequest'));
                        }
                    });
            } else {
                console.log('WebSocket not available, using polling instead');
            }
        </script>
    <?php endif; ?>

</body>

</html><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/layouts/app.blade.php ENDPATH**/ ?>