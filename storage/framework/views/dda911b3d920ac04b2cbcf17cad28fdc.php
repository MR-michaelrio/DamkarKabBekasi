<?php $__env->startSection('title', 'Detail Permintaan | GMCI Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            📋 Detail Permintaan Pasien
        </h1>
    </div>

    <!-- Request Details Card -->
    <div class="bg-white shadow rounded-xl p-6 space-y-6 border border-gray-100">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Nama Pasien</label>
                <p class="text-lg font-bold text-gray-900 mt-1"><?php echo e($patientRequest->patient_name); ?></p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Tanggal</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    <?php echo e($patientRequest->request_date->format('d F Y')); ?>

                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Jam Penjemputan</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    <?php echo e($patientRequest->pickup_time ? \Carbon\Carbon::parse($patientRequest->pickup_time)->format('H:i') : '-'); ?> WIB
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Tipe Perjalanan</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    <?php if($patientRequest->trip_type === 'round_trip'): ?>
                        🔄 Pulang Pergi (PP)
                    <?php else: ?>
                        ➡️ Sekali Jalan
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-50 pt-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Jenis Layanan</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    <?php if($patientRequest->service_type === 'ambulance'): ?>
                        🚑 Pasien (Ambulance)
                    <?php else: ?>
                        ⚰️ Jenazah (Mobil Jenazah)
                    <?php endif; ?>
                </p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Kondisi / Status</label>
                <p class="text-lg font-bold text-gray-900 mt-1">
                    <?php if($patientRequest->patient_condition === 'emergency'): ?>
                        <span class="text-red-600">🚨 EMERGENCY</span>
                    <?php elseif($patientRequest->patient_condition === 'kontrol'): ?>
                        <span class="text-blue-600">🏥 KONTROL</span>
                    <?php elseif($patientRequest->patient_condition === 'pasien_pulang'): ?>
                        <span class="text-emerald-600">🏠 PULANG</span>
                    <?php else: ?>
                        <span class="text-gray-400">-</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 border-t border-gray-50 pt-6">
            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">No. Telepon</label>
                <p class="text-lg font-bold text-gray-900 mt-1"><?php echo e($patientRequest->phone); ?></p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Status</label>
                <div class="mt-2">
                    <?php if($patientRequest->status === 'pending'): ?>
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded text-sm font-bold shadow-sm">
                            ⏳ Pending
                        </span>
                    <?php elseif($patientRequest->status === 'dispatched'): ?>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-bold shadow-sm">
                            ✅ Dispatched
                        </span>
                    <?php elseif($patientRequest->status === 'completed'): ?>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-sm font-bold shadow-sm">
                            🏁 Selesai
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded text-sm font-bold shadow-sm">
                            ❌ Rejected
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-50 pt-6">
            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Alamat Jemput</label>
            <p class="text-gray-900 mt-2 bg-gray-50 p-3 rounded-lg border border-gray-100"><?php echo e($patientRequest->pickup_address); ?></p>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Tujuan</label>
            <p class="text-gray-900 mt-2 bg-gray-50 p-3 rounded-lg border border-gray-100"><?php echo e($patientRequest->destination); ?></p>
        </div>

        <?php if($patientRequest->dispatch_id): ?>
            <div class="border-t border-gray-50 pt-6">
                <label class="block text-sm font-bold text-gray-500 uppercase tracking-wider">Dispatch ID</label>
                <p class="text-xl font-bold text-blue-600 mt-1">
                    #<?php echo e($patientRequest->dispatch_id); ?>

                </p>
            </div>
        <?php endif; ?>

    </div>

    <!-- Actions -->
    <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
        <a href="<?php echo e(route('admin.patient-requests.index')); ?>"
           class="text-gray-600 hover:text-gray-800 font-bold flex items-center w-full sm:w-auto">
            ← Kembali
        </a>

        <?php if($patientRequest->status === 'pending'): ?>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="<?php echo e(route('admin.patient-requests.create-dispatch', $patientRequest)); ?>"
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg text-center transition transform active:scale-95">
                    ✅ Buat Dispatch
                </a>

                <form method="POST" action="<?php echo e(route('admin.patient-requests.reject', $patientRequest)); ?>"
                      class="inline"
                      onsubmit="return confirm('Yakin ingin menolak permintaan ini?')">
                    <?php echo csrf_field(); ?>
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold shadow-lg w-full transition transform active:scale-95">
                        ❌ Tolak
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/patient_requests/show.blade.php ENDPATH**/ ?>