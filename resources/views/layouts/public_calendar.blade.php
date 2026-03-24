<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Unit | Damkar Bekasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta http-equiv="refresh" content="60"> {{-- Auto Refresh every 60s --}}
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('logo-damkar.png') }}" type="image/png">
</head>
<body class="bg-gray-50 text-gray-900 antialiased min-h-screen">
    <header class="bg-white border-b shadow-sm py-4 mb-6">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logo-damkar.png') }}" alt="Damkar Logo" class="h-8">
                <span class="font-bold text-gray-700">Jadwal Operasional Damkar</span>
            </div>
            <a href="{{ route('portal') }}" class="text-emerald-600 font-bold text-sm">← Kembali ke Portal</a>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="py-8 text-center text-xs text-gray-400">
        © {{ date('Y') }} Damkar Kabupaten Bekasi
    </footer>
</body>
</html>
