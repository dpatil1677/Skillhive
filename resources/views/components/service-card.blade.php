{{-- Service Card Component --}}
{{-- Usage: @include('components.service-card', ['service' => $service]) --}}
@php
    $isFav = false;
    if (Auth::check()) {
        $userFavIds = $userFavoritedIds ?? null;
        if ($userFavIds === null) {
            $userFavIds = \App\Models\Favorite::where('user_id', Auth::id())->pluck('service_id')->toArray();
        }
        $isFav = in_array($service['id'], $userFavIds);
    }
@endphp
<div class="card-sh service-card" data-category="{{ $service['category_slug'] }}" data-price="{{ $service['price'] }}" data-rating="{{ $service['rating'] }}" data-delivery="{{ $service['delivery_days'] }}" data-title="{{ strtolower($service['title']) }}">
    <button class="service-card__fav {{ $isFav ? 'active' : '' }}" onclick="toggleFavorite(this, {{ $service['id'] }})" title="Save to favorites" data-service-id="{{ $service['id'] }}" data-requires-auth="{{ Auth::check() ? 'false' : 'true' }}">
        <i class="bi {{ $isFav ? 'bi-heart-fill' : 'bi-heart' }}"></i>
    </button>
    <a href="{{ route('service.show', $service['slug']) }}">
        <img src="{{ $service['image'] }}" alt="{{ $service['title'] }}" class="service-card__img" loading="lazy">
    </a>
    <div class="service-card__body">
        <div class="service-card__freelancer">
            <a href="{{ route('freelancer.show', $service['freelancer']['slug']) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ $service['freelancer']['avatar'] }}" alt="{{ $service['freelancer']['name'] }}" class="service-card__avatar">
                <span class="service-card__name">
                    {{ $service['freelancer']['name'] }}
                    @if($service['freelancer']['verified'])
                        <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.75rem;" title="Verified Student"></i>
                    @endif
                </span>
            </a>
        </div>
        <div class="service-card__title">
            <a href="{{ route('service.show', $service['slug']) }}">{{ $service['title'] }}</a>
        </div>
        <div class="service-card__meta">
            <div class="service-card__rating">
                <i class="bi bi-star-fill"></i>
                <strong>{{ $service['rating'] }}</strong>
                <span>({{ $service['reviews'] }})</span>
            </div>
            <div class="service-card__price">
                ₹{{ number_format($service['price']) }} <span>starting</span>
            </div>
        </div>
        <div class="service-card__delivery">
            <i class="bi bi-clock"></i>
            Delivery in {{ $service['delivery_days'] }} day{{ $service['delivery_days'] > 1 ? 's' : '' }}
        </div>
    </div>
</div>
