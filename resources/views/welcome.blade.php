<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Damkar Kabupaten Bekasi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="antialiased bg-gray-100 text-gray-800">
    <div class="min-h-screen flex flex-col items-center justify-center">
        <h1 class="text-4xl font-bold text-blue-600 mb-4">
            Ambulance Dispatch System
        </h1>

            di Dinas Pemadam Kebakaran dan Penyelamatan Kabupaten Bekasi.

        <div class="flex space-x-4">
            <a href="{{ route('login') }}"
               class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Login
            </a>

            <a href="{{ route('register') }}"
               class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Register
            </a>
        </div>
    </div>
</body>
</html>
