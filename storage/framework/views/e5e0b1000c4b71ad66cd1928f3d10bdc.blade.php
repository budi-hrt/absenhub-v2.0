    <div
        x-data="{
            selected: @entangle($attributes->wire('model')),
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
         <div x-ref="labels" {{ $attributes->except(['wire:model', 'wire:model.live'])->class(["tabs tabs-border", $tabsClass]) }}></div>

        <!--  CONTENTS -->
         <div x-ref="contents"></div>

        <!-- ORIGINAL DATA -->
         <div data-tab x-ref="slot">
            {{ $slot }}
         </div>
    </div>