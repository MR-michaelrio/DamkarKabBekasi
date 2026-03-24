<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($title); ?></title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ed1c24; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #ed1c24; font-size: 20px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        
        .section-title { background: #f4f4f4; padding: 5px 10px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; border-left: 4px solid #ed1c24; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        
        .status { font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .badge { padding: 2px 5px; border-radius: 3px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 5px; }
        
        .analytics-grid { margin-bottom: 20px; }
        .analytics-item { display: inline-block; width: 30%; border: 1px solid #eee; padding: 10px; margin-right: 10px; margin-bottom: 10px; vertical-align: top; }
        .analytics-count { font-size: 18px; font-weight: bold; color: #2d3748; }
        .analytics-label { font-size: 9px; color: #718096; text-transform: uppercase; }

        .sunday-section { background: #fff5f5; border: 1px solid #feb2b2; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h1><?php echo e($title); ?></h1>
    <p>GMCI AMBULANCE DISPATCH SYSTEM</p>
    <p>Periode: 
        <?php if($range === 'today'): ?> <?php echo e(now()->format('d F Y')); ?>

        <?php elseif($range === 'week'): ?> <?php echo e(now()->startOfWeek()->format('d M')); ?> - <?php echo e(now()->endOfweek()->format('d M Y')); ?>

        <?php elseif($range === 'month'): ?> <?php echo e(now()->format('F Y')); ?>

        <?php else: ?> Semua Waktu
        <?php endif; ?>
    </p>
</div>

<div class="section-title">📊 ANALITIK PER MOBIL (PENGGUNAAN)</div>
<div class="analytics-grid">
    <?php $__currentLoopData = $analytics; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="analytics-item">
        <div class="analytics-label"><?php echo e($a->plate_number); ?></div>
        <div class="analytics-count"><?php echo e($a->dispatches_count); ?></div>
        <div class="analytics-label">KALI KELUAR</div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php if($sundayDispatches->isNotEmpty()): ?>
<div class="section-title">☀️ RINGKASAN HARI MINGGU</div>
<div class="sunday-section">
    <p>Total Dispatch pada Hari Minggu: <strong><?php echo e($sundayDispatches->count()); ?></strong></p>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Pasien</th>
                <th>Ambulans</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $sundayDispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($sd->created_at->format('d M Y')); ?></td>
                <td><?php echo e($sd->patient_name); ?></td>
                <td><?php echo e($sd->ambulance?->plate_number); ?></td>
                <td><?php echo e($sd->status); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="section-title">📋 DAFTAR DISPATCH</div>
<table>
    <thead>
        <tr>
            <th>Waktu</th>
            <th>Pasien</th>
            <th>Ambulans</th>
            <th>Driver</th>
            <th>Tujuan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $dispatches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($d->created_at->format('d/m H:i')); ?></td>
            <td>
                <strong><?php echo e($d->patient_name); ?></strong><br>
                <small><?php echo e($d->patient_condition); ?></small>
            </td>
            <td><?php echo e($d->ambulance?->plate_number ?? '-'); ?></td>
            <td><?php echo e($d->driver?->name ?? '-'); ?></td>
            <td><?php echo e($d->destination ?? '-'); ?></td>
            <td><span class="status"><?php echo e(str_replace('_', ' ', $d->status)); ?></span></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="6" style="text-align: center;">Tidak ada data ditemukan</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    Dicetak pada: <?php echo e(now()->format('d-m-Y H:i:s')); ?> | GMCI Ambulance Dispatch
</div>

</body>
</html>
<?php /**PATH /Applications/Dev/ambulance-dispatch/resources/views/admin/dispatches/dashboard_pdf.blade.php ENDPATH**/ ?>