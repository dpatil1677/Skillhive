@extends('layouts.guest')

@section('title', 'Checkout — ' . $service->title)

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Services', 'url' => route('explore')],
        ['label' => $service->category_name ?? ($service->category?->name ?? 'Service'), 'url' => route('category', $service->category_slug ?? 'web-development')],
        ['label' => Str::limit($service->title, 30), 'url' => route('service.show', $service->slug)],
        ['label' => 'Checkout'],
    ]])

    <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: var(--space-6);">Order Checkout</h1>

    <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
        @csrf
        <input type="hidden" name="service_slug" value="{{ $service->slug }}">
        <input type="hidden" name="package_key" value="{{ $packageKey }}">

        <div class="row g-5">
            {{-- Main Checkout Form Column --}}
            <div class="col-lg-8">
                {{-- Service & Package Summary Card --}}
                <div class="card-sh p-4 mb-4">
                    <h5 class="service-section-title mb-3">1. Service & Package Details</h5>
                    
                    @php
                        $defaultImage = 'https://placehold.co/900x540/e8e0f5/6D28D9?text=' . urlencode($service->title ?? 'SkillHive Service');
                        $serviceImage = !empty($service->image) ? $service->image : $defaultImage;
                        $categoryName = $service->category_name ?? ($service->category?->name ?? 'General');
                        $categoryIcon = $service->category?->icon ?? 'bi-tag';
                        $freelancer = $service->freelancer ?? [];
                    @endphp

                    <div class="d-flex align-items-start gap-3 pb-4 mb-4 border-bottom-sh">
                        <img src="{{ $serviceImage }}" 
                             alt="{{ $service->title }}" 
                             onerror="this.onerror=null;this.src='{{ $defaultImage }}';" 
                             style="width: 110px; height: 75px; object-fit: cover; border-radius: var(--radius-md); flex-shrink: 0;">
                        <div class="flex-grow-1">
                            <span class="badge-sh badge-sh-primary mb-1">
                                <i class="bi {{ $categoryIcon }} me-1"></i>{{ $categoryName }}
                            </span>
                            <h6 class="mb-1 fw-semibold" style="font-size: var(--fs-base);">
                                <a href="{{ route('service.show', $service->slug) }}" class="text-dark text-decoration-none">{{ $service->title }}</a>
                            </h6>
                            @if(!empty($service->description))
                                <p class="text-secondary mb-2" style="font-size: var(--fs-xs); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ Str::limit(strip_tags($service->description), 140) }}
                                </p>
                            @endif
                            <div class="d-flex align-items-center gap-2 text-secondary" style="font-size: var(--fs-xs);">
                                <span>Freelancer: <strong>{{ $freelancer['name'] ?? 'SkillHive Student' }}</strong></span>
                                @if(!empty($freelancer['verified']))
                                    <i class="bi bi-patch-check-fill text-primary" title="Verified Student"></i>
                                @endif
                                <span>•</span>
                                <span><i class="bi bi-star-fill text-warning"></i> {{ number_format((float) ($service->rating ?? 5.0), 1) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Package Tier Selector --}}
                    <div class="mb-3">
                        <label class="form-label-sh mb-2">Selected Package Tier</label>
                        <div class="row g-2">
                            @foreach($service->packages as $key => $pkg)
                                <div class="col-4">
                                    <div class="p-3 border rounded text-center cursor-pointer package-selector-option {{ $key === $packageKey ? 'border-primary bg-primary-subtle' : '' }}" onclick="switchCheckoutPackage('{{ $key }}')">
                                        <strong class="d-block text-dark">{{ $pkg['name'] ?? ucfirst($key) }}</strong>
                                        <span class="text-primary fw-semibold" style="font-size: var(--fs-sm);">₹{{ number_format($pkg['price'] ?? 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Package Specs --}}
                    <div class="p-3 bg-alt border rounded">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <strong class="text-dark">{{ $package['name'] }} Package Includes:</strong>
                            <span class="badge-sh badge-sh-success"><i class="bi bi-clock me-1"></i>{{ $package['delivery'] }} {{ Str::plural('Day', (int)$package['delivery']) }} Delivery</span>
                        </div>
                        <ul class="package-features mb-0" style="font-size: var(--fs-sm);">
                            @foreach($package['features'] as $feat)
                                <li><i class="bi bi-check-lg text-success"></i> {{ $feat }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Project Requirements Card --}}
                <div class="card-sh p-4 mb-4">
                    <h5 class="service-section-title mb-3">2. Project Requirements & Brief</h5>
                    <p class="text-secondary" style="font-size: var(--fs-sm); margin-bottom: var(--space-3);">
                        Provide the necessary instructions, guidelines, and specifications for <strong>{{ $freelancer['name'] ?? 'the freelancer' }}</strong> to start working on your project.
                    </p>

                    <div class="mb-3">
                        <label class="form-label-sh" for="requirements">Project Brief & Instructions <span class="text-danger">*</span></label>
                        <textarea class="form-control-sh w-100 @error('requirements') is-invalid @enderror" id="requirements" name="requirements" rows="5" placeholder="e.g. Please build a 5-page Laravel website with a contact form. Colors: Purple & White. Deadline: Next Friday." required>{{ old('requirements', request('requirements')) }}</textarea>
                        @error('requirements')
                            <div class="invalid-feedback-sh">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="form-label-sh">Attach Reference Files (Optional)</label>
                        <input type="file" class="form-control-sh w-100" style="font-size: var(--fs-sm);" disabled title="File attachment placeholder">
                        <span class="text-muted" style="font-size: var(--fs-xs);">Supported: PDF, DOCX, ZIP, PNG, JPG (Max 25MB)</span>
                    </div>
                </div>

                {{-- Simulated Payment Method Card --}}
                <div class="card-sh p-4">
                    <h5 class="service-section-title mb-3">3. Payment Method (Simulated)</h5>
                    <p class="text-secondary" style="font-size: var(--fs-sm); margin-bottom: var(--space-4);">
                        Payments are securely held in escrow until you review and approve the completed work.
                    </p>

                    <div class="d-flex flex-column gap-3 mb-3">
                        <label class="d-flex align-items-center gap-3 p-3 border rounded cursor-pointer bg-alt">
                            <input type="radio" name="payment_method" value="escrow" checked class="form-check-input mt-0">
                            <div>
                                <strong class="d-block text-dark"><i class="bi bi-shield-check text-success me-1"></i> SkillHive Student Escrow (Recommended)</strong>
                                <span class="text-secondary" style="font-size: var(--fs-xs);">Payment released to freelancer only after your final approval.</span>
                            </div>
                        </label>

                        <label class="d-flex align-items-center gap-3 p-3 border rounded cursor-pointer">
                            <input type="radio" name="payment_method" value="upi" class="form-check-input mt-0">
                            <div>
                                <strong class="d-block text-dark"><i class="bi bi-qr-code-scan text-primary me-1"></i> UPI / GPay / PhonePe (Simulated)</strong>
                                <span class="text-secondary" style="font-size: var(--fs-xs);">Instant payment transfer.</span>
                            </div>
                        </label>

                        <label class="d-flex align-items-center gap-3 p-3 border rounded cursor-pointer">
                            <input type="radio" name="payment_method" value="card" class="form-check-input mt-0">
                            <div>
                                <strong class="d-block text-dark"><i class="bi bi-credit-card text-dark me-1"></i> Credit / Debit Card (Simulated)</strong>
                                <span class="text-secondary" style="font-size: var(--fs-xs);">Visa, Mastercard, RuPay.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Order Summary Sidebar Column --}}
            <div class="col-lg-4">
                <div class="service-detail-sidebar">
                    <div class="card-sh p-4">
                        <h5 class="service-section-title mb-3">Order Summary</h5>

                        <div class="d-flex justify-content-between py-2 border-bottom-sh" style="font-size: var(--fs-sm);">
                            <span class="text-secondary">Subtotal ({{ $package['name'] }})</span>
                            <strong class="text-dark">₹{{ number_format($package['price']) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between py-2 border-bottom-sh" style="font-size: var(--fs-sm);">
                            <span class="text-secondary">Platform & Escrow Fee</span>
                            <strong class="text-dark">₹{{ number_format($platformFee) }}</strong>
                        </div>

                        <div class="d-flex justify-content-between py-3 my-2">
                            <strong class="text-dark" style="font-size: var(--fs-md);">Total Amount</strong>
                            <strong class="text-primary" style="font-size: var(--fs-xl);">₹{{ number_format($totalAmount) }}</strong>
                        </div>

                        <div class="p-3 bg-alt border rounded mb-4" style="font-size: var(--fs-xs);">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-clock-history text-primary"></i>
                                <strong>Expected Delivery:</strong>
                            </div>
                            <span class="text-secondary">{{ $package['delivery'] }} {{ Str::plural('day', (int)$package['delivery']) }} after freelancer acceptance</span>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn-sh btn-sh-primary btn-sh-lg w-100">
                                <i class="bi bi-lock-fill me-1"></i> Confirm Order (₹{{ number_format($totalAmount) }})
                            </button>
                        </div>
                    </div>

                    <div class="card-sh p-3 mt-4 text-center bg-alt" style="font-size: var(--fs-xs);">
                        <i class="bi bi-patch-check-fill text-success fs-4 mb-1 d-block"></i>
                        <strong class="d-block text-dark">Verified Student Order</strong>
                        <span class="text-secondary">Your order supports student freelancers.</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function switchCheckoutPackage(pkgKey) {
    const reqField = document.getElementById('requirements');
    const reqVal = reqField && reqField.value ? encodeURIComponent(reqField.value) : '';
    let targetUrl = "{{ route('checkout', $service->slug) }}?package=" + encodeURIComponent(pkgKey);
    if (reqVal) {
        targetUrl += "&requirements=" + reqVal;
    }
    window.location.href = targetUrl;
}
</script>
@endpush
