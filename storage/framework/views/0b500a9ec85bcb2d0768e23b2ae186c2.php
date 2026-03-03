<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-[10px] font-black tracking-widest border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left">Pasien</th>
                    <th class="px-6 py-4 text-left">Ambulans / Driver</th>
                    <th class="px-6 py-4 text-left">Waktu</th>
                    <th class="px-6 py-4 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $dispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900"><?php echo e($d->patient_name); ?></div>
                        <div class="text-[10px] <?php if($d->patient_condition === 'emergency'): ?> text-red-600 <?php else: ?> text-gray-500 <?php endif; ?> font-bold uppercase tracking-tighter">
                            <?php echo e($d->patient_condition); ?>

                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs font-bold text-gray-800"><?php echo e($d->ambulance?->plate_number ?? '-'); ?></span>
                        </div>
                        <div class="text-[10px] text-gray-500 italic"><?php echo e($d->driver?->name ?? '-'); ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-xs text-gray-700 font-medium"><?php echo e($d->created_at->format('H:i')); ?></div>
                        <div class="text-[10px] text-gray-400"><?php echo e($d->created_at->format('d M Y')); ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider
                            <?php if($d->status === 'completed'): ?> bg-emerald-50 text-emerald-700
                            <?php elseif($d->status === 'assigned'): ?> bg-blue-50 text-blue-700
                            <?php else: ?> bg-amber-50 text-amber-700 <?php endif; ?>">
                            <?php echo e(str_replace('_', ' ', $d->status)); ?>

                        </span>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic text-sm">
                        Tidak ada data dispatch untuk periode ini
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/admin/dashboard/partials/dispatch_table.blade.php ENDPATH**/ ?>