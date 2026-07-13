    @aware(['lift', 'box', 'labelClass', 'contentClass', 'activeClass'])

    <div>
        <label
            @class([
                "tab flex flex-nowrap items-center gap-3 whitespace-nowrap px-4",
                $labelClass,
                "hidden" => $hidden,
                "tab-disabled" => $disabled
             ])
            :class="{ '{{ $activeClass }}': selected === '{{ $name }}' }"
        >
            <input id="{{ ($id ?? $uuid).$name }}" type="radio" name="{{ ($id ?? $uuid).$name }}" value="{{ $name }}" x-model="selected" />

            @if($icon)
              <x-mary-icon :name="$icon"  />
            @endif

            {{ $label }}

            @if ($badge)
                <x-badge :value="$badge" @class(["badge-sm badge-soft", $badgeClass]) />
            @endif
        </label>
        <div
            x-show="selected == '{{ $name }}'"
            {{ $attributes->class(["tab-content py-5 px-3 border-t-base-content/10 block rounded-none", $contentClass]) }}
         >
            {{ $slot }}
        </div>
    </div>