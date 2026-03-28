<?php $__env->startSection('title', 'Dashboard | Damkar Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-2">
                🚒 Aktivitas Damkar
            </h1>
            <p class="text-gray-500 text-sm mt-1">
                Laporan aktivitas unit damkar harian, mingguan, dan bulanan
            </p>
        </div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <!-- Filter Form -->
            <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-2 bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
                <?php
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    $currentYear = \Carbon\Carbon::now()->year;
                ?>
                <select name="month" class="text-sm border-none focus:ring-0 bg-transparent text-gray-700 font-medium py-1.5 pl-3 pr-8 w-32 cursor-pointer">
                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($num); ?>" <?php echo e($selectedMonth == $num ? 'selected' : ''); ?>><?php echo e($name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <div class="h-4 w-px bg-gray-300"></div>
                <select name="year" class="text-sm border-none focus:ring-0 bg-transparent text-gray-700 font-medium py-1.5 pl-3 pr-8 cursor-pointer">
                    <?php for($y = $currentYear; $y >= $currentYear - 2; $y--): ?>
                        <option value="<?php echo e($y); ?>" <?php echo e($selectedYear == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                    <?php endfor; ?>
                </select>
                <button type="submit" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 p-2 rounded-md transition ml-1" title="Terapkan Filter">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </button>
            </form>

            <div class="bg-red-50 px-4 py-2 rounded-lg border border-red-100">
                <span class="text-xs text-red-600 font-bold uppercase tracking-wider">Total (Bulan Ini)</span>
                <p class="text-2xl font-black text-red-900"><?php echo e($monthDispatches->count()); ?></p>
            </div>
        </div>
    </div>

    <!-- Group: Analytics & Map -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Ambulance Analytics -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    🚒 Analitik Per Unit
                </h2>
                <span class="text-[10px] bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full font-bold uppercase">Bulan <?php echo e($filterDate->translatedFormat('F Y')); ?></span>
            </div>
            <div class="p-5 max-h-[400px] overflow-y-auto">
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $ambulanceAnalytics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $analytic): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-bold text-sm text-gray-800"><?php echo e($analytic->plate_number); ?></p>
                            <p class="text-[10px] text-gray-500"><?php echo e($analytic->code); ?> - <?php echo e($analytic->type); ?></p>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-black text-emerald-600"><?php echo e($analytic->dispatches_count); ?></span>
                            <span class="text-[10px] text-gray-400 block uppercase font-bold">Kali Keluar</span>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-gray-400 italic text-sm py-4">Belum ada data</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Mini Map -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative group">
            <div id="map" class="w-full h-[400px]"></div>
            <div class="absolute top-4 right-4 z-[1000] pointer-events-none">
                <div class="bg-white/90 backdrop-blur-md p-3 rounded-xl shadow-lg border border-white/50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status Live</p>
                    <div class="flex items-center gap-2">
                        <div
                            class="w-2 h-2 bg-green-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]">
                        </div>
                        <span class="text-xs font-black text-gray-700 tracking-tighter uppercase">Tracking Active</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3 Tables Section -->
    <div class="space-y-8">

        <!-- Today's Table -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    📅 Hari Ini
                </h3>
                <a href="<?php echo e(route('admin.dispatches.export.pdf', ['range' => 'today'])); ?>"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-2">
                    📄 Export PDF
                </a>
            </div>
            <?php echo $__env->make('admin.dashboard.partials.dispatch_table', ['dispatches' => $todayDispatches], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

        <!-- Weekly Table -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    🗓️ Minggu Ini
                </h3>
                <a href="<?php echo e(route('admin.dispatches.export.pdf', ['range' => 'week'])); ?>"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-2">
                    📄 Export PDF
                </a>
            </div>
            <?php echo $__env->make('admin.dashboard.partials.dispatch_table', ['dispatches' => $weekDispatches], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

        <!-- Monthly Table -->
        <section>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    📆 Bulan <?php echo e($filterDate->translatedFormat('F Y')); ?>

                </h3>
                <a href="<?php echo e(route('admin.dispatches.export.pdf', ['range' => 'month', 'month' => $selectedMonth, 'year' => $selectedYear])); ?>"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-2">
                    📄 Export PDF
                </a>
            </div>
            <?php echo $__env->make('admin.dashboard.partials.dispatch_table', ['dispatches' => $monthDispatches], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </section>

    </div>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    // Map remains the same, just smaller in context
    const map = L.map('map').setView([-6.219229, 107.104865], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    const markers = {};

    function updateMap() {
        fetch('<?php echo e(route("admin.maps.ambulances")); ?>')
            .then(res => res.json())
            .then(ambulances => {
                ambulances.forEach(a => {
                    if (markers[a.id]) {
                        markers[a.id].setLatLng([a.latitude, a.longitude]);
                    } else {
                        markers[a.id] = L.marker([a.latitude, a.longitude])
                            .addTo(map)
                            .bindPopup(`🚒 ${a.plate_number}<br><span class='text-xs'>${a.status}</span>`);
                    }
                });
            });
    }

    // Refresh map every 10s if Echo is not available or as fallback
    updateMap();
    setInterval(updateMap, 10000);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>