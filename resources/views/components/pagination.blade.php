@if ($paginator->hasPages())
<nav class="flex items-center gap-1" role="navigation" aria-label="Pagination Navigation">
    {{-- Previous --}}
    @if ($paginator->onFirstPage())
        <span class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm bg-slate-100 text-slate-400 cursor-not-allowed select-none">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
            Anterior
        </span>
    @else
        <button type="button"
                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm bg-white ring-1 ring-slate-200 hover:bg-slate-50">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M15 18l-6-6 6-6"/></svg>
            Anterior
        </button>
    @endif

    {{-- Page Numbers --}}
    @foreach ($elements as $element)
        @if (is_string($element))
            <span class="px-2 text-slate-400 select-none">…</span>
        @endif

        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                    <span aria-current="page"
                          class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-sm font-medium bg-slate-900 text-white ring-1 ring-slate-900">
                        {{ $page }}
                    </span>
                @else
                    <button type="button"
                            wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                            class="inline-flex items-center justify-center rounded-xl px-3 py-2 text-sm bg-white text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
                        {{ $page }}
                    </button>
                @endif
            @endforeach
        @endif
    @endforeach

    {{-- Next --}}
    @if ($paginator->hasMorePages())
        <button type="button"
                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm bg-white ring-1 ring-slate-200 hover:bg-slate-50">
            Próxima
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </button>
    @else
        <span class="inline-flex items-center gap-1 rounded-xl px-3 py-2 text-sm bg-slate-100 text-slate-400 cursor-not-allowed select-none">
            Próxima
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </span>
    @endif
</nav>
@endif
