@extends('layouts.guest')

@section('title', $category['name'])
@section('meta_description', $category['description'])

@section('content')

{{-- Category Header --}}
<div class="category-header">
    <div class="container">
        @include('components.breadcrumb', ['items' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Categories', 'url' => route('explore')],
            ['label' => $category['name']],
        ]])
        <h1><i class="{{ $category['icon'] }} me-2" style="color: var(--color-primary);"></i>{{ $category['name'] }}</h1>
        <p>{{ $category['description'] }}</p>

        {{-- Subcategory Chips --}}
        <div class="subcategory-chips">
            <a href="{{ route('category', $category['slug']) }}" class="subcategory-chip active">All</a>
            @foreach($category['subcategories'] as $sub)
                <a href="{{ route('category', $category['slug']) }}" class="subcategory-chip">{{ $sub }}</a>
            @endforeach
        </div>
    </div>
</div>

<div class="container section-sh-sm">
    {{-- Featured Freelancers --}}
    @if(count($freelancers) > 0)
        <div class="mb-5">
            <h5 style="margin-bottom: var(--space-4);">Featured Freelancers in {{ $category['name'] }}</h5>
            <div class="row g-3">
                @foreach($freelancers as $freelancer)
                    <div class="col-lg-3 col-md-4 col-6">
                        @include('components.freelancer-card', ['freelancer' => $freelancer])
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row">
        {{-- Sidebar Filters --}}
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <h5 style="margin-bottom: var(--space-5); font-size: var(--fs-md);">Filters</h5>

                {{-- Price Filter --}}
                <div class="filter-group">
                    <div class="filter-group__title">Budget</div>
                    <div class="filter-group__options">
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="0-500" id="cat-price-1">
                            <label class="form-check-label" for="cat-price-1">Under ₹500</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="500-1000" id="cat-price-2">
                            <label class="form-check-label" for="cat-price-2">₹500 – ₹1,000</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="1000-2000" id="cat-price-3">
                            <label class="form-check-label" for="cat-price-3">₹1,000 – ₹2,000</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-price" type="checkbox" value="2000-99999" id="cat-price-4">
                            <label class="form-check-label" for="cat-price-4">₹2,000+</label>
                        </div>
                    </div>
                </div>

                {{-- Delivery Filter --}}
                <div class="filter-group">
                    <div class="filter-group__title">Delivery Time</div>
                    <div class="filter-group__options">
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-delivery" type="radio" name="delivery" value="3" id="cat-delivery-1">
                            <label class="form-check-label" for="cat-delivery-1">Up to 3 days</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-delivery" type="radio" name="delivery" value="7" id="cat-delivery-2">
                            <label class="form-check-label" for="cat-delivery-2">Up to 7 days</label>
                        </div>
                        <div class="form-check form-check-sh">
                            <input class="form-check-input filter-delivery" type="radio" name="delivery" value="999" id="cat-delivery-any" checked>
                            <label class="form-check-label" for="cat-delivery-any">Any</label>
                        </div>
                    </div>
                </div>

                {{-- Other Categories --}}
                <div class="filter-group">
                    <div class="filter-group__title">Other Categories</div>
                    <div class="filter-group__options">
                        @foreach($categories as $cat)
                            @if($cat['slug'] !== $category['slug'])
                                <a href="{{ route('category', $cat['slug']) }}" class="d-block mb-1" style="font-size: var(--fs-sm); color: var(--color-text-secondary);">
                                    <i class="{{ $cat['icon'] }} me-1"></i> {{ $cat['name'] }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Service Grid --}}
        <div class="col-lg-9">
            <div class="explore-toolbar">
                <div class="result-count">
                    <span id="resultCount">{{ count($services) }}</span> services in {{ $category['name'] }}
                </div>
                <div class="d-flex align-items-center gap-3">
                    <select class="form-select-sh" id="sortSelect" style="min-width: 160px;">
                        <option value="recommended">Recommended</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Rated</option>
                    </select>
                </div>
            </div>

            @if(count($services) > 0)
                <div class="row g-4" id="serviceGrid">
                    @foreach($services as $service)
                        <div class="col-lg-4 col-md-6 service-grid-item">
                            @include('components.service-card', ['service' => $service])
                        </div>
                    @endforeach
                </div>
            @else
                @include('components.empty-state', [
                    'icon' => 'bi-folder2-open',
                    'title' => 'No services yet',
                    'text' => 'There are no services in this category yet. Check back soon!',
                    'actionUrl' => route('explore'),
                    'actionLabel' => 'Explore All Services',
                ])
            @endif

            <div id="emptyState" style="display: none;">
                @include('components.empty-state', [
                    'icon' => 'bi-search',
                    'title' => 'No services match your filters',
                    'text' => 'Try adjusting your filters to see more results.',
                ])
            </div>
        </div>
    </div>
</div>

@endsection
