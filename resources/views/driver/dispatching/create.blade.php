<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Penugasan | GMCI</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-xl mx-auto px-4 py-6">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('driver.dispatching') }}" class="w-10 h-10 bg-white rounded-full shadow flex items-center justify-center text-gray-600">
            ←
        </a>
        <h1 class="text-xl font-bold text-gray-800">Assign Penugasan</h1>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-white font-bold">Detail Permintaan</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nama Pasien</label>
                <p class="font-bold text-gray-800 text-lg">{{ $patientRequest->patient_name }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tipe Layanan</label>
                    <p class="font-bold text-gray-700">{{ strtoupper($patientRequest->service_type) }}</p>
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nomor HP</label>
                    <p class="font-bold text-gray-700">{{ $patientRequest->phone ?? '-' }}</p>
                </div>
            </div>

            <hr class="border-gray-50">

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1">
                    <span>📍</span> Lokasi Jemput
                </label>
                <p class="text-gray-700 mt-1 leading-relaxed">{{ $patientRequest->pickup_address }}</p>
            </div>

            <div>
                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest flex items-center gap-1">
                    <span>🏁</span> Tujuan
                </label>
                <p class="text-gray-700 mt-1 leading-relaxed">{{ $patientRequest->destination }}</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('driver.patient-requests.store-dispatch', $patientRequest) }}">
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Driver</label>
                <select name="driver_id" required 
                        class="w-full bg-gray-50 border-gray-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 py-3 text-sm font-medium">
                    <option value="">-- Pilih Driver --</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-400 mt-2">Hanya driver dengan status "Available" yang muncul</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Unit Ambulans</label>
                <input type="text" value="{{ $ambulance->plate_number }}" disabled 
                       class="w-full bg-gray-100 border-gray-100 rounded-xl py-3 px-4 text-sm font-bold text-gray-500 cursor-not-allowed">
                <p class="text-[10px] text-gray-400 mt-2">Otomatis menggunakan unit yang sedang login</p>
            </div>

            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2 mt-4">
                🚀 Ambil Penugasan Ini
            </button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('driver.dispatching') }}" class="text-sm font-bold text-gray-400 hover:text-gray-600">
            Batal & Kembali
        </a>
    </div>

</div>

</body>
</html>
