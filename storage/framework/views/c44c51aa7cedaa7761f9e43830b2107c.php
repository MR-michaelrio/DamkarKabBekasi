<?php $__env->startSection('title', 'Edit Tipe Armada | Damkar Dispatch'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            ✏️ Edit Tipe Armada Damkar: <?php echo e($ambulanceType->name); ?>

        </h1>
    </div>

    <div class="bg-white shadow rounded-xl p-6">
        <form action="<?php echo e(route('admin.ambulance-types.update', $ambulanceType)); ?>" method="POST" class="space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Tipe</label>
                <input type="text" name="name" value="<?php echo e(old('name', $ambulanceType->name)); ?>" required
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="pt-4 border-t flex justify-end gap-3">
                <a href="<?php echo e(route('admin.ambulance-types.index')); ?>"
                   class="px-4 py-2 text-gray-500 font-semibold hover:text-gray-700">
                    Batal
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/ambulance_types/edit.blade.php ENDPATH**/ ?>