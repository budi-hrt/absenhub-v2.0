<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            <div class="flex justify-between flex-1 sm:hidden">
                <span>
                    @if ($paginator->onFirstPage())
                        <span class="btn btn-sm btn-disabled">{!! __('pagination.previous') !!}</span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="btn btn-sm">
                            {!! __('pagination.previous') !!}
                        </button>
                    @endif
                </span>
                <span>
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.before" class="btn btn-sm">
                            {!! __('pagination.next') !!}
                        </button>
                    @else
                        <span class="btn btn-sm btn-disabled">{!! __('pagination.next') !!}</span>
                    @endif
                </span>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-base-content/60">
                        <span>{!! __('Showing') !!}</span>
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        <span>{!! __('to') !!}</span>
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                        <span>{!! __('of') !!}</span>
                        <span class="font-medium">{{ $paginator->total() }}</span>
                        <span>{!! __('results') !!}</span>
                    </p>
                </div>
                <div class="join shadow-sm">
                    {{-- Previous Page --}}
                    @if ($paginator->onFirstPage())
                        <span class="join-item btn btn-sm btn-disabled">‹</span>
                    @else
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" dusk="previousPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="join-item btn btn-sm">
                            ‹
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="join-item btn btn-sm btn-disabled">{{ $element }}</span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="join-item btn btn-sm btn-primary">{{ $page }}</span>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="join-item btn btn-sm" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page --}}
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" dusk="nextPage{{ $paginator->getPageName() == 'page' ? '' : '.' . $paginator->getPageName() }}.after" class="join-item btn btn-sm">
                            ›
                        </button>
                    @else
                        <span class="join-item btn btn-sm btn-disabled">›</span>
                    @endif
                </div>
            </div>
        </nav>
    @endif
</div>
