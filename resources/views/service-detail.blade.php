@extends('layouts.guest')

@section('title', $service['title'])
@section('meta_description', Str::limit(strip_tags($service['description']), 150))

@section('content')

<div class="container section-sh-sm">
    {{-- Breadcrumb --}}
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => $service['category'], 'url' => route('category', $service['category_slug'])],
        ['label' => Str::limit($service['title'], 35)],
    ]])

    <div class="row g-5">
        {{-- Main Content Column --}}
        <div class="col-lg-8">
            <div class="service-detail-content">
                {{-- Header Info --}}
                <div>
                    <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: var(--space-3); line-height: var(--lh-tight);">
                        {{ $service['title'] }}
                    </h1>

                    <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                        <a href="{{ route('freelancer.show', $service['freelancer']['slug']) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                            <img src="{{ $service['freelancer']['avatar'] }}" alt="{{ $service['freelancer']['name'] }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <span class="fw-semibold text-dark">{{ $service['freelancer']['name'] }}</span>
                                @if($service['freelancer']['verified'])
                                    <span class="badge-sh badge-verified ms-1"><i class="bi bi-patch-check-fill"></i> Verified Student</span>
                                @endif
                            </div>
                        </a>
                        <span class="text-muted">•</span>
                        <div class="d-flex align-items-center gap-1">
                            <i class="bi bi-star-fill text-warning"></i>
                            <strong>{{ number_format($service['rating'], 1) }}</strong>
                            <span class="text-secondary">({{ $service['reviews'] }} reviews)</span>
                        </div>
                    </div>
                </div>

                {{-- Service Gallery --}}
                <div class="service-gallery">
                    <img id="mainGalleryImage" src="{{ $service['gallery'][0] ?? $service['image'] }}" alt="{{ $service['title'] }}" class="service-gallery__main">
                    @if(isset($service['gallery']) && count($service['gallery']) > 1)
                        <div class="service-gallery__thumbs">
                            @foreach($service['gallery'] as $index => $img)
                                <img src="{{ $img }}" alt="Thumbnail {{ $index + 1 }}" class="service-gallery__thumb {{ $loop->first ? 'active' : '' }}" onclick="switchGalleryImage(this, '{{ $img }}')">
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Description --}}
                <div>
                    <h4 class="service-section-title">About This Service</h4>
                    <div style="font-size: var(--fs-base); color: var(--color-text); line-height: var(--lh-relaxed); white-space: pre-line;">
                        {{ $service['description'] }}
                    </div>
                </div>

                {{-- What is Included --}}
                @if(isset($service['what_you_get']))
                    <div>
                        <h4 class="service-section-title">What is Included</h4>
                        <ul class="includes-list">
                            @foreach($service['what_you_get'] as $item)
                                <li><i class="bi bi-check-circle-fill"></i> {{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Technologies --}}
                @if(isset($service['technologies']))
                    <div>
                        <h4 class="service-section-title">Technologies & Tools Used</h4>
                        <div class="tech-tags">
                            @foreach($service['technologies'] as $tech)
                                <span class="tech-tag"><i class="bi bi-code-slash me-1"></i>{{ $tech }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- FAQ Section --}}
                @if(isset($service['faq']) && count($service['faq']) > 0)
                    <div>
                        <h4 class="service-section-title">Frequently Asked Questions</h4>
                        <div class="accordion" id="serviceFaqAccordion">
                            @foreach($service['faq'] as $index => $faq)
                                <div class="faq-item">
                                    <div class="faq-item__q" data-bs-toggle="collapse" data-bs-target="#sFaq{{ $index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                        <span>{{ $faq['q'] }}</span>
                                        <i class="bi bi-chevron-down text-muted"></i>
                                    </div>
                                    <div id="sFaq{{ $index }}" class="collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#serviceFaqAccordion">
                                        <div class="faq-item__a pt-2">
                                            {{ $faq['a'] }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- About Freelancer Section --}}
                <div>
                    <h4 class="service-section-title">About The Freelancer</h4>
                    <div class="card-sh p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <img src="{{ $service['freelancer']['avatar'] }}" alt="{{ $service['freelancer']['name'] }}" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <h5 class="mb-1">
                                    <a href="{{ route('freelancer.show', $service['freelancer']['slug']) }}" class="text-dark text-decoration-none">
                                        {{ $service['freelancer']['name'] }}
                                    </a>
                                </h5>
                                <div class="text-secondary" style="font-size: var(--fs-sm);">{{ $service['freelancer']['skill'] }} • {{ $service['freelancer']['college'] }}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <i class="bi bi-star-fill text-warning" style="font-size: var(--fs-xs);"></i>
                                    <strong style="font-size: var(--fs-sm);">{{ $service['freelancer']['rating'] }}</strong>
                                    <span class="text-secondary" style="font-size: var(--fs-xs);">({{ $service['freelancer']['reviews'] }} reviews)</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm); line-height: var(--lh-relaxed);">
                            {{ $service['freelancer']['bio'] }}
                        </p>
                        <div class="d-flex gap-2">
                            <a href="{{ route('freelancer.show', $service['freelancer']['slug']) }}" class="btn-sh btn-sh-outline btn-sh-sm">
                                View Full Profile
                            </a>
                            <button class="btn-sh btn-sh-secondary btn-sh-sm" onclick="contactFreelancer()">
                                <i class="bi bi-chat-dots"></i> Contact Me
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Reviews Section --}}
                <div>
                    <h4 class="service-section-title">Reviews ({{ count($reviews) }})</h4>
                    
                    @include('components.rating-summary', [
                        'summary' => $ratingSummary,
                        'overallRating' => $service['rating'],
                        'totalReviews' => count($reviews)
                    ])

                    <div class="reviews-list">
                        @forelse($reviews as $review)
                            @include('components.review-card', ['review' => $review])
                        @empty
                            <div class="text-muted p-4 text-center">No reviews yet for this freelancer.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar Column: Package Selector & CTA --}}
        <div class="col-lg-4">
            <div class="service-detail-sidebar">
                {{-- Package Tabs --}}
                <div class="package-tabs" id="packageTabs">
                    @foreach(['basic' => 'Basic', 'standard' => 'Standard', 'premium' => 'Premium'] as $key => $label)
                        <div class="package-tab {{ $key === 'standard' ? 'active' : '' }}" data-package="{{ $key }}" onclick="selectPackage('{{ $key }}')">
                            {{ $label }}
                        </div>
                    @endforeach
                </div>

                {{-- Package Dynamic Card --}}
                @php
                    $packages = $service['packages'];
                    $defaultPkgKey = 'standard';
                    $defaultPkg = $packages[$defaultPkgKey];
                    $isLoggedIn = Auth::check();
                    $isOwner = $isLoggedIn && (Auth::id() === $service->user_id);
                @endphp

                <div class="package-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge-sh badge-sh-primary" id="pkgName">{{ $defaultPkg['name'] }} Package</span>
                    </div>

                    <div class="package-price" id="pkgPrice">
                        ₹{{ number_format($defaultPkg['price']) }}
                    </div>

                    <div class="package-meta">
                        <span><i class="bi bi-clock"></i> <strong id="pkgDelivery">{{ $defaultPkg['delivery'] }}</strong> Days Delivery</span>
                        <span><i class="bi bi-arrow-repeat"></i> <strong id="pkgRevisions">{{ $defaultPkg['revisions'] }}</strong> Revisions</span>
                    </div>

                    <ul class="package-features" id="pkgFeatures">
                        @foreach($defaultPkg['features'] as $feat)
                            <li><i class="bi bi-check-lg"></i> {{ $feat }}</li>
                        @endforeach
                    </ul>

                    @if($isOwner)
                        <div class="alert alert-primary py-3 px-3 mb-3 border-0 rounded" style="background: rgba(109, 40, 217, 0.08);">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-person-check-fill text-primary fs-5"></i>
                                <strong class="text-primary" style="font-size: var(--fs-sm);">This is Your Service!</strong>
                            </div>
                            <p class="text-secondary mb-0" style="font-size: var(--fs-xs);">
                                Clients hire you through this page. You do not pay for your own service.
                            </p>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('seller.services.edit', $service->id) }}" class="btn-sh btn-sh-primary btn-sh-lg w-100 text-center">
                                <i class="bi bi-pencil-square me-1"></i> Edit This Service
                            </a>
                            <a href="{{ route('seller.orders') }}" class="btn-sh btn-sh-secondary w-100 text-center">
                                <i class="bi bi-bag-check me-1"></i> Manage Client Orders
                            </a>
                            <a href="{{ route('seller.dashboard') }}" class="btn-sh btn-sh-outline w-100 text-center">
                                <i class="bi bi-speedometer2 me-1"></i> Freelancer Dashboard
                            </a>
                        </div>
                    @else
                        <div class="d-grid gap-2">
                            <button class="btn-sh btn-sh-primary btn-sh-lg w-100" onclick="bookService()">
                                <i class="bi bi-bag-check-fill"></i> Continue (₹<span id="pkgBtnPrice">{{ number_format($defaultPkg['price']) }}</span>)
                            </button>
                            <button class="btn-sh btn-sh-outline w-100" onclick="contactFreelancer()">
                                <i class="bi bi-chat-text"></i> Message Freelancer
                            </button>
                        </div>
                    @endif
                </div>

                {{-- Security / Earnings Guarantee Card --}}
                @if($isOwner)
                    <div class="card-sh p-3 mt-4 text-center bg-alt" style="font-size: var(--fs-xs);">
                        <i class="bi bi-cash-stack text-success fs-5 mb-1 d-block"></i>
                        <strong class="d-block mb-1">Freelancer Escrow Protection</strong>
                        <span class="text-secondary">When clients hire you, their payment is secured in Escrow and released directly to your earnings upon project completion.</span>
                    </div>
                @else
                    <div class="card-sh p-3 mt-4 text-center bg-alt" style="font-size: var(--fs-xs);">
                        <i class="bi bi-shield-lock-fill text-success fs-5 mb-1 d-block"></i>
                        <strong class="d-block mb-1">SkillHive Guarantee</strong>
                        <span class="text-secondary">Your payment is kept safe in escrow until you approve the delivered work.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Related Services Section --}}
    @if(count($relatedServices) > 0)
        <div class="mt-5 pt-5 border-top-sh">
            <h3 class="mb-4" style="font-size: var(--fs-xl);">Related Services You Might Like</h3>
            <div class="row g-4">
                @foreach($relatedServices as $relService)
                    <div class="col-lg-3 col-md-6">
                        @include('components.service-card', ['service' => $relService])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
