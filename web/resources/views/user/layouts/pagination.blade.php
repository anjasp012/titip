@if ($paginator->hasPages())
    <ul class="pagination">
        @if($paginator->currentPage() > 3)
            <li class="page-item hidden-xs"><a href="{{ $paginator->url(1) }}" class="page-link">First</a></li>
        @endif
        @if (!$paginator->onFirstPage())
            <li class="page-item"><a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">«</a></li>
        @endif
        @foreach(range(1, $paginator->lastPage()) as $i)
            @if($i >= $paginator->currentPage() - 2 && $i <= $paginator->currentPage() + 2)
                @if ($i == $paginator->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                @else
                    <li class="page-item"><a href="{{ $paginator->url($i) }}" class="page-link">{{ $i }}</a></li>
                @endif
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <li class="page-item"><a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">»</a></li>
        @endif
        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            <li class="page-item hidden-xs"><a href="{{ $paginator->url($paginator->lastPage()) }}" class="page-link">Last</a></li>
        @endif
    </ul>
@endif