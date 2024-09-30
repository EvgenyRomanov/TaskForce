@if ($paginator->hasPages())
    <div role="navigation" aria-label="Pagination Navigation" class="pagination-wrapper">
        <ul class="pagination-list">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="pagination-item mark disabled" aria-disabled="true">
                </li>
            @else
                <li class="pagination-item mark">
                    <a class="link link--page" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                    </a>
                </li>
            @endif

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="pagination-item mark">
                    <a class="link link--page" href="{{ $paginator->nextPageUrl() }}" rel="next">
                    </a>
                </li>
            @else
                <li class="pagination-item mark disabled" aria-disabled="true">
                </li>
            @endif
        </ul>
    </div>
@endif
