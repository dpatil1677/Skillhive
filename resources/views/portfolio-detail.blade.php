@extends('layouts.guest')

@section('title', $portfolio['title'] . ' — Portfolio by ' . $freelancer['name'])
@section('meta_description', Str::limit($portfolio['description'], 150))

@section('content')

<div class="container section-sh-sm">
    {{-- Breadcrumb --}}
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $freelancer['name'], 'url' => route('freelancer.show', $freelancer['slug'])],
        ['label' => 'Portfolio', 'url' => route('freelancer.show', $freelancer['slug'])],
        ['label' => Str::limit($portfolio['title'], 30)],
    ]])

    <div class="row g-5">
        {{-- Main Portfolio Column --}}
        <div class="col-lg-8">
            <div class="mb-4">
                <span class="badge-sh badge-sh-primary mb-2">{{ $portfolio['category'] }}</span>
                <h1 style="font-size: var(--fs-3xl); font-weight: var(--fw-bold); margin-bottom: var(--space-3); line-height: var(--lh-tight);">
                    {{ $portfolio['title'] }}
                </h1>
                <div class="d-flex align-items-center gap-3 text-secondary" style="font-size: var(--fs-sm);">
                    <span>By <a href="{{ route('freelancer.show', $freelancer['slug']) }}" class="text-dark fw-semibold">{{ $freelancer['name'] }}</a></span>
                    <span>•</span>
                    <span>{{ $freelancer['college'] }}</span>
                </div>
            </div>

            {{-- Main Showcase Image --}}
            <img src="{{ $portfolio['image'] }}" alt="{{ $portfolio['title'] }}" class="portfolio-detail-img">

            {{-- Overview / Description --}}
            <div class="mb-5">
                <h4 class="service-section-title">Project Overview</h4>
                <div style="font-size: var(--fs-base); color: var(--color-text); line-height: var(--lh-relaxed); white-space: pre-line;">
                    {{ $portfolio['description'] }}
                </div>
            </div>

            {{-- Technologies Used --}}
            <div class="mb-5">
                <h4 class="service-section-title">Technologies & Tools Used</h4>
                <div class="tech-tags">
                    @foreach($portfolio['technologies'] as $tech)
                        <span class="tech-tag"><i class="bi bi-cpu me-1"></i>{{ $tech }}</span>
                    @endforeach
                </div>
            </div>

            {{-- Live Link CTA --}}
            @if(isset($portfolio['project_url']) && $portfolio['project_url'] !== '#')
                <div class="card-sh p-4 bg-alt d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5">
                    <div>
                        <h6 class="mb-1">Want to see the live project?</h6>
                        <span class="text-secondary" style="font-size: var(--fs-sm);">Check out the working demonstration online.</span>
                    </div>
                    <a href="{{ $portfolio['project_url'] }}" target="_blank" class="btn-sh btn-sh-primary">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Visit Live Project
                    </a>
                </div>
            @endif
        </div>

        {{-- Sidebar: Freelancer Info & Hire --}}
        <div class="col-lg-4">
            <div class="service-detail-sidebar">
                <div class="card-sh p-4">
                    <h5 class="mb-3" style="font-size: var(--fs-md);">Project Created By</h5>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="{{ $freelancer['avatar'] }}" alt="{{ $freelancer['name'] }}" style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover;">
                        <div>
                            <h6 class="mb-1">
                                <a href="{{ route('freelancer.show', $freelancer['slug']) }}" class="text-dark text-decoration-none">
                                    {{ $freelancer['name'] }}
                                </a>
                            </h6>
                            <span class="text-secondary d-block" style="font-size: var(--fs-xs);">{{ $freelancer['skill'] }}</span>
                            <div class="d-flex align-items-center gap-1 mt-1" style="font-size: var(--fs-xs);">
                                <i class="bi bi-star-fill text-warning"></i>
                                <strong>{{ number_format($freelancer['rating'], 1) }}</strong>
                                <span class="text-secondary">({{ $freelancer['reviews'] }})</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-secondary mb-4" style="font-size: var(--fs-xs); line-height: var(--lh-relaxed);">
                        Liked this project? You can hire {{ $freelancer['name'] }} to build a similar project for your needs.
                    </p>

                    <div class="d-grid gap-2">
                        <button class="btn-sh btn-sh-primary w-100" onclick="showAuthModal()" data-requires-auth="true">
                            <i class="bi bi-briefcase-fill"></i> Hire for Similar Project
                        </button>
                        <a href="{{ route('freelancer.show', $freelancer['slug']) }}" class="btn-sh btn-sh-outline w-100">
                            View Full Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Other Projects by Same Freelancer --}}
    @if(count($otherPortfolios) > 0)
        <div class="mt-5 pt-5 border-top-sh">
            <h3 class="mb-4" style="font-size: var(--fs-xl);">More Projects by {{ $freelancer['name'] }}</h3>
            <div class="row g-4">
                @foreach($otherPortfolios as $item)
                    <div class="col-lg-4 col-md-6">
                        @include('components.portfolio-card', ['item' => $item, 'freelancerSlug' => $freelancer['slug']])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection
