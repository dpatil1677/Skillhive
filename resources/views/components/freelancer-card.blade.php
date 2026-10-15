{{-- Freelancer Mini Card Component --}}
{{-- Usage: @include('components.freelancer-card', ['freelancer' => $freelancer]) --}}
<div class="card-sh freelancer-card">
    <a href="{{ route('freelancer.show', $freelancer['slug']) }}">
        <img src="{{ $freelancer['avatar'] }}" alt="{{ $freelancer['name'] }}" class="freelancer-card__avatar" loading="lazy">
    </a>
    <div class="freelancer-card__name">
        <a href="{{ route('freelancer.show', $freelancer['slug']) }}">{{ $freelancer['name'] }}</a>
        @if($freelancer['verified'])
            <i class="bi bi-patch-check-fill text-primary" style="font-size: 0.75rem;" title="Verified Student"></i>
        @endif
    </div>
    <div class="freelancer-card__skill">{{ $freelancer['skill'] }}</div>
    <div class="freelancer-card__rating">
        <i class="bi bi-star-fill"></i>
        <strong style="color: var(--color-text); font-weight: 600;">{{ $freelancer['rating'] }}</strong>
        <span>({{ $freelancer['reviews'] }} reviews)</span>
    </div>
    @if($freelancer['level'] === 'Pro')
        <span class="badge-sh badge-sh-primary mt-2">
            <i class="bi bi-award"></i> Pro
        </span>
    @elseif($freelancer['level'] === 'Rising Talent')
        <span class="badge-sh badge-sh-success mt-2">
            <i class="bi bi-graph-up-arrow"></i> Rising Talent
        </span>
    @endif
</div>
