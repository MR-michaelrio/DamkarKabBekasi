<?php $__env->startSection('title', 'Drivers | Damkar Dispatch'); ?>


<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            👨‍✈️ Manajemen Driver
        </h1>
        <a href="<?php echo e(route('admin.drivers.create')); ?>"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow w-full sm:w-auto text-center">
            ➕ Tambah Driver
        </a>

    </div>

    <!-- Alert -->
    <?php if(session('success')): ?>
    <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
        <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    <!-- Table -->
    <div class="bg-white shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-600 uppercase text-xs">
                        <th class="px-6 py-3 whitespace-nowrap">Nama</th>
                        <th class="px-6 py-3 whitespace-nowrap">No HP</th>
                        <th class="px-6 py-3 whitespace-nowrap">No SIM</th>
                        <th class="px-6 py-3 whitespace-nowrap">Status</th>
                        <th class="px-6 py-3 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-semibold text-gray-800 whitespace-nowrap"><?php echo e($driver->name); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo e($driver->phone ?? '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap"><?php echo e($driver->license_number ?? '-'); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded text-xs font-bold
                                <?php echo e($driver->status === 'available' ? 'bg-green-100 text-green-700' :
                                   ($driver->status === 'on_duty' ? 'bg-red-100 text-red-700' :
                                    'bg-gray-200 text-gray-600')); ?>">


                                <?php echo e(strtoupper(str_replace('_',' ',$driver->status))); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 text-right space-x-3 whitespace-nowrap">
                            <a href="<?php echo e(route('admin.drivers.edit',$driver)); ?>"
                                class="text-blue-600 hover:text-blue-800 font-bold">
                                Edit
                            </a>
                            <form method="POST" action="<?php echo e(route('admin.drivers.destroy',$driver)); ?>" class="inline">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button onclick="return confirm('Hapus driver?')"
                                    class="text-red-600 hover:text-red-800 font-bold">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                            👨‍✈️ Belum ada data driver
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/drivers/index.blade.php ENDPATH**/ ?>