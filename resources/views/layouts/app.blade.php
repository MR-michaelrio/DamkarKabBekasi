<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Damkar Kabupaten Bekasi')</title>

    <!-- Tailwind CSS (CDN, tanpa Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (Core) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Firebase SDK -->
    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-app.js";
        import { getDatabase, ref, onChildAdded, query, orderByKey, limitToLast } from "https://www.gstatic.com/firebasejs/12.11.0/firebase-database.js";

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyBXeSgAPUaMUVh_pAobbCPeWCFSeeaZLl4",
            authDomain: "damkarkabbekasi.firebaseapp.com",
            projectId: "damkarkabbekasi",
            storageBucket: "damkarkabbekasi.firebasestorage.app",
            messagingSenderId: "1008202040220",
            appId: "1:1008202040220:web:17b4b559245900ce773e59",
            measurementId: "G-4NLWBJ58G9"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);

        // Make Firebase available globally
        window.FirebaseDB = database;
    </script>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo-damkar.png') }}" type="image/png">
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen">

    {{-- Navigation --}}
    @include('layouts.navigation')

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- Audio Notification System -->
    <script>
        // Create global audio context for autoplay policy
        window.audioContext = {
            emergency: null,
            tts: null,
            userInteracted: false,
            
            // Initialize and request notification permission on first interaction
            init() {
                // Request notification permission (counts as user interaction)
                if ('Notification' in window && Notification.permission === 'default') {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            this.userInteracted = true;
                            console.log('Notification permission granted');
                        }
                    });
                } else if ('Notification' in window && Notification.permission === 'granted') {
                    this.userInteracted = true;
                }
                
                // Also listen for any user interaction
                const interactionHandler = () => {
                    this.userInteracted = true;
                };

                document.addEventListener('click', interactionHandler, { once: true });
                document.addEventListener('keydown', interactionHandler, { once: true });
                document.addEventListener('pointerdown', interactionHandler, { once: true });
                document.addEventListener('touchstart', interactionHandler, { once: true });
                document.addEventListener('mousemove', interactionHandler, { once: true });
            },
            
            playEmergency() {
                const audio = new Audio('{{ asset("emergency.mp3") }}');
                audio.volume = 0.8;
                audio.play().catch(error => {
                    console.log('Emergency audio play failed:', error);
                    // Fallback to vibration
                    if ('vibrate' in navigator) {
                        navigator.vibrate([500, 100, 500]);
                    }
                });
            },
            
            playTTS(url) {
                if (!url) return;
                setTimeout(() => {
                    const audio = new Audio(url);
                    audio.volume = 0.9;
                    audio.play().catch(error => {
                        console.log('TTS audio play failed:', error);
                    });
                }, 3000);
            }
        };
        
        // Initialize audio context
        window.audioContext.init();
    </script>

    <!-- Global Notification Polling System -->
    <script>
        // Global notification polling for all admin pages
        if (window.location.pathname.startsWith('/admin/')) {
            window.notificationPoller = {
                lastRequestId: parseInt(localStorage.getItem('lastNotifiedRequestId') || '0'),
                notifiedIds: new Set(JSON.parse(localStorage.getItem('notifiedRequestIds') || '[]')),
                pollingInterval: null,

                init() {
                    console.log('🔄 Initializing global notification poller with lastRequestId:', this.lastRequestId);
                    // Start polling immediately, then every 10 seconds
                    this.checkForNewRequests();
                    this.pollingInterval = setInterval(() => {
                        this.checkForNewRequests();
                    }, 10000);
                },

                saveNotifiedId(id) {
                    this.notifiedIds.add(id);
                    localStorage.setItem('notifiedRequestIds', JSON.stringify(Array.from(this.notifiedIds)));
                    this.lastRequestId = Math.max(this.lastRequestId, id);
                    localStorage.setItem('lastNotifiedRequestId', this.lastRequestId.toString());
                },

                checkForNewRequests() {
                    console.log('🔍 Checking for new requests with last_id:', this.lastRequestId);
                    fetch('/api/check-new-requests?last_id=' + this.lastRequestId)
                    .then(response => response.json())
                    .then(data => {
                        console.log('📡 API Response:', data);
                        if (data.new_requests && data.new_requests.length > 0) {
                            const maxRequestId = Math.max(...data.new_requests.map(request => request.id));

                            // If this is the first sync and we have no stored IDs,
                            // do not notify existing historical requests.
                            if (this.lastRequestId === 0 && this.notifiedIds.size === 0) {
                                console.log('📥 Initial sync: storing latest request id without notifying older entries');
                                this.lastRequestId = maxRequestId;
                                localStorage.setItem('lastNotifiedRequestId', this.lastRequestId.toString());
                                return;
                            }

                            const unseenRequests = data.new_requests.filter(request => !this.notifiedIds.has(request.id));
                            if (unseenRequests.length > 0) {
                                console.log('🚨 Unseen new requests:', unseenRequests);
                                unseenRequests.forEach(request => {
                                    this.triggerNotifications(request);
                                    this.saveNotifiedId(request.id);
                                });
                            } else {
                                console.log('✅ New requests already notified before');
                            }
                        } else {
                            console.log('✅ No new requests found');
                        }
                    })
                    .catch(error => console.log('❌ Polling error:', error));
                },

                triggerNotifications(request) {
                    console.log('🔔 Triggering notifications for:', request);

                    if ('Notification' in window) {
                        if (Notification.permission === 'default') {
                            Notification.requestPermission().then(permission => {
                                if (permission === 'granted') {
                                    this.showNotification(request);
                                }
                            });
                        } else if (Notification.permission === 'granted') {
                            this.showNotification(request);
                        }
                    }

                    if (window.audioContext && window.audioContext.userInteracted) {
                        console.log('🔊 Playing emergency sound...');
                        window.audioContext.playEmergency();
                        window.audioContext.playTTS(request.tts_url);
                    } else {
                        console.log('🔇 Not playing audio - user has not interacted or audioContext not ready');
                    }
                },

                showNotification(request) {
                    const notification = new Notification('🚨 Permintaan Baru Masuk!', {
                        body: `${request.patient_name} - ${request.service_type} di ${request.pickup_address}`,
                        icon: '{{ asset("logo-damkar.png") }}',
                        tag: 'new-patient-request-' + request.id,
                        requireInteraction: true
                    });

                    notification.onclick = function() {
                        window.location.href = '/admin/laporan-masyarakat';
                        notification.close();
                    };

                    setTimeout(() => {
                        notification.close();
                    }, 15000);
                },

                destroy() {
                    if (this.pollingInterval) {
                        clearInterval(this.pollingInterval);
                    }
                }
            };

            // Initialize polling when page loads
            window.notificationPoller.init();

            // Clean up when page unloads
            window.addEventListener('beforeunload', () => {
                window.notificationPoller.destroy();
            });

            // Debug commands available in console
            window.debugNotifications = {
                checkStorage: () => {
                    console.log('Current lastNotifiedRequestId:', localStorage.getItem('lastNotifiedRequestId'));
                    return localStorage.getItem('lastNotifiedRequestId');
                },
                resetStorage: () => {
                    localStorage.removeItem('lastNotifiedRequestId');
                    window.notificationPoller.lastRequestId = 0;
                    console.log('Reset lastNotifiedRequestId to 0');
                },
                testAudio: () => {
                    if (window.audioContext) {
                        console.log('Testing emergency audio...');
                        window.audioContext.playEmergency();
                    } else {
                        console.log('Audio context not available');
                    }
                }
            };
            console.log('🔧 Debug commands available: window.debugNotifications.checkStorage(), window.debugNotifications.resetStorage(), window.debugNotifications.testAudio()');
        }
    </script>

    <!-- Notification Script -->
    <script type="module">
        // Wait for Firebase to be ready
        const checkFirebaseReady = () => {
            return new Promise((resolve) => {
                const check = () => {
                    if (window.FirebaseDB) {
                        resolve(window.FirebaseDB);
                    } else {
                        setTimeout(check, 100);
                    }
                };
                check();
            });
        };

        checkFirebaseReady().then(async (database) => {
            const { ref, onChildAdded, query, orderByKey, limitToLast } = await import("https://www.gstatic.com/firebasejs/12.11.0/firebase-database.js");

            // Listen for new patient requests in Firebase
            const requestsRef = ref(database, 'patient_requests');
            const recentRequestsQuery = query(requestsRef, orderByKey(), limitToLast(1));

            onChildAdded(recentRequestsQuery, (snapshot) => {
                const request = snapshot.val();
                console.log('New patient request from Firebase:', request);

                // Always show notifications and play audio for authenticated users
                // Show browser notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    const notification = new Notification('🚨 Permintaan Baru Masuk!', {
                        body: `${request.patient_name} - ${request.service_type} di ${request.pickup_address}`,
                        icon: '{{ asset("logo-damkar.png") }}',
                        tag: 'new-patient-request',
                        requireInteraction: true
                    });

                    notification.onclick = function () {
                        // Redirect to patient requests page
                        window.location.href = '/admin/laporan-masyarakat';
                        notification.close();
                    };
                }

                // Play audio immediately if user has interacted
                if (window.audioContext && window.audioContext.userInteracted) {
                    window.audioContext.playEmergency();
                    window.audioContext.playTTS(request.tts_url);
                }

                // Trigger table refresh only if on patient requests page
                if (window.location.pathname.includes('laporan-masyarakat')) {
                    window.dispatchEvent(new CustomEvent('new-patient-request'));
                }
            });
            console.log('Firebase real-time listener active');
        });
    </script>

</body>

</html>