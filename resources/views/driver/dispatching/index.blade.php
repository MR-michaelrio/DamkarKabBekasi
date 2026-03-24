<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Masuk | Damkar Kabupaten Bekasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-xl mx-auto px-4 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('driver.dashboard') }}" class="w-10 h-10 bg-white rounded-full shadow flex items-center justify-center text-gray-600">
            ←
        </a>
        <h1 class="text-xl font-bold text-gray-800">📋 Laporan Masyarakat</h1>
    </div>

    <!-- Sorting Toggle -->
    <div class="mb-4 flex justify-end">
        <a href="{{ route('driver.dispatching', ['direction' => ($direction === 'asc' ? 'desc' : 'asc')]) }}" 
           class="inline-flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-gray-200 text-xs font-bold text-gray-600 hover:bg-gray-50 transition active:scale-95">
            <span>Urutan: {{ $direction === 'asc' ? 'Terdekat' : 'Terjauh' }}</span>
            @if($direction === 'asc')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12" />
                </svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h9m5-1v12m0 0l-4-4m4 4l4-4" />
                </svg>
            @endif
        </a>
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
                        <div class="flex gap-2">
                            <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold uppercase tracking-wider rounded">
                                {{ $request->service_type === 'ambulance' ? '🚒 Unit Damkar' : '🚒 Rescue' }}
                            </span>
                            @if($request->patient_condition)
                            <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[10px] font-bold uppercase tracking-wider rounded">
                                {{ strtoupper($request->patient_condition) }}
                            </span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-400">
                            {{ $request->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <h3 class="font-bold text-gray-800 leading-tight">
                        {{ $request->patient_name }}
                    </h3>
                    <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mt-1">
                        📅 {{ $request->request_date ? \Carbon\Carbon::parse($request->request_date)->translatedFormat('d M Y') : '-' }}
                        &nbsp;·&nbsp;
                        🕒 {{ $request->pickup_time ? \Carbon\Carbon::parse($request->pickup_time)->format('H:i') : '-' }} WIB
                    </p>
                    
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
                        class="bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2 rounded-lg shadow-sm transition active:scale-95">
                        Respon Laporan
                    </a>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl p-10 text-center shadow-sm border border-gray-100">
                <div class="text-4xl mb-3">📭</div>
                <p class="text-gray-500 font-medium">Belum ada laporan masuk</p>
                <p class="text-xs text-gray-400 mt-1">Standby untuk informasi selanjutnya</p>
            </div>
        @endforelse
    </div>

</div>

</body>
</html>
