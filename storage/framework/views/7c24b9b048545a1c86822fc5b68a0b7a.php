<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo $__env->yieldContent('title', 'Damkar Kabupaten Bekasi'); ?></title>

    <!-- Tailwind CSS (CDN, tanpa Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (Core) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Favicon -->
    <link rel="icon" href="<?php echo e(asset('logo-damkar.png')); ?>" type="image/png">
</head>

<body class="font-sans antialiased bg-gray-50 min-h-screen">

    
    <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

</body>
</html>
<?php /**PATH /Applications/Dev/damkar-dispatch/resources/views/layouts/app.blade.php ENDPATH**/ ?>