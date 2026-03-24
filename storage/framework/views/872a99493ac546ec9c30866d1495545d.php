<?php $__env->startSection('title', 'Laporan Masyarakat | Damkar Admin'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
            📋 Laporan Masyarakat
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            Kelola laporan kejadian dari masyarakat
        </p>
    </div>

    <!-- Success Message -->
    <?php if(session('success')): ?>
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- Requests Table -->
    <div class="bg-white shadow rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                            <a href="<?php echo e(route('admin.patient-requests.index', ['direction' => ($direction === 'asc' ? 'desc' : 'asc')])); ?>" class="flex items-center gap-1 hover:text-blue-600 transition">
                                Tanggal
                                <?php if($direction === 'asc'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                <?php endif; ?>
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Pelapor</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Layanan</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Kondisi</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                <?php echo e($request->request_date->format('d/m/Y')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                <?php echo e($request->patient_name); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                <?php if($request->service_type === 'kebakaran'): ?>
                                    🔥 Kebakaran
                                <?php elseif($request->service_type === 'rescue'): ?>
                                    🚒 Rescue
                                <?php else: ?>
                                    <?php echo e(strtoupper($request->service_type)); ?>

                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                <?php if($request->patient_condition === 'emergency'): ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">
                                        🚨 Emergency
                                    </span>
                                <?php elseif($request->patient_condition === 'kontrol'): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">
                                        🏥 Kontrol
                                    </span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <?php if($request->status === 'pending'): ?>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold">
                                        ⏳ Pending
                                    </span>
                                <?php elseif($request->status === 'dispatched'): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">
                                        ✅ Dispatched
                                    </span>
                                <?php elseif($request->status === 'completed'): ?>
                                    <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">
                                        🏁 Selesai
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">
                                        ❌ Rejected
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm flex items-center gap-3">
                                <a href="<?php echo e(route('admin.patient-requests.show', $request)); ?>"
                                   class="text-blue-600 hover:text-blue-800 font-bold">
                                    Lihat
                                </a>
                                <a href="<?php echo e(route('admin.patient-requests.edit', $request)); ?>"
                                   class="text-amber-600 hover:text-amber-800 font-bold">
                                    Edit
                                </a>
                                <form action="<?php echo e(route('admin.patient-requests.destroy', $request)); ?>" method="POST" onsubmit="return confirm('Yakin ingin menghapus permintaan ini?')" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </form>
                                <?php if($request->status === 'pending'): ?>
                                    <a href="<?php echo e(route('admin.patient-requests.create-dispatch', $request)); ?>"
                                       class="text-green-600 hover:text-green-800 font-bold">
                                        Dispatch
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                                Belum ada laporan masuk
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/patient_requests/index.blade.php ENDPATH**/ ?>