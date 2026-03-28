<?php $__env->startSection('title', 'Tambah Driver | Damkar Dispatch'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ➕ Tambah Driver
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Daftarkan driver baru Damkar
        </p>

    </div>

    <form method="POST" action="<?php echo e(route('admin.drivers.store')); ?>"
        class="bg-white p-6 rounded-xl shadow border border-gray-100 space-y-5">
        <?php echo csrf_field(); ?>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
            <input name="name" placeholder="Nama Driver" value="<?php echo e(old('name')); ?>"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor HP</label>
            <input name="phone" placeholder="0812..." value="<?php echo e(old('phone')); ?>"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor SIM</label>
            <input name="license_number" placeholder="No SIM" value="<?php echo e(old('license_number')); ?>"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Status</label>
            <select name="status"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="available" <?php echo e(old('status')=='available' ? 'selected' : ''); ?>>Available</option>
                <option value="on_duty" <?php echo e(old('status')=='on_duty' ? 'selected' : ''); ?>>On Duty</option>
                <option value="inactive" <?php echo e(old('status')=='inactive' ? 'selected' : ''); ?>>Inactive</option>
            </select>
        </div>

        <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4 border-t border-gray-50">
            <a href="<?php echo e(route('admin.drivers.index')); ?>"
                class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                ← Kembali
            </a>
            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                Simpan Driver
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/drivers/create.blade.php ENDPATH**/ ?>