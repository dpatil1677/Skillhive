{{-- Category Card Component --}}
{{-- Usage: @include('components.category-card', ['category' => $category]) --}}
<a href="{{ route('category', $category['slug']) }}" class="card-sh category-card">
    <div class="category-card__icon">
        <i class="{{ $category['icon'] }}"></i>
    </div>
    <div class="category-card__info">
        <h6>{{ $category['name'] }}</h6>
        <p>{{ $category['count'] }} services</p>
    </div>
</a>
