{{-- Search Bar Component --}}
{{-- Usage: @include('components.search-bar', ['placeholder' => '...', 'value' => '', 'size' => 'lg']) --}}
<div class="{{ isset($size) && $size === 'lg' ? 'search-bar-lg' : '' }}">
    <form class="search-bar-sh" action="{{ route('explore') }}" method="GET">
        <input
            type="text"
            name="q"
            placeholder="{{ $placeholder ?? 'What service are you looking for?' }}"
            value="{{ $value ?? '' }}"
            autocomplete="off"
        >
        <button type="submit">
            <i class="bi bi-search"></i>
            <span class="d-none d-sm-inline">Search</span>
        </button>
    </form>
</div>
