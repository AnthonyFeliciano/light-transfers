@if ($paginator->hasPages())
<nav role="navigation" aria-label="Paginação" class="flex items-center gap-2">
  {{-- Previous --}}
  @if ($paginator->onFirstPage())
    <span class="inline-flex items-center rounded-xl px-3 py-2 text-sm bg-slate-100 text-slate-400 cursor-not-allowed">
      ‹
    </span>
  @else
    <a href="{{ $paginator->previousPageUrl() }}" dusk="previousPage"
       class="inline-flex items-center rounded-xl px-3 py-2 text-sm bg-white ring-1 ring-slate-200 hover:bg-slate-50">
      ‹
    </a>
  @endif

  {{-- Elements --}}
  @foreach ($elements as $element)
    @if (is_string($element))
      <span class="px-2 text-sm text-slate-400">…</span>
    @endif

    @if (is_array($element))
      @foreach ($element as $page => $url)
        @if ($page == $paginator->currentPage())
          <span aria-current="page"
                class="inline-flex items-center rounded-xl px-3 py-2 text-sm
                       bg-slate-900 text-white">
            {{ $page }}
          </span>
        @else
          <a href="{{ $url }}"
             class="inline-flex items-center rounded-xl px-3 py-2 text-sm
                    bg-white ring-1 ring-slate-200 hover:bg-slate-50">
            {{ $page }}
          </a>
        @endif
      @endforeach
    @endif
  @endforeach

  {{-- Next --}}
  @if ($paginator->hasMorePages())
    <a href="{{ $paginator->nextPageUrl() }}" dusk="nextPage"
       class="inline-flex items-center rounded-xl px-3 py-2 text-sm bg-white ring-1 ring-slate-200 hover:bg-slate-50">
      ›
    </a>
  @else
    <span class="inline-flex items-center rounded-xl px-3 py-2 text-sm bg-slate-100 text-slate-400 cursor-not-allowed">
      ›
    </span>
  @endif
</nav>
@endif
