@extends('layouts.guest')

@section('title', 'Explore Services')
@section('meta_description', 'Browse and discover student freelance services on SkillHive. Filter by category, price, rating and delivery time.')

@section('content')

<div class="container section-sh-sm">
    {{-- Breadcrumb --}}
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Explore Services'],
    ]])

    {{-- Search --}}
    <div class="mb-5">
        @include('components.search-bar', ['value' => $query, 'placeholder' => 'Search services...'])
    </div>

    <div class="row">
        {{-- Sidebar Filters --}}
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 style="margin: 0; font-size: var(--fs-md);">Filters</h5>
                    <button class="btn-sh btn-sh-sm btn-sh-secondary" id="clearFilters" style="font-size: var(--fs-xs);">Clear All</button>
                </div>

                {{-- Category Filter --}}
                <div class="filter-group">
                    <div class="filter-group__title">Category</div>
                    <div class="filter-group__options">
                        @foreach($categories as $cat)
                            <div class="form-check form-check-sh">
                                <input class="form-check-input filter-category" type="checkbox" value="{{ $cat['slug'] }}" id="cat-{{ $cat['slug'] }}">
                                <label class="form-check-label" for="cat-{{ $cat['slug'] }}">
                                    {{ $cat['name'] }} <span class="text-muted">({{ $cat['count'] }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Price Filter --}}
                <div class="filter-group">
                    <div class="filter-group__title">Budget</div>
                    <div class="filter-group__options">
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="0-500" id="price-1">
                            <label class="form-check-label" for="price-1">Under ₹500</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="500-1000" id="price-2">
                            <label class="form-check-label" for="price-2">₹500 – ₹1,000</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="1000-2000" id="price-3">
                            <label class="form-check-label" for="price-3">₹1,000 – ₹2,000</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="2000-5000" id="price-4">
                            <label class="form-check-label" for="price-4">₹2,000 – ₹5,000</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="5000-99999" id="price-5">
                            <label class="form-check-label" for="price-5">₹5,000+</label>
                        </div>
                    </div>
                </div>

                {{-- Rating Filter --}}
                <div class="filter-group">
                    <div class="filter-group__title">Seller Rating</div>
                    <div class="filter-group__options">
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-rating" type="radio" name="rating" value="4.5" id="rating-1">
                            <label class="form-check-label" for="rating-1">
                                <i class="bi bi-star-fill" style="color: var(--color-star); font-size: 0.75rem;"></i> 4.5 & up
                            </label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-rating" type="radio" name="rating" value="4.0" id="rating-2">
                            <label class="form-check-label" for="rating-2">
                                <i class="bi bi-star-fill" style="color: var(--color-star); font-size: 0.75rem;"></i> 4.0 & up
                            </label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-rating" type="radio" name="rating" value="3.5" id="rating-3">
                            <label class="form-check-label" for="rating-3">
                                <i class="bi bi-star-fill" style="color: var(--color-star); font-size: 0.75rem;"></i> 3.5 & up
                            </label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-rating" type="radio" name="rating" value="0" id="rating-any" checked>
                            <label class="form-check-label" for="rating-any">Any rating</label>
                        </div>
                    </div>
                </div>

                {{-- Delivery Time Filter --}}
                <div class="filter-group">
                    <div class="filter-group__title">Delivery Time</div>
                    <div class="filter-group__options">
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-delivery" type="radio" name="delivery" value="1" id="delivery-1">
                            <label class="form-check-label" for="delivery-1">Up to 1 day</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-delivery" type="radio" name="delivery" value="3" id="delivery-2">
                            <label class="form-check-label" for="delivery-2">Up to 3 days</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-delivery" type="radio" name="delivery" value="7" id="delivery-3">
                            <label class="form-check-label" for="delivery-3">Up to 7 days</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-delivery" type="radio" name="delivery" value="999" id="delivery-any" checked>
                            <label class="form-check-label" for="delivery-any">Any</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Service Grid --}}
        <div class="col-lg-9">
            <div class="explore-toolbar">
                <div class="result-count">
                    <span id="resultCount">{{ count($services) }}</span> services available
                </div>
                <div class="d-flex align-items-center gap-3">
                    <label class="form-label-sh mb-0" for="sortSelect" style="white-space: nowrap;">Sort by:</label>
                    <select class="form-select-sh" id="sortSelect" style="min-width: 160px;">
                        <option value="recommended">Recommended</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                        <option value="newest">Newest</option>
                    </select>
                </div>
            </div>

            <div class="row g-4" id="serviceGrid">
                @foreach($services as $service)
                    <div class="col-lg-4 col-md-6 service-grid-item">
                        @include('components.service-card', ['service' => $service])
                    </div>
                @endforeach
            </div>

            {{-- Empty State (hidden by default, shown via JS) --}}
            <div id="emptyState" style="display: none;">
                @include('components.empty-state', [
                    'icon' => 'bi-search',
                    'title' => 'No services found',
                    'text' => 'Try adjusting your search or filters to find what you\'re looking for.',
                ])
            </div>

            {{-- Pagination --}}
            @include('components.pagination', ['currentPage' => 1, 'totalPages' => 1])
        </div>
    </div>
</div>

@endsection
