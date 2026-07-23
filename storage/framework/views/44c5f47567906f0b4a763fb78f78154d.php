<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<div>
    <?php if (isset($component)) { $__componentOriginal5a2f10112e92a9c01ae3ba423b1cc044 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5a2f10112e92a9c01ae3ba423b1cc044 = $attributes; } ?>
<?php $component = Mary\View\Components\Menu::resolve(['activateByRoute' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Menu::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


        
        <?php if($user = auth()->user()): ?>
        <?php if (isset($component)) { $__componentOriginal254139bd69d0def79ecb6c40efbc400d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal254139bd69d0def79ecb6c40efbc400d = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSeparator::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSeparator::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $attributes = $__attributesOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $component = $__componentOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__componentOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418 = $attributes; } ?>
<?php $component = Mary\View\Components\ListItem::resolve(['item' => $user,'value' => 'name','subValue' => 'email','noSeparator' => true,'noHover' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('list-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\ListItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => '-mx-2 !-my-2 rounded']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

             <?php $__env->slot('actions', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-power','tooltipLeft' => 'logoff'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-circle btn-ghost btn-xs','onclick' => 'event.preventDefault(); document.getElementById(\'logout-form\').submit();']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST"
                    class="hidden">
                    <?php echo csrf_field(); ?>
                </form>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418)): ?>
<?php $attributes = $__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418; ?>
<?php unset($__attributesOriginal8653fe0e2b5ee7b7ab3811c66ab90418); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418)): ?>
<?php $component = $__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418; ?>
<?php unset($__componentOriginal8653fe0e2b5ee7b7ab3811c66ab90418); ?>
<?php endif; ?>

        <?php if (isset($component)) { $__componentOriginal254139bd69d0def79ecb6c40efbc400d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal254139bd69d0def79ecb6c40efbc400d = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSeparator::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-separator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSeparator::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $attributes = $__attributesOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__attributesOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal254139bd69d0def79ecb6c40efbc400d)): ?>
