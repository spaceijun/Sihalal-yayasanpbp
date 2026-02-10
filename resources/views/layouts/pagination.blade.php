<div class="align-items-center mt-2 row g-3 text-center text-sm-start">
    <div class="col-sm">
        <div class="text-muted">
            Showing <span class="fw-semibold">{{ $paginator->firstItem() }}</span> to
            <span class="fw-semibold">{{ $paginator->lastItem() }}</span> of
            <span class="fw-semibold">{{ $paginator->total() }}</span> Results
        </div>
    </div>
    <div class="col-sm-auto">
        <ul class="pagination pagination-separated pagination-sm justify-content-center justify-content-sm-start mb-0">
            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">←</span>
                </li>
            @else
                <li class="page-item">
                    <a href="{{ $paginator->previousPageUrl() }}" class="page-link">←</a>
                </li>
            @endif

            {{-- Page Numbers --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $delta = 2; // Jumlah halaman di kiri dan kanan halaman aktif
                $left = $current - $delta;
                $right = $current + $delta + 1;
                $range = [];
                $rangeWithDots = [];
                $l = 0;

                for ($i = 1; $i <= $last; $i++) {
                    if ($i == 1 || $i == $last || ($i >= $left && $i < $right)) {
                        $range[] = $i;
                    }
                }

                foreach ($range as $i) {
                    if ($l) {
                        if ($i - $l === 2) {
                            $rangeWithDots[] = $l + 1;
                        } elseif ($i - $l !== 1) {
                            $rangeWithDots[] = '...';
                        }
                    }
                    $rangeWithDots[] = $i;
                    $l = $i;
                }
            @endphp

            @foreach ($rangeWithDots as $page)
                @if ($page === '...')
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                @else
                    <li class="page-item {{ $page == $current ? 'active' : '' }}">
                        <a href="{{ $paginator->url($page) }}" class="page-link">{{ $page }}</a>
                    </li>
                @endif
            @endforeach

            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a href="{{ $paginator->nextPageUrl() }}" class="page-link">→</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">→</span>
                </li>
            @endif
        </ul>
    </div>
</div>