const packagesData = @json($service['packages']);
const serviceSlug = @json($service['slug']);
const isLoggedIn = @json(Auth::check());
let selectedPackageKey = 'standard';

function selectPackage(pkgKey) {
    if (!packagesData[pkgKey]) return;

    selectedPackageKey = pkgKey;
    const pkg = packagesData[pkgKey];

    document.querySelectorAll('.package-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.package === pkgKey);
    });

    document.getElementById('pkgName').textContent = pkg.name + ' Package';
    document.getElementById('pkgPrice').textContent = '₹' + pkg.price.toLocaleString('en-IN');
    document.getElementById('pkgBtnPrice').textContent = pkg.price.toLocaleString('en-IN');
    document.getElementById('pkgDelivery').textContent = pkg.delivery;
    document.getElementById('pkgRevisions').textContent = pkg.revisions;

    const featuresContainer = document.getElementById('pkgFeatures');
    featuresContainer.innerHTML = '';
    pkg.features.forEach(feat => {
        const li = document.createElement('li');
        li.innerHTML = '<i class="bi bi-check-lg"></i> ' + feat;
        featuresContainer.appendChild(li);
    });
}

function switchGalleryImage(thumbEl, imgSrc) {
    document.getElementById('mainGalleryImage').src = imgSrc;
    document.querySelectorAll('.service-gallery__thumb').forEach(t => t.classList.remove('active'));
    thumbEl.classList.add('active');
}

function bookService() {
    if (isLoggedIn) {
        window.location.href = "{{ route('checkout', $service['slug']) }}?package=" + selectedPackageKey;
    } else {
        window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(window.location.pathname + '?package=' + selectedPackageKey);
    }
}

function contactFreelancer() {
    if (isLoggedIn) {
        window.location.href = "{{ route('chat.index') }}?with={{ $service->user_id }}";
    } else {
        window.location.href = "{{ route('login') }}?redirect=" + encodeURIComponent(window.location.pathname);
    }
}
</script>
@endpush
