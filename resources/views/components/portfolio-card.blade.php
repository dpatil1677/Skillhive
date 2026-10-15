{{-- Portfolio Card Component --}}
{{-- Usage: @include('components.portfolio-card', ['item' => $portfolioItem, 'freelancerSlug' => $freelancer['slug']]) --}}
<div class="card-sh portfolio-card">
    <a href="{{ route('portfolio.show', ['freelancerSlug' => $freelancerSlug, 'portfolioSlug' => $item['slug']]) }}" class="portfolio-card__img-wrap d-block">
        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="portfolio-card__img" loading="lazy">
    </a>
    <div class="portfolio-card__body">
        <div class="portfolio-card__title">
            <a href="{{ route('portfolio.show', ['freelancerSlug' => $freelancerSlug, 'portfolioSlug' => $item['slug']]) }}">
                {{ $item['title'] }}
            </a>
        </div>
        <p class="portfolio-card__desc">{{ $item['description'] }}</p>
        <div class="portfolio-card__techs">
            @foreach($item['technologies'] as $tech)
                <span class="portfolio-tech">{{ $tech }}</span>
            @endforeach
        </div>
    </div>
</div>
