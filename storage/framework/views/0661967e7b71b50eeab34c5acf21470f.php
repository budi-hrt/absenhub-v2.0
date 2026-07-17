<div>
    <?php if($paginator->hasPages()): ?>
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    <?php if($paginator->onFirstPage()): ?>
                        <span class="btn btn-sm btn-disabled"><?php echo __('pagination.previous'); ?></span>
                    <?php else: ?>
                        <button type="button" wire:click="previousPage('<?php echo e($paginator->getPageName()); ?>')" dusk="previousPage<?php echo e($paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName()); ?>.before" class="btn btn-sm">
                            <?php echo __('pagination.previous'); ?>

                        </button>
                    <?php endif; ?>
                </span>
                <span>
                    <?php if($paginator->hasMorePages()): ?>
                        <button type="button" wire:click="nextPage('<?php echo e($paginator->getPageName()); ?>')" dusk="nextPage<?php echo e($paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName()); ?>.before" class="btn btn-sm">
                            <?php echo __('pagination.next'); ?>

                        </button>
                    <?php else: ?>
                        <span class="btn btn-sm btn-disabled"><?php echo __('pagination.next'); ?></span>
                    <?php endif; ?>
                </span>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-base-content/60">
                        <span><?php echo __('Showing'); ?></span>
                        <span class="font-medium"><?php echo e($paginator->firstItem()); ?></span>
                        <span><?php echo __('to'); ?></span>
                        <span class="font-medium"><?php echo e($paginator->lastItem()); ?></span>
                        <span><?php echo __('of'); ?></span>
                        <span class="font-medium"><?php echo e($paginator->total()); ?></span>
                        <span><?php echo __('results'); ?></span>
                    </p>
                </div>
                <div class="join shadow-sm">
                    
                    <?php if($paginator->onFirstPage()): ?>
                        <span class="join-item btn btn-sm btn-disabled">‹</span>
                    <?php else: ?>
                        <button type="button" wire:click="previousPage('<?php echo e($paginator->getPageName()); ?>')" dusk="previousPage<?php echo e($paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName()); ?>.after" class="join-item btn btn-sm">
                            ‹
                        </button>
                    <?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(is_string($element)): ?>
                            <span class="join-item btn btn-sm btn-disabled"><?php echo e($element); ?></span>
                        <?php endif; ?>
                        <?php if(is_array($element)): ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php if($page == $paginator->currentPage()): ?>
                                    <span class="join-item btn btn-sm btn-primary"><?php echo e($page); ?></span>
                                <?php else: ?>
                                    <button type="button" wire:click="gotoPage(<?php echo e($page); ?>, '<?php echo e($paginator->getPageName()); ?>')" class="join-item btn btn-sm" aria-label="<?php echo e(__('Go to page :page', ['page' => $page])); ?>">
                                        <?php echo e($page); ?>

                                    </button>
                                <?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                    
                    <?php if($paginator->hasMorePages()): ?>
                        <button type="button" wire:click="nextPage('<?php echo e($paginator->getPageName()); ?>')" dusk="nextPage<?php echo e($paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName()); ?>.after" class="join-item btn btn-sm">
                            ›
                        </button>
                    <?php else: ?>
                        <span class="join-item btn btn-sm btn-disabled">›</span>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\absenhub-v2.0\resources\views/vendor/livewire/tailwind.blade.php ENDPATH**/ ?>