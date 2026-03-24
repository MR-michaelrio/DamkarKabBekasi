<?php $__env->startSection('title', 'Riwayat Perbaikan - ' . $ambulance->code); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Breadcrumbs -->
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm text-gray-500">
            <li>
                <a href="<?php echo e(route('admin.ambulances.index')); ?>" class="hover:text-blue-600">Ambulans</a>
            </li>
            <li>
                <span class="mx-2">/</span>
                <span class="text-gray-800 font-semibold">Riwayat Perbaikan <?php echo e($ambulance->code); ?></span>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            🛠️ Riwayat Perbaikan: <?php echo e($ambulance->code); ?> (<?php echo e($ambulance->plate_number); ?>)
        </h1>
        <button onclick="document.getElementById('addMaintenanceModal').classList.remove('hidden')"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold shadow">
            ➕ Tambah Riwayat
        </button>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <!-- History Table -->
    <div class="bg-white shadow rounded-xl overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr class="text-left text-gray-600 uppercase text-xs">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Jenis</th>
                        <th class="px-6 py-3">Bengkel</th>
                        <th class="px-6 py-3">Biaya</th>
                        <th class="px-6 py-3">Odometer</th>
                        <th class="px-6 py-3">Sparepart</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php $__empty_1 = true; $__currentLoopData = $maintenances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $maintenance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e($maintenance->maintenance_date->format('d M Y')); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="capitalize"><?php echo e($maintenance->maintenance_type); ?></span>
                            </td>
                            <td class="px-6 py-4"><?php echo e($maintenance->workshop); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                Rp <?php echo e(number_format($maintenance->cost, 0, ',', '.')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap"><?php echo e(number_format($maintenance->odometer, 0, ',', '.')); ?> km</td>
                            <td class="px-6 py-4">
                                <?php if($maintenance->spare_parts && count($maintenance->spare_parts) > 0): ?>
                                    <ul class="list-disc list-inside text-xs text-gray-600">
                                        <?php $__currentLoopData = $maintenance->spare_parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($part); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-gray-400 italic text-xs">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="<?php echo e(route('admin.maintenance.destroy', $maintenance)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" onclick="return confirm('Hapus riwayat ini?')" 
                                            class="text-red-600 hover:text-red-800 font-bold">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic text-lg">
                                Belum ada riwayat perbaikan
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Maintenance Modal -->
<div id="addMaintenanceModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b flex justify-between items-center bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800">Tambah Riwayat Perbaikan</h2>
            <button onclick="document.getElementById('addMaintenanceModal').classList.add('hidden')" 
                    class="text-gray-500 hover:text-gray-800 text-2xl font-bold leading-none">&times;</button>
        </div>
        
        <form action="<?php echo e(route('admin.ambulances.maintenance.store', $ambulance)); ?>" method="POST" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Perbaikan</label>
                    <input type="date" name="maintenance_date" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Jenis Perbaikan</label>
                    <input type="text" name="maintenance_type" placeholder="Contoh: Service Rutin, Ganti Ban" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Bengkel</label>
                    <input type="text" name="workshop" placeholder="Masukkan nama bengkel" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Biaya (Rp)</label>
                    <input type="number" name="cost" placeholder="0" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Odometer (km)</label>
                    <input type="number" name="odometer" placeholder="0" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">Sparepart Perbaikan</label>
                <div id="spareparts-container" class="space-y-2">
                    <div class="flex gap-2">
                        <input type="text" name="spare_parts[]" placeholder="Nama sparepart" 
                               class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" onclick="removeSparepart(this)" 
                                class="bg-red-50 text-red-600 px-3 py-2 rounded-lg hover:bg-red-100 transition">
                            &times;
                        </button>
                    </div>
                </div>
                <button type="button" onclick="addSparepart()" 
                        class="text-blue-600 text-sm font-bold hover:text-blue-800 transition flex items-center gap-1 mt-2">
                    <span>+</span> Tambah Sparepart
                </button>
            </div>

            <div class="pt-4 border-t flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addMaintenanceModal').classList.add('hidden')"
                        class="px-4 py-2 text-gray-500 font-semibold hover:text-gray-700">Batal</button>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow-md transition">
                    Simpan Riwayat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function addSparepart() {
        const container = document.getElementById('spareparts-container');
        const div = document.createElement('div');
        div.className = 'flex gap-2';
        div.innerHTML = `
            <input type="text" name="spare_parts[]" placeholder="Nama sparepart" 
                   class="flex-1 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="button" onclick="removeSparepart(this)" 
                    class="bg-red-50 text-red-600 px-3 py-2 rounded-lg hover:bg-red-100 transition">
                &times;
            </button>
        `;
        container.appendChild(div);
    }

    function removeSparepart(button) {
        button.parentElement.remove();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/admin/ambulances/maintenance/index.blade.php ENDPATH**/ ?>