@if ($paginator->hasPages())
<div class="pagination">
    @if ($paginator->onFirstPage())<span style="opacity:.4">←</span>@else<a href="{{ $paginator->previousPageUrl() }}">←</a>@endif
    @foreach ($elements as $element)
        @if (is_string($element))<span>…</span>@endif
        @if (is_array($element))
            @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())<span class="current">{{ $page }}</span>
                @else<a href="{{ $url }}">{{ $page }}</a>@endif
            @endforeach
        @endif
    @endforeach
    @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}">→</a>@else<span style="opacity:.4">→</span>@endif
</div>
@endif
