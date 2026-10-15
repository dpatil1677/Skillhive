{{-- Pagination Component --}}
{{-- Usage: @include('components.pagination', ['currentPage' => 1, 'totalPages' => 5]) --}}
@if(($totalPages ?? 1) > 1)
<nav aria-label="Page navigation">
    <ul class="pagination-sh">
        <li class="page-item {{ ($currentPage ?? 1) <= 1 ? 'disabled' : '' }}">
            <a class="page-link" href="#" data-page="{{ ($currentPage ?? 1) - 1 }}">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>
        @for($i = 1; $i <= ($totalPages ?? 1); $i++)
            <li class="page-item {{ ($currentPage ?? 1) == $i ? 'active' : '' }}">
                <a class="page-link" href="#" data-page="{{ $i }}">{{ $i }}</a>
            </li>
        @endfor
        <li class="page-item {{ ($currentPage ?? 1) >= ($totalPages ?? 1) ? 'disabled' : '' }}">
            <a class="page-link" href="#" data-page="{{ ($currentPage ?? 1) + 1 }}">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
@endif