<?php $component = $__componentOriginal254139bd69d0def79ecb6c40efbc400d; ?>
<?php unset($__componentOriginal254139bd69d0def79ecb6c40efbc400d); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'karyawan')): ?>
        <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Dashboard','icon' => 'o-home','link' => '/dashboard'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Riwayat Absen','icon' => 'o-document-text','link' => '/riwayat'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Pengajuan','icon' => 'o-paper-airplane','link' => '/pengajuan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Profil Saya','icon' => 'o-user','link' => '/profile'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
        <?php else: ?>
        <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Dashboard','icon' => 'o-home','link' => '/'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|super-admin|operator|manager')): ?>
        <?php if (isset($component)) { $__componentOriginald82092fa13795886cb51cb7dc7d7b48e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSub::resolve(['title' => 'Manajemen Users','icon' => 'o-users'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-sub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'lihat-admin')): ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Data Users','icon' => 'o-user','iconClasses' => 'text-primary','link' => '/users','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if (\Illuminate\Support\Facades\Blade::check('haspermission', 'lihat-user-karyawan')): ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Users Karyawan','icon' => 'o-users','iconClasses' => 'text-warning','link' => '/users-karyawan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $attributes = $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $component = $__componentOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|super-admin|operator|manager')): ?>
        <?php if (isset($component)) { $__componentOriginald82092fa13795886cb51cb7dc7d7b48e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSub::resolve(['title' => 'Master Data','icon' => 'o-circle-stack'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-sub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Jabatan','icon' => 'o-briefcase','exact' => true,'link' => '/master/jabatan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Status Kerja','icon' => 'o-check-badge','exact' => true,'link' => '/master/status-kerja'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Masa Kontrak','icon' => 'o-clock','exact' => true,'link' => '/master/masa-kontrak'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Penandatangan','icon' => 'o-pencil-square','exact' => true,'link' => '/master/penandatangan'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $attributes = $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $component = $__componentOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|super-admin|operator|manager')): ?>
        <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Data Karyawan','icon' => 'o-users','iconClasses' => 'text-success','link' => '/karyawan','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Kontrak Kerja','icon' => 'o-document-text','iconClasses' => 'text-warning','link' => '/kontrak','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|super-admin|operator|manager')): ?>
        <?php if (isset($component)) { $__componentOriginald82092fa13795886cb51cb7dc7d7b48e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSub::resolve(['title' => 'Pengajuan Cuti/Izin','icon' => 'o-paper-airplane'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-sub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Kelola Pengajuan','icon' => 'o-check-badge','iconClasses' => 'text-warning','link' => '/pengajuan/kelola','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $attributes = $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $component = $__componentOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|super-admin|operator|manager')): ?>
        <?php if (isset($component)) { $__componentOriginald82092fa13795886cb51cb7dc7d7b48e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSub::resolve(['title' => 'Manajemen Absensi','icon' => 'o-calendar-days'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-sub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Kelola Absensi','icon' => 'o-pencil-square','iconClasses' => 'text-primary','link' => '/absen/kelola','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Lihat Absensi','icon' => 'o-eye','iconClasses' => 'text-success','link' => '/absen/lihat','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Detail Harian','icon' => 'o-document-text','iconClasses' => 'text-info','link' => '/absen/detail-harian','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Rekap Bulanan','icon' => 'o-table-cells','iconClasses' => 'text-accent','link' => '/absen/rekap-bulanan','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Rekap Tahunan','icon' => 'o-calendar','iconClasses' => 'text-secondary','link' => '/absen/rekap-tahunan','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Performa Karyawan','icon' => 'o-chart-bar-square','iconClasses' => 'text-primary','link' => '/absen/performa','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Laporan Bulanan','icon' => 'o-chart-bar','iconClasses' => 'text-error','link' => '/absen/laporan-bulanan','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $attributes = $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $component = $__componentOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'admin|super-admin|operator|manager')): ?>
        <?php if (isset($component)) { $__componentOriginald82092fa13795886cb51cb7dc7d7b48e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSub::resolve(['title' => 'Pengaturan','icon' => 'o-cog-6-tooth'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-sub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Profil Saya','icon' => 'o-user','iconClasses' => 'text-info','link' => '/profile','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Jam Kerja','icon' => 'o-clock','iconClasses' => 'text-warning','link' => '/pengaturan/absen','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Lokasi Kantor','icon' => 'o-map-pin','iconClasses' => 'text-primary','link' => '/pengaturan/lokasi','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Jatah Cuti','icon' => 'o-calendar-days','iconClasses' => 'text-success','link' => '/pengajuan/jatah-cuti','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $attributes = $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $component = $__componentOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
        <?php endif; ?>

        
        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'super-admin')): ?>
        <?php if (isset($component)) { $__componentOriginald82092fa13795886cb51cb7dc7d7b48e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e = $attributes; } ?>
<?php $component = Mary\View\Components\MenuSub::resolve(['title' => 'Super Admin','icon' => 'o-shield-check'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-sub'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuSub::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Roles','icon' => 'o-user-group','iconClasses' => 'text-primary','link' => '/roles','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Permissions','icon' => 'o-key','iconClasses' => 'text-warning','link' => '/permissions','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879 = $attributes; } ?>
<?php $component = Mary\View\Components\MenuItem::resolve(['title' => 'Feature Flags','icon' => 'o-flag','iconClasses' => 'text-success','link' => '/feature-flags','exact' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('menu-item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\MenuItem::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $attributes = $__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__attributesOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879)): ?>
<?php $component = $__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879; ?>
<?php unset($__componentOriginal7c3255ff27a5c6d076ca64dbcfc1f879); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $attributes = $__attributesOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__attributesOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e)): ?>
<?php $component = $__componentOriginald82092fa13795886cb51cb7dc7d7b48e; ?>
<?php unset($__componentOriginald82092fa13795886cb51cb7dc7d7b48e); ?>
<?php endif; ?>
        <?php endif; ?>

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
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views\components\⚡sidebar-menu.blade.php ENDPATH**/ ?>