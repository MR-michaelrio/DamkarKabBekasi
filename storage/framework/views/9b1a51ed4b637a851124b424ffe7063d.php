<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-800 tracking-tight">
            📅 Permintaan & Kegiatan Event
        </h1>
        <a href="<?php echo e(route('admin.event-requests.create')); ?>" 
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 sm:px-4 py-2 rounded-lg font-bold shadow-sm transition transform active:scale-95 text-sm sm:text-base whitespace-nowrap w-full sm:w-auto text-center">
            + Tambah Event
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 font-medium rounded-r-lg shadow-sm">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="bg-white shadow rounded-xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Nama Event</th>
                    <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Waktu</th>
                    <th class="px-3 sm:px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                    <th class="px-3 sm:px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="font-bold text-gray-900 flex items-center gap-2">
                                <?php if($event->type === 'disaster'): ?>
                                    <span class="text-[9px] px-1.5 py-0.5 bg-orange-100 text-orange-600 rounded font-black uppercase">🚨 BENCANA</span>
                                <?php else: ?>
                                    <span class="text-[9px] px-1.5 py-0.5 bg-pink-100 text-pink-600 rounded font-black uppercase">🎪 EVENT</span>
                                <?php endif; ?>
                                <?php echo e($event->event_name); ?>

                            </div>
                            <div class="text-xs text-gray-400 mt-1 truncate max-w-[150px] sm:max-w-xs"><?php echo e($event->needs); ?></div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-700">
                                <?php echo e($event->start_date->format('d M Y')); ?> - <?php echo e($event->end_date->format('d M Y')); ?>

                            </div>
                            <div class="text-[10px] text-gray-400 uppercase font-black mt-1">
                                <?php echo e($event->start_date->diffInDays($event->end_date) + 1); ?> Hari
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <?php if($event->status === 'pending'): ?>
                                <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full text-[10px] font-black uppercase tracking-wider">Pending</span>
                            <?php elseif($event->status === 'approved'): ?>
                                <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-[10px] font-black uppercase tracking-wider">Disetujui</span>
                            <?php else: ?>
                                <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-[10px] font-black uppercase tracking-wider">Ditolak</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 sm:px-6 py-4 text-right">
                            <div class="flex flex-wrap justify-end gap-2 shrink-0">
                                <?php if($event->status === 'pending'): ?>
                                <form action="<?php echo e(route('admin.event-requests.approve', $event)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="text-green-600 hover:text-green-800 font-bold text-xs uppercase tracking-tighter">Approve</button>
                                </form>
                                <form action="<?php echo e(route('admin.event-requests.reject', $event)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button class="text-orange-600 hover:text-orange-800 font-bold text-xs uppercase tracking-tighter">Reject</button>
                                </form>
                            <?php endif; ?>
                            <a href="<?php echo e(route('admin.event-requests.show', $event)); ?>" class="text-emerald-600 hover:text-emerald-800 font-bold text-xs uppercase tracking-tighter">Detail</a>
                            <a href="<?php echo e(route('admin.event-requests.edit', $event)); ?>" class="text-blue-600 hover:text-blue-800 font-bold text-xs uppercase tracking-tighter">Edit</a>
                            <form action="<?php echo e(route('admin.event-requests.destroy', $event)); ?>" method="POST" class="inline" onsubmit="return confirm('Hapus event ini?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="text-red-600 hover:text-red-800 font-bold text-xs uppercase tracking-tighter">Hapus</button>
                            </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <div class="text-4xl mb-2">🗓️</div>
                            Belum ada permintaan event.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/admin/event_requests/index.blade.php ENDPATH**/ ?>