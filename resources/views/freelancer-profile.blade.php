@extends('layouts.guest')

@section('title', $freelancer['name'] . ' — ' . $freelancer['skill'])
@section('meta_description', Str::limit($freelancer['bio'], 150))

@section('content')

{{-- Profile Hero / Header --}}
<div class="profile-hero">
    <div class="container">
        @include('components.breadcrumb', ['items' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Freelancers', 'url' => route('explore')],
            ['label' => $freelancer['name']],
        ]])

        <div class="profile-header">
            <img src="{{ $freelancer['avatar'] }}" alt="{{ $freelancer['name'] }}" class="profile-avatar">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                    <h1 class="profile-meta__name mb-0">{{ $freelancer['name'] }}</h1>
                    @if($freelancer['verified'])
                        <span class="badge-sh badge-verified"><i class="bi bi-patch-check-fill"></i> Verified Student</span>
                    @endif
                    @if($freelancer['level'] === 'Pro')
                        <span class="badge-sh badge-sh-primary"><i class="bi bi-award"></i> Pro</span>
                    @elseif($freelancer['level'] === 'Rising Talent')
                        <span class="badge-sh badge-sh-success"><i class="bi bi-graph-up-arrow"></i> Rising Talent</span>
                    @endif
                </div>

                <div class="profile-meta__title">{{ $freelancer['skill'] }} • <i class="bi bi-mortarboard me-1"></i>{{ $freelancer['college'] }}</div>

                <div class="profile-meta__stats">
                    <div class="profile-meta__stat">
                        <i class="bi bi-star-fill text-warning"></i>
                        <strong>{{ number_format($freelancer['rating'], 1) }}</strong>
                        <span>({{ count($reviews) }} reviews)</span>
                    </div>
                    <div class="profile-meta__stat">
                        <i class="bi bi-bag-check text-success"></i>
                        <strong>{{ $freelancer['completed_orders'] }}</strong>
                        <span>Orders Completed</span>
                    </div>
                    <div class="profile-meta__stat">
                        <i class="bi bi-clock text-secondary"></i>
                        <span>Response time: <strong>{{ $freelancer['response_time'] }}</strong></span>
                    </div>
                    <div class="profile-meta__stat">
                        <i class="bi bi-calendar3 text-secondary"></i>
                        <span>Member since <strong>{{ $freelancer['member_since'] }}</strong></span>
                    </div>
                </div>

                <div class="profile-actions">
                    @if(Auth::check())
                        <a href="{{ route('chat.index', ['with' => $freelancer['id']]) }}" class="btn-sh btn-sh-outline">
                            <i class="bi bi-chat-dots-fill"></i> Message
                        </a>
                    @else
                        <button class="btn-sh btn-sh-outline" onclick="showAuthModal()" data-requires-auth="true">
                            <i class="bi bi-chat-dots-fill"></i> Message
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container section-sh-sm">
    <div class="row g-5">
        {{-- Main Section Column --}}
        <div class="col-lg-8">
            {{-- Tabs --}}
            <div class="profile-tabs">
                <a href="#services-section" class="profile-tab active" onclick="switchProfileTab(event, 'services-tab-content')">
                    Services ({{ count($services) }})
                </a>
                <a href="#portfolio-section" class="profile-tab" onclick="switchProfileTab(event, 'portfolio-tab-content')">
                    Portfolio ({{ count($freelancer['portfolio']) }})
                </a>
                <a href="#reviews-section" class="profile-tab" onclick="switchProfileTab(event, 'reviews-tab-content')">
                    Reviews ({{ count($reviews) }})
                </a>
                <a href="#about-section" class="profile-tab" onclick="switchProfileTab(event, 'about-tab-content')">
                    About
                </a>
            </div>

            {{-- Tab 1: Services Offered --}}
            <div id="services-tab-content" class="profile-tab-pane">
                <h4 class="service-section-title">Services Offered by {{ $freelancer['name'] }}</h4>
                @if(count($services) > 0)
                    <div class="row g-4 mb-5">
                        @foreach($services as $s)
                            <div class="col-md-6">
                                @include('components.service-card', ['service' => $s])
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No active services listed right now.</p>
                @endif
            </div>

            {{-- Tab 2: Portfolio Projects --}}
            <div id="portfolio-tab-content" class="profile-tab-pane d-none">
                <h4 class="service-section-title">Portfolio & Previous Work</h4>
                @if(count($freelancer['portfolio']) > 0)
                    <div class="row g-4 mb-5">
                        @foreach($freelancer['portfolio'] as $item)
                            <div class="col-md-6">
                                @include('components.portfolio-card', ['item' => $item, 'freelancerSlug' => $freelancer['slug']])
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No portfolio items added yet.</p>
                @endif
            </div>

            {{-- Tab 3: Reviews --}}
            <div id="reviews-tab-content" class="profile-tab-pane d-none">
                <h4 class="service-section-title">Client Feedback & Ratings</h4>
                
                @include('components.rating-summary', [
                    'summary' => $ratingSummary,
                    'overallRating' => $freelancer['rating'],
                    'totalReviews' => count($reviews)
                ])

                <div class="reviews-list">
                    @forelse($reviews as $review)
                        @include('components.review-card', ['review' => $review])
                    @empty
                        <div class="text-muted p-4 text-center">No reviews yet.</div>
                    @endforelse
                </div>
            </div>

            {{-- Tab 4: About & Bio --}}
            <div id="about-tab-content" class="profile-tab-pane d-none">
                <h4 class="service-section-title">About {{ $freelancer['name'] }}</h4>
                <div class="card-sh p-4 mb-4">
                    <p style="font-size: var(--fs-base); color: var(--color-text); line-height: var(--lh-relaxed); white-space: pre-line;">
                        {{ $freelancer['bio'] }}
                    </p>
                </div>

                <h4 class="service-section-title">Skills & Expertise</h4>
                <div class="skills-list mb-4">
                    @foreach($freelancer['skills'] as $skill)
                        <span class="skill-chip"><i class="bi bi-check2 me-1 text-primary"></i>{{ $skill }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Sidebar Info Column --}}
        <div class="col-lg-4">
            <div class="card-sh p-4">
                <h5 class="mb-3" style="font-size: var(--fs-md);">Freelancer Information</h5>
                
                <div class="profile-info-item">
                    <i class="bi bi-geo-alt"></i>
                    <div>
                        <span>College Location</span>
                        <strong class="d-block">{{ $freelancer['college'] }}</strong>
                    </div>
                </div>

                <div class="profile-info-item">
                    <i class="bi bi-translate"></i>
                    <div>
                        <span>Languages</span>
                        <strong class="d-block">{{ implode(', ', $freelancer['languages']) }}</strong>
                    </div>
                </div>

                <div class="profile-info-item">
                    <i class="bi bi-calendar-event"></i>
                    <div>
                        <span>Joined SkillHive</span>
                        <strong class="d-block">{{ $freelancer['member_since'] }}</strong>
                    </div>
                </div>

                <div class="profile-info-item">
                    <i class="bi bi-lightning-charge"></i>
                    <div>
                        <span>Avg. Response Time</span>
                        <strong class="d-block">{{ $freelancer['response_time'] }}</strong>
                    </div>
                </div>

                <div class="profile-info-item">
                    <i class="bi bi-patch-check"></i>
                    <div>
                        <span>Student Verification Status</span>
                        <strong class="d-block text-success">
                            @if($freelancer['verified'])
                                <i class="bi bi-check-circle-fill"></i> ID Verified
                            @else
                                Pending Verification
                            @endif
                        </strong>
                    </div>
                </div>

                <div class="mt-4">
                    @if(Auth::check())
                        <a href="{{ route('chat.index', ['with' => $freelancer['id']]) }}" class="btn-sh btn-sh-primary w-100 text-center text-decoration-none">
                            <i class="bi bi-chat-dots"></i> Message {{ $freelancer['name'] }}
                        </a>
                    @else
                        <button class="btn-sh btn-sh-primary w-100" onclick="showAuthModal()" data-requires-auth="true">
                            <i class="bi bi-chat-dots"></i> Message {{ $freelancer['name'] }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function switchProfileTab(event, contentId) {
    event.preventDefault();

    // Toggle active tab link
    document.querySelectorAll('.profile-tab').forEach(tab => tab.classList.remove('active'));
    event.currentTarget.classList.add('active');

    // Toggle active content pane
    document.querySelectorAll('.profile-tab-pane').forEach(pane => pane.classList.add('d-none'));
    document.getElementById(contentId).classList.remove('d-none');
}
</script>
@endpush
