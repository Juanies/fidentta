@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación"
        class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-xs text-ink2">
            Mostrando {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} de {{ $paginator->total() }}
        </p>

        <div class="flex flex-wrap items-center justify-center gap-1" aria-label="Páginas">
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" class="border border-border px-3 py-2 text-sm text-ink2/50">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Página anterior"
                    class="border border-border px-3 py-2 text-sm text-ink hover:bg-mint/40">Anterior</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span aria-disabled="true" class="px-2 py-2 text-sm text-ink2">{{ $element }}</span>
                @elseif (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" aria-label="Página {{ $page }}"
                                class="min-w-9 border border-ink bg-ink px-3 py-2 text-center text-sm font-semibold text-paper">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" aria-label="Ir a la página {{ $page }}"
                                class="min-w-9 border border-border px-3 py-2 text-center text-sm text-ink hover:bg-mint/40">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Página siguiente"
                    class="border border-border px-3 py-2 text-sm text-ink hover:bg-mint/40">Siguiente</a>
            @else
                <span aria-disabled="true" class="border border-border px-3 py-2 text-sm text-ink2/50">Siguiente</span>
            @endif
        </div>
    </nav>
@endif
