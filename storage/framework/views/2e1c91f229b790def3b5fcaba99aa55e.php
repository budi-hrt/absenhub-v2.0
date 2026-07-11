<div class="mary-table-pagination">
    <div <?php echo e($attributes->class(["mb-4 border-t-[length:var(--border)] border-t-base-content/5"])); ?>></div>
    <div class="justify-between md:flex md:flex-row w-auto md:w-full items-center overflow-y-auto ps-2 pe-2 relative">
        <?php if($isShowable()): ?>
        <div class="flex flex-row justify-center md:justify-start mb-2 md:mb-0 py-1">
            <select id="<?php echo e($uuid); ?>" <?php if(!empty($modelName())): ?> wire:model.live="<?php echo e($modelName()); ?>" <?php endif; ?>
                    class="select select-sm flex sm:text-sm sm:leading-6 w-auto md:mr-5">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $perPageValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <option value="<?php echo e($option); ?>" <?php if($rows->perPage() === $option): echo 'selected'; endif; ?>><?php echo e($option); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="w-full">
        <?php if($rows instanceof LengthAwarePaginator): ?>
            <?php echo e($rows->onEachSide(1)->links(data: ['scrollTo' => false])); ?>

        <?php else: ?>
            <?php echo e($rows->links(data: ['scrollTo' => false])); ?>

        <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/eafb32841241207db0a9ac5073f7bc7a.blade.php ENDPATH**/ ?>