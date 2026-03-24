<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            🚒 Dispatch Unit Damkar
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Buat penugasan unit damkar baru berdasarkan laporan
        </p>
    </div>

    <form method="POST" action="<?php echo e(route('admin.dispatches.store')); ?>"
          class="bg-white p-6 rounded-xl shadow border border-gray-100 space-y-6">
        <?php echo csrf_field(); ?>

        <?php if(isset($patientRequest) && $patientRequest): ?>
            <input type="hidden" name="patient_request_id" value="<?php echo e($patientRequest->id); ?>">
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pelapor</label>
                <input type="text" name="patient_name" required 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="<?php echo e(old('patient_name', $patientRequest->patient_name ?? '')); ?>">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">No HP Pelapor</label>
                <input type="text" name="patient_phone" 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="<?php echo e(old('patient_phone', $patientRequest->phone ?? '')); ?>">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Kejadian</label>
                <input type="date" name="request_date" required 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="<?php echo e(old('request_date', isset($patientRequest) ? $patientRequest->request_date->format('Y-m-d') : date('Y-m-d'))); ?>">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Jam Kejadian</label>
                <input type="time" name="pickup_time" required 
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="<?php echo e(old('pickup_time', $patientRequest->pickup_time ?? '')); ?>">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tipe Kejadian</label>
                <select name="patient_condition" id="patient_condition" required 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="kebakaran" <?php echo e(old('patient_condition', $patientRequest->service_type ?? '') === 'kebakaran' ? 'selected' : ''); ?>>🔥 Kebakaran</option>
                    <option value="rescue" <?php echo e(old('patient_condition', $patientRequest->service_type ?? '') === 'rescue' ? 'selected' : ''); ?>>🚒 Rescue</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Laporan</label>
                <input type="text" name="nomor" placeholder="Contoh: 001/Laporan"
                       class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500"
                       value="<?php echo e(old('nomor', $patientRequest->nomor ?? '')); ?>">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-gray-50 pt-6">
            <div class="md:col-span-3">
                <label class="block text-sm font-bold text-gray-700 mb-1">Alamat TKP (Lokasi Kejadian)</label>
                <textarea name="pickup_address" required rows="2"
                          class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 font-bold text-red-600"><?php echo e(old('pickup_address', $patientRequest->pickup_address ?? '')); ?></textarea>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Blok</label>
                <input type="text" name="blok" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="<?php echo e(old('blok', $patientRequest->blok ?? '')); ?>">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">RT</label>
                <input type="text" name="rt" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="<?php echo e(old('rt', $patientRequest->rt ?? '')); ?>">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">RW</label>
                <input type="text" name="rw" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="<?php echo e(old('rw', $patientRequest->rw ?? '')); ?>">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan</label>
                <input type="text" name="kelurahan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="<?php echo e(old('kelurahan', $patientRequest->kelurahan ?? '')); ?>">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                <input type="text" name="kecamatan" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500" value="<?php echo e(old('kecamatan', $patientRequest->kecamatan ?? '')); ?>">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-50 pt-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Petugas (Driver)</label>
                <select name="driver_id" required 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Petugas --</option>
                    <?php $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->id); ?>"><?php echo e($d->name); ?> (<?php echo e($d->status); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Unit Mobil Damkar</label>
                <select name="ambulance_id" required 
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Pilih Unit --</option>
                    <?php $__currentLoopData = $ambulances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a->id); ?>"><?php echo e($a->plate_number); ?> (<?php echo e($a->status); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 pt-6">
            <a href="<?php echo e(route('admin.dispatches.index')); ?>" 
               class="text-gray-600 hover:text-gray-800 font-bold w-full sm:w-auto text-center">
                ← Batal
            </a>
            <button class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg w-full sm:w-auto transition transform active:scale-95">
                🚒 Kirim Unit Damkar
            </button>
        </div>

    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/dispatches/create.blade.php ENDPATH**/ ?>