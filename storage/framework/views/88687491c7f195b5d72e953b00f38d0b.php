        <div
            class="hidden"
            x-init="steps.push({ step: '<?php echo e($step); ?>', text: '<?php echo e($text); ?>', classes: '<?php echo e($stepClasses); ?>' <?php if($icon): ?> , icon: <?php echo e(json_encode($iconHTML())); ?>  <?php endif; ?> <?php if($dataContent): ?>, dataContent: '<?php echo e($dataContent); ?>' <?php endif; ?> })"
        ></div>

        <div x-show="current == '<?php echo e($step); ?>'" <?php echo e($attributes->class("px-1")); ?> >
            <?php echo e($slot); ?>

        </div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/d38bf19609e8b6ec798d05f46b2cdfec.blade.php ENDPATH**/ ?>