<?php $__env->startSection('title', 'Edit Ambulans | GMCI Dispatch'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ✏️ Edit Ambulans
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Perbarui data ambulans GMCI
        </p>
    </div>

    <!-- Card -->
    <div class="bg-white shadow rounded-xl p-6 border border-gray-100">

        <!-- Error Validation -->
        <?php if($errors->any()): ?>
            <div class="mb-4 bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('admin.ambulances.update', $ambulance->id)); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <!-- Code -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Kode Ambulans
                </label>
                <input type="text" name="code" required
                       value="<?php echo e(old('code', $ambulance->code)); ?>"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Plate -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Plat Nomor
                </label>
                <input type="text" name="plate_number" required
                       value="<?php echo e(old('plate_number', $ambulance->plate_number)); ?>"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Tipe Ambulans
                </label>
                <select name="type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="" disabled>Pilih Tipe</option>
                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->name); ?>" <?php echo e(old('type', $ambulance->type) == $type->name ? 'selected' : ''); ?>>
                            <?php echo e($type->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($types->isEmpty()): ?>
                        <option value="BASIC" <?php echo e(old('type', $ambulance->type) == 'BASIC' ? 'selected' : ''); ?>>BASIC</option>
                        <option value="Jenazah" <?php echo e(old('type', $ambulance->type) == 'Jenazah' ? 'selected' : ''); ?>>Jenazah</option>
                    <?php endif; ?>
                </select>
                <p class="text-xs text-gray-500 mt-1">
                    Atur tipe di menu <a href="<?php echo e(route('admin.ambulance-types.index')); ?>" class="text-blue-600 underline">Tipe Armada</a>
                </p>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Status
                </label>
                <select name="status"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="ready" <?php echo e(old('status', $ambulance->status) === 'ready' ? 'selected' : ''); ?>>Ready</option>
                    <option value="on_duty" <?php echo e(old('status', $ambulance->status) === 'on_duty' ? 'selected' : ''); ?>>On Duty</option>
                    <option value="maintenance" <?php echo e(old('status', $ambulance->status) === 'maintenance' ? 'selected' : ''); ?>>Maintenance</option>
                </select>
            </div>

            <!-- Password (Optional) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Ganti Password (Opsional)
                </label>
                <input type="password" name="password"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Kosongkan jika tidak ingin mengubah">
                <p class="text-xs text-gray-500 mt-1 italic">
                    Gunakan password ini jika staff unit lupa password atau ingin direset oleh Admin.
                </p>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4">
                <a href="<?php echo e(route('admin.ambulances.index')); ?>"
                   class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                    ← Kembali
                </a>

                <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                    Update Ambulans
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/admin/ambulances/edit.blade.php ENDPATH**/ ?>