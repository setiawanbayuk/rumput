@if ($paginator->hasPages())
<nav aria-label="Pagination" class="d-flex justify-content-center mt-3">
    <ul class="pagination m-0">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                <span class="page-link"><i class="ri-arrow-left-s-line"></i></span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link"
                    href="{{ $paginator->previousPageUrl() . (str_contains($paginator->previousPageUrl(), '#') ? '' : '#'.($fragment ?? '')) }}"
                    rel="prev" aria-label="@lang('pagination.previous')">
                    <i class="ri-arrow-left-s-line"></i>
                </a>
            </li>
        @endif

        {{-- Numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @php
                        // tambahkan fragment agar balik ke tab yang benar
                        $urlWithHash = $url . (str_contains($url, '#') ? '' : '#'.($fragment ?? ''));
                    @endphp
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $urlWithHash }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li class="page-item">
                <a class="page-link"
                    href="{{ $paginator->nextPageUrl() . (str_contains($paginator->nextPageUrl(), '#') ? '' : '#'.($fragment ?? '')) }}"
                    rel="next" aria-label="@lang('pagination.next')">
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                <span class="page-link"><i class="ri-arrow-right-s-line"></i></span>
            </li>
        @endif

    </ul>
</nav>
@endif
