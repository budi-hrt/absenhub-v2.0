<div>
    <fieldset class="fieldset">
        <div class="w-full">
            <label class="<?php echo \Illuminate\Support\Arr::toCssClasses(["flex gap-3 items-center cursor-pointer", "justify-between" => $right, "!items-start" => $hint]); ?>">

                
                <input
                    id="<?php echo e($uuid); ?>"
                    type="checkbox"
                    <?php echo e($attributes->whereDoesntStartWith("id")
                            ->class(["order-2" => $right])
                            ->merge(["class" => "checkbox"])); ?>

                />

                
                 <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(["order-1" => $right]); ?>">
                    <div class="text-sm font-medium">
                        <?php echo e($label); ?>


                        <?php if($attributes->get('required')): ?>
                            <span class="text-error">*</span>
                        <?php endif; ?>
                    </div>

                    
                    <?php if($hint): ?>
                        <div class="<?php echo e($hintClass); ?>" x-classes="fieldset-label"><?php echo e($hint); ?></div>
                    <?php endif; ?>
                </div>
            </label>
        </div>

        
        <?php if(!$omitError && $errors->has($errorFieldName())): ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->get($errorFieldName()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = Arr::wrap($message); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $line): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="<?php echo e($errorClass); ?>" x-class="text-error"><?php echo e($line); ?></div>
                    <?php if($firstErrorOnly) break; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if($firstErrorOnly) break; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        <?php endif; ?>
    </fieldset>
</div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/b071c2e8544fe1dc984c95339b974ca0.blade.php ENDPATH**/ ?>