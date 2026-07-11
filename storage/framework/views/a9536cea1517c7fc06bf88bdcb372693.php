<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(isset($title) ? $title . ' - ' : ''); ?><?php echo e(config('app.name')); ?></title>

    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
        document.addEventListener('livewire:navigated', () => {
            document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
        });
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200">
    <?php echo e($slot); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views/layouts/empty.blade.php ENDPATH**/ ?>