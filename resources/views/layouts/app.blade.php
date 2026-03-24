<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Damkar Bekasi'))</title>

    <!-- Tailwind CSS (CDN, tanpa Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (Core) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen">

    {{-- Navigation --}}
    @include('layouts.navigation')

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

</body>
</html>
