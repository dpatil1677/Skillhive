{{-- Rating Summary Component --}}
{{-- Usage: @include('components.rating-summary', ['summary' => $ratingSummary, 'overallRating' => $rating, 'totalReviews' => $reviewsCount]) --}}
<div class="rating-summary">
    <div class="d-flex align-items-center">
        <div class="rating-summary__big">
            <div class="rating-summary__number">{{ number_format($overallRating, 1) }}</div>
            <div class="mb-2">
                @include('components.star-rating', ['rating' => $overallRating])
            </div>
            <div style="font-size: var(--fs-xs); color: var(--color-text-secondary);">
                {{ $totalReviews }} total review{{ $totalReviews == 1 ? '' : 's' }}
            </div>
        </div>
        <div class="rating-summary__bars">
            @foreach($summary as $star => $data)
                <div class="rating-bar-row">
                    <div class="rating-bar-label">{{ $star }} ★</div>
                    <div class="rating-bar-track">
                        <div class="rating-bar-fill" style="width: {{ $data['percent'] }}%;"></div>
                    </div>
                    <div class="rating-bar-count">({{ $data['count'] }})</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
