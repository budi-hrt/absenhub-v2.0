    <div
            x-data="{
                    steps: [],
                    current: <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?>,
                    init() {
                        // Fix weird issue when navigating back
                        document.addEventListener('livewire:navigating', () => {
                            document.querySelectorAll('.step').forEach(el =>  el.remove());
                        });
                    }
            }"
        >
            <!-- STEP LABELS -->
            <ul class="steps [&>*:nth-child(2)]:before:hidden <?php echo e($stepperClasses); ?>">
                <template x-for="(step, index) in steps" :key="index">
                    <li
                        class="step"
                        :data-content="!step.icon ? step.dataContent || (index + 1) : ''"
                        :class="(index + 1 <= current) && '<?php echo e($stepsColor); ?> ' + step.classes"
                    >
                            <template x-if="step.icon">
                                <span x-html="step.icon" class="step-icon"></span>
                            </template>
                            <span x-html="step.text"></span>
                    </li>
                </template>
            </ul>

            <!-- STEP PANELS-->
            <div <?php echo e($attributes->whereDoesntStartWith('wire')); ?>>
                <?php echo e($slot); ?>

            </div>

            <!-- Force Tailwind compile steps color -->
            <span class="hidden step-primary step-error step-success step-neutral step-info step-accent"></span>
        </div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/5e14a48e6226c55a5db139f3badddb7e.blade.php ENDPATH**/ ?>