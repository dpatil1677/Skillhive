{{-- Review Card Component --}}
{{-- Usage: @include('components.review-card', ['review' => $review]) --}}
<div class="review-card">
    <div class="review-card__header">
        <div class="review-card__user">
            <img src="{{ $review['avatar'] }}" alt="{{ $review['name'] }}" class="review-card__avatar">
            <div>
                <div class="review-card__name">{{ $review['name'] }}</div>
                <div class="review-card__meta">
                    <span>{{ $review['service'] }}</span>
                    @if($review['verified'])
                        <span>•</span>
                        <span class="text-success"><i class="bi bi-bag-check-fill"></i> Verified Order</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="review-card__rating-date">
            @include('components.star-rating', ['rating' => $review['rating']])
            <div class="review-card__date">{{ $review['date'] }}</div>
        </div>
    </div>
    <p class="review-card__text">{{ $review['text'] }}</p>
</div>
