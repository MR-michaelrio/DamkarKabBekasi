<table class="w-full text-sm">
    <thead class="bg-gray-100 text-gray-700">
        <tr>
            <th class="px-4 py-3 text-left">Pelapor</th>
            <th class="px-4 py-3 text-left">Jadwal</th>
            <th class="px-4 py-3 text-left">Lokasi</th>
            <th class="px-4 py-3 text-left">Driver</th>
            <th class="px-4 py-3 text-left">Armada</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-center">Aksi</th>
        </tr>
    </thead>

    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $dispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="border-t hover:bg-gray-50">

            <!-- Pelapor -->
            <td class="px-4 py-3">
                <div class="font-semibold"><?php echo e($d->patient_name); ?></div>
                <div class="text-xs font-bold">
                    <?php if($d->patient_condition === 'kebakaran'): ?>
                        <span class="text-red-600">🔥 KEBAKARAN</span>
                    <?php elseif($d->patient_condition === 'rescue'): ?>
                        <span class="text-blue-600">🚒 RESCUE</span>
                    <?php else: ?>
                        <span class="text-gray-500"><?php echo e(strtoupper($d->patient_condition)); ?></span>
                    <?php endif; ?>
                </div>
            </td>

            <!-- JADWAL -->
            <td class="px-4 py-3">
                <div class="font-medium"><?php echo e($d->request_date?->format('d M Y') ?? '-'); ?></div>
                <div class="text-xs text-gray-500"><?php echo e($d->pickup_time ?? '-'); ?></div>
            </td>

            <!-- LOKASI -->
            <td class="px-4 py-3">
                <?php echo e($d->pickup_address); ?>

            </td>

            <!-- DRIVER -->
            <td class="px-4 py-3">
                <?php echo e($d->driver?->name ?? '-'); ?>

            </td>

            <!-- Armada -->
            <td class="px-4 py-3">
                <?php echo e($d->ambulance?->plate_number ?? '-'); ?>

            </td>

            <!-- STATUS -->
            <td class="px-4 py-3">
                <span class="px-2 py-1 rounded text-xs font-semibold
                <?php if($d->status === 'completed'): ?> bg-green-100 text-green-700
                <?php elseif($d->status === 'pending'): ?> bg-blue-100 text-blue-700
                <?php else: ?> bg-yellow-100 text-yellow-700 <?php endif; ?>">
                    <?php echo e(str_replace('_',' ', strtoupper($d->status))); ?>

                </span>
            </td>

            <!-- AKSI -->
            <td class="px-4 py-3 text-center">
                <div class="flex justify-center gap-2">

                    <!-- DETAIL -->
                    <a href="<?php echo e(route('admin.dispatches.show', $d)); ?>"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-xs">
                        📄 Detail
                    </a>

                    <?php if($d->status !== 'completed'): ?>
                    <!-- NEXT -->
                    <form method="POST" action="<?php echo e(route('admin.dispatches.next', $d)); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs">
                            ▶ Next
                        </button>
                    </form>
                    <?php endif; ?>

                    <!-- DELETE -->
                    <form method="POST" action="<?php echo e(route('admin.dispatches.destroy', $d)); ?>"
                        onsubmit="return confirm('Yakin hapus dispatch ini?')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs">
                            🗑 Hapus
                        </button>
                    </form>

                </div>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                Belum ada data dispatch
            </td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/dispatches/_table.blade.php ENDPATH**/ ?>