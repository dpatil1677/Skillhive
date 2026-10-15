{{-- Star Rating Component --}}
{{-- Usage: @include('components.star-rating', ['rating' => 4.5]) --}}
<div class="star-rating">
    @for($i = 1; $i <= 5; $i++)
        @if($i <= floor($rating))
            <i class="bi bi-star-fill"></i>
        @elseif($i - 0.5 <= $rating)
            <i class="bi bi-star-half"></i>
        @else
            <i class="bi bi-star"></i>
        @endif
    @endfor
</div>
