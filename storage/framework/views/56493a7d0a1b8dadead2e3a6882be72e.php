<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">
            📅 Jadwal Layanan
        </h1>
        <div class="flex items-center gap-4">
            <?php
                $prevMonth = $currentDate->copy()->subMonth();
                $nextMonth = $currentDate->copy()->addMonth();
                $route = isset($isPublic) && $isPublic ? 'portal.jadwal' : 'admin.schedules.index';
            ?>
            <a href="<?php echo e(route($route, ['month' => $prevMonth->month, 'year' => $prevMonth->year])); ?>" 
               class="p-2 hover:bg-gray-100 rounded-full">
                &larr;
            </a>
            <span class="font-bold text-lg text-gray-700 uppercase tracking-tighter">
                <?php echo e($currentDate->translatedFormat('F Y')); ?>

            </span>
            <a href="<?php echo e(route($route, ['month' => $nextMonth->month, 'year' => $nextMonth->year])); ?>" 
               class="p-2 hover:bg-gray-100 rounded-full">
                &rarr;
            </a>
        </div>
    </div>

    <?php
        $daysInMonth = $currentDate->daysInMonth;
        $firstDayOfMonth = $currentDate->copy()->startOfMonth()->dayOfWeek;
    ?>

    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-7 border-b bg-gray-50">
            <?php $__currentLoopData = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dayName): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="py-2 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    <?php echo e($dayName); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="grid grid-cols-7">
            <?php for($i = 0; $i < $firstDayOfMonth; $i++): ?>
                <div class="h-32 md:h-40 border-r border-b bg-gray-50/30"></div>
            <?php endfor; ?>

            <?php for($day = 1; $day <= $daysInMonth; $day++): ?>
                <?php
                    $dateStr = $currentDate->copy()->day($day)->format('Y-m-d');
                    $dayItems = $dispatches->get($dateStr, collect());
                ?>
                <div class="h-32 md:h-40 border-r border-b p-1 overflow-y-auto hover:bg-gray-50 transition relative">
                    <div class="text-right text-xs font-black text-gray-300 mb-1 sticky top-0 bg-transparent">
                        <?php echo e($day); ?>

                    </div>
                    <div class="space-y-1">
                        <?php $__currentLoopData = $dayItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($item->calendar_type === 'event'): ?>
                                <?php
                                    $bgColor = $item->type === 'disaster' ? 'bg-orange-600' : 'bg-pink-600';
                                    $borderColor = $item->type === 'disaster' ? 'border-orange-700' : 'border-pink-700';
                                    $label = $item->type === 'disaster' ? '🚨 BENCANA' : '🎪 EVENT';
                                ?>
                                <div class="text-[9px] p-1.5 rounded-md leading-tight border shadow-sm <?php echo e($bgColor); ?> <?php echo e($borderColor); ?> text-white">
                                    <div class="font-black uppercase tracking-tighter flex items-center gap-1">
                                        <?php echo e($label); ?>

                                    </div>
                                    <div class="font-bold mt-0.5"><?php echo e($item->event_name); ?></div>
                                    <div class="text-[8px] opacity-80 truncate"><?php echo e($item->needs); ?></div>
                                    
                                    <?php if($item->dispatches->isNotEmpty()): ?>
                                        <div class="mt-1 pt-1 border-t border-white/20 space-y-1">
                                            <?php $__currentLoopData = $item->dispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="flex flex-col">
                                                    <span class="font-black">🚑 <?php echo e($d->ambulance?->code ?? '?'); ?></span>
                                                    <span class="opacity-80 text-[7px]">👤 <?php echo e(explode(' ', $d->driver?->name ?? 'No Driver')[0]); ?></span>
                                                </div>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <?php
                                    $isPending = $item->calendar_type === 'request';
                                    $isJenazah = $isPending 
                                        ? ($item->service_type === 'jenazah') 
                                        : ($item->patient_condition === 'jenazah');
                                    
                                    $title = '';
                                    if ($isPending) {
                                        $title = 'MENUNGGU';
                                    } else {
                                        if ($item->status === 'completed') {
                                            $title = 'SELESAI';
                                        } elseif ($item->status === 'assigned') {
                                            $title = 'ASSIGNED';
                                        } else {
                                            $title = strtoupper($item->status);
                                        }
                                    }
                                ?>
                                <div class="text-[9px] p-1.5 rounded-md leading-tight border shadow-sm
                                    <?php if($isJenazah): ?> 
                                        bg-stone-900 border-stone-950 text-white
                                    <?php else: ?>
                                        bg-red-600 border-red-700 text-white
                                    <?php endif; ?>">
                                    <div class="font-bold flex justify-between items-center mb-0.5">
                                        <span class="bg-white/20 px-1 rounded">
                                            <?php if($item->pickup_time): ?>
                                                <?php echo e(\Carbon\Carbon::parse($item->pickup_time)->format('H:i')); ?>

                                            <?php else: ?>
                                                <?php echo e($item->created_at->format('H:i')); ?>

                                            <?php endif; ?>
                                        </span>
                                        <span class="text-[8px] font-black tracking-tighter opacity-80"><?php echo e($title); ?></span>
                                    </div>
                                    <div class="truncate font-black">
                                        <?php if($isPending): ?>
                                            🕒 STANDBY
                                        <?php else: ?>
                                            <?php echo e($item->ambulance?->code ?? '?'); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="truncate opacity-90 font-medium">
                                        <?php if($isPending): ?>
                                            -
                                        <?php else: ?>
                                            👤 <?php echo e(explode(' ', $item->driver?->name ?? 'No Driver')[0]); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div class="truncate italic opacity-75 mt-0.5 text-[8px]">
                                        <?php echo e($item->patient_name); ?>

                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endfor; ?>

            <?php
                $lastDayOfMonth = $currentDate->copy()->endOfMonth()->dayOfWeek;
                $remainingDays = 6 - $lastDayOfMonth;
            ?>
            <?php for($i = 0; $i < $remainingDays; $i++): ?>
                <div class="h-32 md:h-40 border-r border-b bg-gray-50/30"></div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap gap-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded bg-red-600"></span> Ambulance
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded bg-stone-900"></span> Jenazah
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded bg-pink-600"></span> Event
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(isset($isPublic) && $isPublic ? 'layouts.public_calendar' : 'layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/schedules/calendar.blade.php ENDPATH**/ ?>