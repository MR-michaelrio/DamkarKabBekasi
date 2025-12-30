<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-6 py-6">

    <div class="flex justify-between mb-6">
        <h1 class="text-2xl font-bold">🚨 Dispatch Ambulans</h1>

        <a href="<?php echo e(route('admin.dispatches.create')); ?>"
           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
            + Dispatch Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3">Pasien</th>
                    <th class="px-4 py-3">Lokasi</th>
                    <th class="px-4 py-3">Driver</th>
                    <th class="px-4 py-3">Ambulans</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $dispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-t">
                    <td class="px-4 py-3">
                        <strong><?php echo e($d->patient_name); ?></strong><br>
                        <span class="text-xs"><?php echo e($d->patient_condition); ?></span>
                    </td>

                    <td class="px-4 py-3"><?php echo e($d->pickup_address); ?></td>
                    <td class="px-4 py-3"><?php echo e($d->driver?->name ?? '-'); ?></td>
                    <td class="px-4 py-3"><?php echo e($d->ambulance?->plate_number ?? '-'); ?></td>
                    <td class="px-4 py-3 font-semibold"><?php echo e($d->status); ?></td>

                    <td class="px-4 py-3 text-center space-x-2">
                        <?php if($d->status !== 'completed'): ?>
                        <form method="POST" action="<?php echo e(route('admin.dispatches.next', $d)); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button class="bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                NEXT
                            </button>
                        </form>
                        <?php endif; ?>

                        <form method="POST" action="<?php echo e(route('admin.dispatches.destroy', $d)); ?>"
                              class="inline"
                              onsubmit="return confirm('Hapus dispatch ini?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="bg-gray-600 text-white px-3 py-1 rounded text-xs">
                                HAPUS
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                        Belum ada dispatch
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/ambulance-dispatch/resources/views/admin/dispatches/index.blade.php ENDPATH**/ ?>