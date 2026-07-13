    <div
        x-data="{
            selected: <?php if ((object) ($attributes->wire('model')) instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')->value()); ?>')<?php echo e($attributes->wire('model')->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e($attributes->wire('model')); ?>')<?php endif; ?>,
            init() {
                this.refresh();
                Livewire.hook('morphed', ({ el }) => this.refresh() );
            },
            refresh() {
                Array.from($refs.slot?.children ?? [])?.forEach(tab => {
                    const label = tab.querySelector('label');
                    const content = tab.querySelector('.tab-content');

                    if (label) {
                        $refs.labels.appendChild(label);
                    }

                    if (content) {
                        $refs.contents.appendChild(content);
                    }
                });
            }
        }"
        x-class="scrollbar-none flex-nowrap overflow-x-auto"
    >
        <!-- TABS -->
         <div x-ref="labels" <?php echo e($attributes->except(['wire:model', 'wire:model.live'])->class(["tabs tabs-border", $tabsClass])); ?>></div>

        <!--  CONTENTS -->
         <div x-ref="contents"></div>

        <!-- ORIGINAL DATA -->
         <div data-tab x-ref="slot">
            <?php echo e($slot); ?>

         </div>
    </div><?php /**PATH C:\laragon\www\absenhub-v2.0\storage\framework\views/e5e0b1000c4b71ad66cd1928f3d10bdc.blade.php ENDPATH**/ ?>