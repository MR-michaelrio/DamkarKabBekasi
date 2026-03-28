<?php $__env->startSection('title', 'Edit Laporan | Damkar Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            ✏️ Edit Laporan Masyarakat
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Perbarui data laporan kejadian dari masyarakat
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

        <form method="POST" action="<?php echo e(route('admin.patient-requests.update', $patientRequest->id)); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nama Pelapor
                    </label>
                    <input type="text" name="patient_name" required
                           value="<?php echo e(old('patient_name', $patientRequest->patient_name)); ?>"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Nomor HP / WA
                    </label>
                    <input type="text" name="phone"
                           value="<?php echo e(old('phone', $patientRequest->phone)); ?>"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Service Type -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Jenis Kejadian
                    </label>
                    <select name="service_type" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                        <option value="kebakaran" <?php echo e(old('service_type', $patientRequest->service_type) === 'kebakaran' ? 'selected' : ''); ?>>🔥 Kebakaran</option>
                        <option value="rescue" <?php echo e(old('service_type', $patientRequest->service_type) === 'rescue' ? 'selected' : ''); ?>>🚒 Rescue</option>
                    </select>
                </div>

                <!-- Request Date -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Tanggal Kejadian
                    </label>
                    <input type="date" name="request_date" required
                           value="<?php echo e(old('request_date', $patientRequest->request_date->format('Y-m-d'))); ?>"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Jam -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">
                        Jam Kejadian
                    </label>
                    <input type="time" name="pickup_time"
                           value="<?php echo e(old('pickup_time', $patientRequest->pickup_time)); ?>"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                </div>
            </div>

            <!-- Pickup Address -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">
                    Alamat TKP (Lokasi Kejadian)
                </label>
                <textarea name="pickup_address" required rows="3"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"><?php echo e(old('pickup_address', $patientRequest->pickup_address)); ?></textarea>
            </div>

            <!-- Damkar Specific Fields -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Blok</label>
                    <input type="text" name="blok" value="<?php echo e(old('blok', $patientRequest->blok)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">RT</label>
                    <input type="text" name="rt" value="<?php echo e(old('rt', $patientRequest->rt)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">RW</label>
                    <input type="text" name="rw" value="<?php echo e(old('rw', $patientRequest->rw)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan</label>
                    <input type="text" name="kelurahan" value="<?php echo e(old('kelurahan', $patientRequest->kelurahan)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                    <input type="text" name="kecamatan" value="<?php echo e(old('kecamatan', $patientRequest->kecamatan)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nomor</label>
                    <input type="text" name="nomor" value="<?php echo e(old('nomor', $patientRequest->nomor)); ?>" class="w-full border-gray-300 rounded-lg shadow-sm">
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row justify-between gap-4 pt-4 border-t border-gray-100">
                <a href="<?php echo e(route('admin.patient-requests.index')); ?>"
                   class="text-gray-600 hover:text-gray-800 font-bold flex items-center">
                    ← Kembali
                </a>

                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transition transform active:scale-95">
                    Update Laporan
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/patient_requests/edit.blade.php ENDPATH**/ ?>