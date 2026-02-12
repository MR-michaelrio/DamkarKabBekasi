<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penerimaan Pasien | GMCI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-xl mx-auto px-4 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('driver.dashboard') }}" class="w-10 h-10 bg-white rounded-full shadow flex items-center justify-center text-gray-600">
            ←
        </a>
        <h1 class="text-xl font-bold text-gray-800">📋 Permintaan Pasien</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded-lg text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse($requests as $request)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider rounded">
                            {{ $request->service_type }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $request->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <h3 class="font-bold text-gray-800">{{ $request->patient_name }}</h3>
                    
                    <div class="mt-3 space-y-2 text-sm text-gray-600">
                        <div class="flex items-start gap-2">
                            <span class="mt-0.5">📍</span>
                            <p class="leading-tight">{{ $request->pickup_address }}</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="mt-0.5">🏁</span>
                            <p class="leading-tight">{{ $request->destination }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 p-3 flex justify-end px-4 border-t border-gray-50">
                    <a href="{{ route('driver.patient-requests.create-dispatch', $request) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-4 py-2 rounded-lg shadow-sm transition active:scale-95">
                        Assign Saya
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl p-10 text-center shadow-sm border border-gray-100">
                <div class="text-4xl mb-3">📭</div>
                <p class="text-gray-500 font-medium">Belum ada permintaan masuk</p>
                <p class="text-xs text-gray-400 mt-1">Cek kembali beberapa saat lagi</p>
            </div>
        @endforelse
    </div>

</div>

</body>
</html>
