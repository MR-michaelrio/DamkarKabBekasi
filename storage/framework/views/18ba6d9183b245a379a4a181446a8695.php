<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">
            📅 Jadwal Layanan
        </h1>
        <div class="flex items-center gap-4">
            <a href="<?php echo e(route('admin.schedules.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year])); ?>" 
               class="p-2 hover:bg-gray-100 rounded-full">
                &larr;
            </a>
            <span class="font-bold text-lg text-gray-700">
                <?php echo e($currentDate->translatedFormat('F Y')); ?>

            </span>
            <a href="<?php echo e(route('admin.schedules.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year])); ?>" 
               class="p-2 hover:bg-gray-100 rounded-full">
                &rarr;
            </a>
        </div>
    </div>

    <?php
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfMonth = $currentDate->copy()->startOfMonth()->dayOfWeek; // 0 (Sun) to 6 (Sat)
        // Adjust if your week starts on Monday? default is 0 for Sunday.
    ?>

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 border-b bg-gray-50">
            <?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="py-2 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <?php echo e($dayName); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="grid grid-cols-7">
            <!-- Blank days for the first week -->
            <?php for($i = 0; $i < $firstDayOfMonth; $i++): ?>
                <div class="h-32 border-r border-b bg-gray-50/50"></div>
            <?php endfor; ?>

            <!-- Days of the month -->
            <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                <?php
                    $dateStr = $currentDate->copy()->day($day)->format('Y-m-d');
                    $dayDispatches = $dispatches->get($dateStr, collect());
                ?>
                <div class="h-32 border-r border-b p-1 overflow-y-auto hover:bg-gray-50 transition">
                    <div class="text-right text-xs font-bold text-gray-400 mb-1">
                        <?php echo e($day); ?>

                    </div>
                    <div class="space-y-1">
                        <?php $__currentLoopData = $dayDispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
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
                            ?>
                            <div class="text-[9px] p-1 rounded-md leading-tight border shadow-sm
                                <?php if($isJenazah): ?> 
                                    bg-black border-gray-900 text-white
                                <?php else: ?>
                                    bg-red-600 border-red-700 text-white
                                <?php endif; ?>">
                                <div class="font-bold flex justify-between">
                                    <span>
                                        <?php if($d->pickup_time): ?>
                                            <?php echo e(\Carbon\Carbon::parse($d->pickup_time)->format('H:i')); ?>

                                        <?php else: ?>
                                            <?php echo e($d->created_at->format('H:i')); ?>

                                        <?php endif; ?>
                                    </span>
                                    <span><?php echo e($title); ?></span>
                                </div>
                                <div class="truncate font-semibold mt-0.5">
                                    <?php if($isPending): ?>
                                        🕒 Belum Ada Armada
                                    <?php else: ?>
                                        <?php echo e($d->ambulance?->code ?? '?'); ?> - <?php echo e($d->ambulance?->plate_number ?? '-'); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="truncate opacity-90">
                                    <?php if($isPending): ?>
                                        👤 Belum Ada Driver
                                    <?php else: ?>
                                        👤 <?php echo e($d->driver?->name ?? 'No Driver'); ?>

                                    <?php endif; ?>
                                </div>
                                <div class="truncate italic opacity-75 mt-0.5">
                                    <?php echo e($d->patient_name); ?>

                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endfor; ?>

            <!-- Blank days for the last week -->
            <?php
                $lastDayOfMonth = $currentDate->copy()->endOfMonth()->dayOfWeek;
                $remainingDays = 6 - $lastDayOfMonth;
            ?>
            <?php for($i = 0; $i < $remainingDays; $i++): ?>
                <div class="h-32 border-r border-b bg-gray-50/50"></div>
            <?php endfor; ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/admin/schedules/calendar.blade.php ENDPATH**/ ?>