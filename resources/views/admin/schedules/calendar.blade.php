@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">
            📅 Jadwal Layanan
        </h1>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.schedules.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" 
               class="p-2 hover:bg-gray-100 rounded-full">
                &larr;
            </a>
            <span class="font-bold text-lg text-gray-700">
                {{ $currentDate->translatedFormat('F Y') }}
            </span>
            <a href="{{ route('admin.schedules.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" 
               class="p-2 hover:bg-gray-100 rounded-full">
                &rarr;
            </a>
        </div>
    </div>

    @php
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfMonth = $currentDate->copy()->startOfMonth()->dayOfWeek; // 0 (Sun) to 6 (Sat)
        // Adjust if your week starts on Monday? default is 0 for Sunday.
    @endphp

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 border-b bg-gray-50">
            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                <div class="py-2 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    {{ $dayName }}
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-7">
            <!-- Blank days for the first week -->
            @for($i = 0; $i < $firstDayOfMonth; $i++)
                <div class="h-32 border-r border-b bg-gray-50/50"></div>
            @endfor

            <!-- Days of the month -->
            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateStr = $currentDate->copy()->day($day)->format('Y-m-d');
                    $dayDispatches = $dispatches->get($dateStr, collect());
                @endphp
                <div class="h-32 border-r border-b p-1 overflow-y-auto hover:bg-gray-50 transition">
                    <div class="text-right text-xs font-bold text-gray-400 mb-1">
                        {{ $day }}
                    </div>
                    <div class="space-y-1">
                        @foreach($dayDispatches as $d)
                            @php
                                $isPending = !($d instanceof \App\Models\Dispatch);
                                $isJenazah = $isPending 
                                    ? ($d->service_type === 'jenazah') 
                                    : ($d->patient_condition === 'jenazah');
                                
                                $title = '';
                                if ($isPending) {
                                    $title = 'MENUNGGU';
                                } else {
                                    if ($d->status === 'completed') {
                                        $title = 'SELESAI';
                                    } elseif ($d->status === 'assigned') {
                                        $title = 'DITUGASKAN';
                                    } else {
                                        $title = strtoupper($d->status);
                                    }
                                }
                            @endphp
                            <div class="text-[9px] p-1 rounded-md leading-tight border shadow-sm
                                @if($isJenazah) 
                                    bg-black border-gray-900 text-white
                                @else
                                    bg-red-600 border-red-700 text-white
                                @endif">
                                <div class="font-bold flex justify-between">
                                    <span>
                                        @if($d->pickup_time)
                                            {{ \Carbon\Carbon::parse($d->pickup_time)->format('H:i') }}
                                        @else
                                            {{ $d->created_at->format('H:i') }}
                                        @endif
                                    </span>
                                    <span>{{ $title }}</span>
                                </div>
                                <div class="truncate font-semibold mt-0.5">
                                    @if($isPending)
                                        🕒 Belum Ada Armada
                                    @else
                                        {{ $d->ambulance?->code ?? '?' }} - {{ $d->ambulance?->plate_number ?? '-' }}
                                    @endif
                                </div>
                                <div class="truncate opacity-90">
                                    @if($isPending)
                                        👤 Belum Ada Driver
                                    @else
                                        👤 {{ $d->driver?->name ?? 'No Driver' }}
                                    @endif
                                </div>
                                <div class="truncate italic opacity-75 mt-0.5">
                                    {{ $d->patient_name }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endfor

            <!-- Blank days for the last week -->
            @php
                $lastDayOfMonth = $currentDate->copy()->endOfMonth()->dayOfWeek;
                $remainingDays = 6 - $lastDayOfMonth;
            @endphp
            @for($i = 0; $i < $remainingDays; $i++)
                <div class="h-32 border-r border-b bg-gray-50/50"></div>
            @endfor
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap gap-4 text-xs font-medium text-gray-600">
        <div class="flex items-center gap-1">
            <span class="w-3 h-3 rounded bg-red-600 border border-red-700"></span> Ambulance (Emergency/Kontrol/Pulang)
        </div>
        <div class="flex items-center gap-1">
            <span class="w-3 h-3 rounded bg-black border border-gray-900"></span> Mobil Jenazah
        </div>
    </div>
</div>
@endsection
