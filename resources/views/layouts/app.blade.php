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

            // Request notification permission
            if ('Notification' in window && Notification.permission === 'default') {
                Notification.requestPermission();
            }

            // Listen for new patient requests in Firebase
            const requestsRef = ref(database, 'patient_requests');
            const recentRequestsQuery = query(requestsRef, orderByKey(), limitToLast(1));

            onChildAdded(recentRequestsQuery, (snapshot) => {
                const request = snapshot.val();
                console.log('New patient request from Firebase:', request);

                // Show browser notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    const notification = new Notification('Permintaan Baru Masuk!', {
                        body: `${request.patient_name} - ${request.service_type} di ${request.pickup_address}`,
                        icon: '{{ asset("logo-damkar.png") }}',
                        tag: 'new-patient-request'
                    });

                    notification.onclick = function () {
                        window.focus();
                        notification.close();
                    };
                }

                // Play emergency sound first
                const audio = new Audio('{{ asset("emergency.mp3") }}');
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

                // Play TTS audio if available (with delay after emergency sound)
                if (request.tts_url) {
                    setTimeout(() => {
                        const ttsAudio = new Audio(request.tts_url);
                        ttsAudio.volume = 0.9;
                        ttsAudio.play().then(() => {
                            console.log('TTS audio played successfully');
                        }).catch(error => {
                            console.log('TTS audio play failed:', error);
                        });
                    }, 1000); // Wait 1 second after emergency sound
                }

                // Trigger table refresh if on patient requests or dispatch page
                if (window.location.pathname.includes('laporan-masyarakat') || window.location.pathname.includes('dispatches')) {
                    window.dispatchEvent(new CustomEvent('new-patient-request'));
                }
            });
            console.log('Firebase real-time listener active');
        });
    </script>

</body>

</html>