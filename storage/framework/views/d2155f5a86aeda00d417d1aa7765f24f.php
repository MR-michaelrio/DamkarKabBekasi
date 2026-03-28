<?php $__env->startSection('title', 'Edit Driver | Damkar Dispatch'); ?>


<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ✏️ Edit Driver
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Perbarui data profil driver
        </p>
    </div>

    <form method="POST" action="<?php echo e(route('admin.drivers.update',$driver)); ?>"
        class="bg-white p-6 rounded-xl shadow border border-gray-100 space-y-5">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Driver</label>
            <input name="name" value="<?php echo e(old('name', $driver->name)); ?>"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">No HP</label>
            <input name="phone" value="<?php echo e(old('phone', $driver->phone)); ?>"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">No SIM</label>
            <input name="license_number" value="<?php echo e(old('license_number', $driver->license_number)); ?>"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
            <select name="status"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="available" <?php echo e(old('status', $driver->status) === 'available' ? 'selected' : ''); ?>>Available</option>
                <option value="on_duty" <?php echo e(old('status', $driver->status) === 'on_duty' ? 'selected' : ''); ?>>On Duty
                </option>
                <option value="inactive" <?php echo e(old('status', $driver->status) === 'inactive' ? 'selected' : ''); ?>>Inactive
                </option>
            </select>
        </div>

        <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4 border-t border-gray-50">
            <a href="<?php echo e(route('admin.drivers.index')); ?>"
                class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                ← Kembali
            </a>
            <button
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                Update Driver
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/drivers/edit.blade.php ENDPATH**/ ?>