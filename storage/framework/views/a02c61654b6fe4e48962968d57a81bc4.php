<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak - <?php echo e(config('app.name')); ?></title>

    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-12px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 0.5; }
            80%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .pulse-ring::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            border: 2px solid currentColor;
            animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
    </style>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200 flex items-center justify-center p-4">

    <div class="max-w-lg w-full text-center">

        
        <div class="bg-base-100 shadow-2xl rounded-3xl p-10 md:p-14 border border-base-300/50 relative overflow-hidden">

            
            <div class="absolute -top-16 -right-16 w-48 h-48 bg-error/5 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-primary/5 rounded-full blur-3xl pointer-events-none"></div>

            
            <div class="relative inline-flex items-center justify-center w-24 h-24 bg-error/10 text-error rounded-[1.25rem] mb-8 shadow-sm animate-float">
                <span class="pulse-ring relative inline-flex items-center justify-center w-full h-full rounded-[1.25rem]">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-shield-exclamation'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-12 h-12']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                </span>
            </div>

            
            <h1 class="text-8xl font-black tracking-tighter text-base-content/10 leading-none select-none mb-2">403</h1>

            
            <h2 class="text-2xl font-bold text-base-content -mt-4 mb-3">Akses Ditolak</h2>

            
            <div class="divider w-16 mx-auto my-4 before:bg-base-300 after:bg-base-300"></div>

            
            <p class="text-base-content/60 text-sm mb-8 leading-relaxed max-w-sm mx-auto">
                <?php echo e($exception?->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.'); ?>

            </p>

            
            <?php
                $homeUrl = auth()->check() && auth()->user()->hasRole('karyawan') ? '/dashboard' : '/';
            ?>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <button onclick="window.history.back()" class="btn btn-outline btn-base-content/50 gap-2 flex-1">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-uturn-left'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                    Kembali
                </button>
                <a href="<?php echo e($homeUrl); ?>" class="btn btn-primary gap-2 flex-1 shadow-md hover:shadow-lg transition-shadow">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-home'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                    Ke Beranda
                </a>
            </div>
        </div>

        
        <p class="text-xs text-base-content/30 mt-6">
            &copy; <?php echo e(date('Y')); ?> <?php echo e(config('app.name')); ?>

        </p>
    </div>
</body>

</html>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views/errors/403.blade.php ENDPATH**/ ?>