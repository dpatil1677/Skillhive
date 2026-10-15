@extends('layouts.guest')

@section('title', 'SkillHive')
@section('meta_description', 'SkillHive — Student Freelance Marketplace. Find talented students for web development, design, content, video editing and more. Your skills deserve an audience.')

@section('content')

{{-- Hero Section --}}
<section class="hero-sh">
    <div class="container">
        <h1>Find the right student for your next project.</h1>
        <p>Discover talented students for development, design, content, video and more.</p>

        @include('components.search-bar', ['size' => 'lg', 'placeholder' => 'What service are you looking for?'])

        <div class="popular-searches">
            <span>Popular:</span>
            <a href="{{ route('explore', ['q' => 'Web Development']) }}">Web Development</a>
            <a href="{{ route('explore', ['q' => 'Logo Design']) }}">Logo Design</a>
            <a href="{{ route('explore', ['q' => 'Video Editing']) }}">Video Editing</a>
            <a href="{{ route('explore', ['q' => 'UI/UX']) }}">UI/UX</a>
            <a href="{{ route('explore', ['q' => 'Python']) }}">Python</a>
            <a href="{{ route('explore', ['q' => 'Presentation Design']) }}">Presentation Design</a>
        </div>
    </div>
</section>

{{-- Popular Categories --}}
<section class="section-sh section-sh--alt">
    <div class="container">
        <div class="section-header">
            <h2>Popular Categories</h2>
            <p>Browse services by category to find exactly what you need.</p>
        </div>
        <div class="row g-3">
            @foreach($categories as $category)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.category-card', ['category' => $category])
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Trending Services --}}
<section class="section-sh">
    <div class="container">
        <div class="section-header d-flex align-items-end justify-content-between">
            <div>
                <h2>Trending Services</h2>
                <p>Popular services from top student freelancers.</p>
            </div>
            <a href="{{ route('explore') }}" class="btn-sh btn-sh-outline btn-sh-sm d-none d-md-inline-flex">
                View All <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="row g-4">
            @foreach($services as $service)
                <div class="col-lg-3 col-md-4 col-sm-6">
                    @include('components.service-card', ['service' => $service])
                </div>
            @endforeach
        </div>
        <div class="text-center mt-5 d-md-none">
            <a href="{{ route('explore') }}" class="btn-sh btn-sh-outline">
                View All Services <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

{{-- Top Student Freelancers --}}
<section class="section-sh section-sh--alt">
    <div class="container">
        <div class="section-header">
            <h2>Top Student Freelancers</h2>
            <p>Verified students delivering quality work across multiple disciplines.</p>
        </div>
        <div class="row g-4">
            @foreach($freelancers as $freelancer)
                <div class="col-lg col-md-4 col-6">
                    @include('components.freelancer-card', ['freelancer' => $freelancer])
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- How SkillHive Works --}}
<section class="section-sh">
    <div class="container">
        <div class="section-header text-center">
            <h2>How SkillHive Works</h2>
            <p>Getting your project done is simple and straightforward.</p>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-card__number">1</div>
                    <div class="step-card__icon"><i class="bi bi-search"></i></div>
                    <h5>Find a Service</h5>
                    <p>Browse categories or search for the specific service you need.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-card__number">2</div>
                    <div class="step-card__icon"><i class="bi bi-chat-dots"></i></div>
                    <h5>Connect With a Student</h5>
                    <p>Review profiles, portfolios and ratings. Message to discuss your project.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-card__number">3</div>
                    <div class="step-card__icon"><i class="bi bi-bag-check"></i></div>
                    <h5>Place Your Order</h5>
                    <p>Agree on scope and price. Place your order securely through the platform.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="step-card">
                    <div class="step-card__number">4</div>
                    <div class="step-card__icon"><i class="bi bi-check-circle"></i></div>
                    <h5>Get Your Work</h5>
                    <p>Receive your deliverables, request revisions if needed, and leave a review.</p>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('how-it-works') }}" class="btn-sh btn-sh-secondary">
                Learn More <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

{{-- Student Verification --}}
<section class="section-sh section-sh--alt">
    <div class="container">
        <div class="verification-section">
            <div class="flex-grow-1">
                <span class="badge-sh badge-sh-success mb-3">
                    <i class="bi bi-shield-check"></i> Verified Students
                </span>
                <h2 style="margin-bottom: var(--space-4);">Quality you can trust.</h2>
                <p class="text-secondary mb-4" style="font-size: var(--fs-md); max-width: 480px;">
                    Every freelancer on SkillHive is a verified student. We ensure quality through our verification process and review system.
                </p>
                <ul class="verification-features">
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Student ID Verification</strong>
                            <span>Every freelancer verifies their student status with a valid college ID.</span>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Skill Assessment</strong>
                            <span>Portfolios and past work are reviewed before freelancers can list services.</span>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Transparent Reviews</strong>
                            <span>All reviews are from verified buyers. No fake ratings allowed.</span>
                        </div>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill"></i>
                        <div>
                            <strong>Secure Payments</strong>
                            <span>Payments are held securely until you approve the delivered work.</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="d-none d-lg-block" style="min-width: 380px;">
                <div class="card-sh p-5 text-center">
                    <div style="font-size: 4rem; color: var(--color-primary); margin-bottom: var(--space-4);">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 style="margin-bottom: var(--space-2);">500+ Verified Students</h4>
                    <p class="text-secondary mb-4" style="font-size: var(--fs-sm);">
                        From 50+ colleges across India
                    </p>
                    <div class="d-flex justify-content-center gap-4">
                        <div class="stat-item">
                            <div class="stat-item__number" style="font-size: var(--fs-2xl);">98%</div>
                            <div class="stat-item__label">Satisfaction</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-item__number" style="font-size: var(--fs-2xl);">2.5k</div>
                            <div class="stat-item__label">Orders Done</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Testimonials --}}
<section class="section-sh">
    <div class="container">
        <div class="section-header text-center">
            <h2>What Clients Say</h2>
            <p>Real feedback from students and professionals who have used SkillHive.</p>
        </div>
        <div class="row g-4">
            @foreach($testimonials as $testimonial)
                <div class="col-lg-4 col-md-6">
                    <div class="card-sh testimonial-card">
                        <div class="mb-3">
                            @include('components.star-rating', ['rating' => $testimonial['rating']])
                        </div>
                        <p class="testimonial-card__text">"{{ $testimonial['text'] }}"</p>
                        <div class="testimonial-card__author">
                            <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] }}" class="testimonial-card__avatar">
                            <div>
                                <div class="testimonial-card__name">{{ $testimonial['name'] }}</div>
                                <div class="testimonial-card__role">{{ $testimonial['role'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="container">
        <h2>Ready to get started?</h2>
        <p>Join SkillHive today and connect with talented student freelancers.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('explore') }}" class="btn-sh btn-sh-white btn-sh-lg">
                <i class="bi bi-compass"></i> Explore Services
            </a>
            <a href="{{ route('register') }}" class="btn-sh btn-sh-dark btn-sh-lg">
                Join as a Student <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

@endsection
