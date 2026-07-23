<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(isset($title) ? $title . ' - ' . config('app.name') : config('app.name')); ?></title>

    <script>
        document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
        document.addEventListener('livewire:navigated', () => {
            document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || 'emerald');
        });
    </script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="min-h-screen font-sans antialiased bg-base-200">

    
    <?php if (isset($component)) { $__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58 = $attributes; } ?>
<?php $component = Mary\View\Components\Nav::resolve(['sticky' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Nav::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:hidden']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('brand', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-brand'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppBrand::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $attributes = $__attributesOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__attributesOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $component = $__componentOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__componentOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <label for="main-drawer" class="lg:hidden me-3">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-bars-3'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'cursor-pointer']); ?>
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
            </label>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58)): ?>
<?php $attributes = $__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58; ?>
<?php unset($__attributesOriginalc7e9ca4bc90f51d317ff9ec682225f58); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58)): ?>
<?php $component = $__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58; ?>
<?php unset($__componentOriginalc7e9ca4bc90f51d317ff9ec682225f58); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal11da67fd6f50ab34ca1b98cbdd145132 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132 = $attributes; } ?>
<?php $component = Mary\View\Components\Main::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('main'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Main::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        
         <?php $__env->slot('sidebar', null, ['drawer' => 'main-drawer','collapsible' => true,'class' => 'bg-base-100']); ?> 

            
            <?php if (isset($component)) { $__componentOriginalac37604bae5cded3771d6931140b8398 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalac37604bae5cded3771d6931140b8398 = $attributes; } ?>
<?php $component = App\View\Components\AppBrand::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-brand'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppBrand::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'px-5 pt-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $attributes = $__attributesOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__attributesOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalac37604bae5cded3771d6931140b8398)): ?>
<?php $component = $__componentOriginalac37604bae5cded3771d6931140b8398; ?>
<?php unset($__componentOriginalac37604bae5cded3771d6931140b8398); ?>
<?php endif; ?>

            
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('sidebar-menu', []);

$__keyOuter = $__key ?? null;

$__key = null;
$__componentSlots = [];

$__key ??= \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::generateKey('lw-3836950257-0', $__key);

$__html = app('livewire')->mount($__name, $__params, $__key, $__componentSlots);

echo $__html;

unset($__html);
unset($__key);
$__key = $__keyOuter;
unset($__keyOuter);
unset($__name);
unset($__params);
unset($__componentSlots);
unset($__split);
?>

            
            <?php if (isset($component)) { $__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044 = $attributes; } ?>
<?php $component = Mary\View\Components\Menu::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Menu::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <li x-data="{ theme: localStorage.getItem('theme') || 'emerald' }"
                    x-init="$watch('theme', val => { localStorage.setItem('theme', val); document.documentElement.setAttribute('data-theme', val) })">
                    <a href="#" @click.prevent="theme = theme === 'emerald' ? 'dark' : 'emerald'">
                        <span x-show="theme === 'emerald'"><?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-moon'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
<?php endif; ?></span>
                        <span x-show="theme !== 'emerald'"><?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-sun'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5']); ?>
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
<?php endif; ?></span>
                        <span class="mary-hideable" x-text="theme === 'emerald' ? 'Dark Mode' : 'Light Mode'"></span>
                    </a>
                </li>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044)): ?>
<?php $attributes = $__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044; ?>
<?php unset($__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044)): ?>
<?php $component = $__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044; ?>
<?php unset($__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>

        
         <?php $__env->slot('content', null, []); ?> 
            <?php echo e($slot); ?>

         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132)): ?>
<?php $attributes = $__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132; ?>
<?php unset($__attributesOriginal11da67fd6f50ab34ca1b98cbdd145132); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal11da67fd6f50ab34ca1b98cbdd145132)): ?>
<?php $component = $__componentOriginal11da67fd6f50ab34ca1b98cbdd145132; ?>
<?php unset($__componentOriginal11da67fd6f50ab34ca1b98cbdd145132); ?>
<?php endif; ?>

    
    <?php if (isset($component)) { $__componentOriginal2aca76be1376419dfd37220f36011753 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aca76be1376419dfd37220f36011753 = $attributes; } ?>
<?php $component = Mary\View\Components\Toast::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Toast::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $attributes = $__attributesOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__attributesOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $component = $__componentOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__componentOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>

    
    <div class="dock lg:hidden bg-base-100 border-t border-base-200 z-50">
        <a href="<?php echo e(auth()->user()?->hasRole('karyawan') ? route('dashboard') : '/'); ?>" wire:navigate class="<?php echo e(request()->routeIs('dashboard') || request()->is('/') ? 'dock-active text-primary' : 'text-base-content/60'); ?>">
            <svg class="size-5 md:size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><polyline points="1 11 12 2 23 11" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline><path d="m5,13v7c0,1.105.895,2,2,2h10c1.105,0,2-.895,2-2v-7" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path><line x1="12" y1="22" x2="12" y2="18" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></line></g></svg>
            <span class="dock-label text-[10px] md:text-xs">Home</span>
        </a>
        
        <a href="<?php echo e(auth()->user()?->hasRole('karyawan') ? route('karyawan.riwayat') : '#'); ?>" wire:navigate class="<?php echo e(request()->routeIs('karyawan.riwayat') ? 'dock-active text-primary' : 'text-base-content/60 hover:text-primary transition-colors'); ?>">
            <svg class="size-5 md:size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><polyline points="3 14 9 14 9 17 15 17 15 14 21 14" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline><rect x="3" y="3" width="18" height="18" rx="2" ry="2" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></rect></g></svg>
            <span class="dock-label text-[10px] md:text-xs">Riwayat</span>
        </a>
        
        <a href="<?php echo e(auth()->user()?->hasRole('karyawan') ? route('karyawan.profile') : '#'); ?>" wire:navigate class="<?php echo e(request()->routeIs('karyawan.profile') ? 'dock-active text-primary' : 'text-base-content/60 hover:text-primary transition-colors'); ?>">
            <svg class="size-5 md:size-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></circle><path d="m22,13.25v-2.5l-2.318-.966c-.167-.581-.395-1.135-.682-1.654l.954-2.318-1.768-1.768-2.318.954c-.518-.287-1.073-.515-1.654-.682l-.966-2.318h-2.5l-.966,2.318c-.581.167-1.135.395-1.654.682l-2.318-.954-1.768,1.768.954,2.318c-.287.518-.515,1.073-.682,1.654l-2.318.966v2.5l2.318.966c.167.581.395,1.135.682,1.654l-.954,2.318,1.768,1.768,2.318-.954c.518.287,1.073.515,1.654.682l.966,2.318h2.5l.966-2.318c.581-.167,1.135-.395,1.654-.682l2.318.954,1.768-1.768-.954-2.318c.287-.518.515-1.073.682-1.654l2.318-.966Z" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path></g></svg>
            <span class="dock-label text-[10px] md:text-xs">Profile</span>
        </a>
    </div>
</body>

</html><?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\layouts\app.blade.php ENDPATH**/ ?>