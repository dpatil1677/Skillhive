@extends('layouts.guest')

@section('title', 'About SkillHive')
@section('meta_description', 'SkillHive is a student freelance marketplace connecting talented students with people who need quality work done. Learn our story and mission.')

@section('content')

{{-- About Hero --}}
<div class="about-hero" style="background-color: var(--color-bg-alt); border-bottom: 1px solid var(--color-border);">
    <div class="container">
        <span class="badge-sh badge-sh-primary mb-4" style="font-size: var(--fs-sm); padding: var(--space-2) var(--space-4);">
            <i class="bi bi-hexagon-fill"></i> About SkillHive
        </span>
        <h1>Built for students who want to do real work.</h1>
        <p>SkillHive is a marketplace where talented students offer professional services — and where anyone can find quality help at fair prices.</p>
    </div>
</div>

{{-- Mission --}}
<section class="section-sh">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge-sh badge-sh-success mb-3">Our Mission</span>
                <h2 style="font-size: var(--fs-2xl); margin-bottom: var(--space-4);">Turning skills into opportunities.</h2>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); line-height: var(--lh-relaxed); margin-bottom: var(--space-4);">
                    Every student has skills. Not every student has an audience. SkillHive bridges that gap — giving students a professional platform to offer their services, build their portfolio, and earn real income while still in college.
                </p>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); line-height: var(--lh-relaxed); margin-bottom: var(--space-6);">
                    We are not trying to replace professional platforms. We are building something specific — a focused, trustworthy space for student talent.
                </p>
                <div class="d-flex gap-4 flex-wrap">
                    <div>
                        <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-primary);">500+</div>
                        <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Student Freelancers</div>
                    </div>
                    <div>
                        <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-primary);">50+</div>
                        <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Colleges Represented</div>
                    </div>
                    <div>
                        <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-primary);">2,500+</div>
                        <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Projects Completed</div>
                    </div>
                    <div>
                        <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-primary);">4.8★</div>
                        <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Average Rating</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    @php
                        $values = [
                            ['icon' => 'bi-mortarboard', 'title' => 'Student First', 'text' => 'Every feature is designed around the student experience — flexible, fair, and encouraging.'],
                            ['icon' => 'bi-shield-check', 'title' => 'Trust & Safety', 'text' => 'Verified IDs, transparent reviews, and secure payments protect everyone on the platform.'],
                            ['icon' => 'bi-briefcase', 'title' => 'Real Opportunities', 'text' => 'Students earn real income, build real portfolios, and develop real professional skills.'],
                            ['icon' => 'bi-people', 'title' => 'Community Driven', 'text' => 'We are built by students for students, guided by the feedback of our community.'],
                        ];
                    @endphp
                    @foreach($values as $value)
                        <div class="col-6">
                            <div class="card-sh value-card">
                                <div class="value-card__icon">
                                    <i class="{{ $value['icon'] }}"></i>
                                </div>
                                <h5>{{ $value['title'] }}</h5>
                                <p>{{ $value['text'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- What SkillHive Offers --}}
<section class="section-sh section-sh--alt">
    <div class="container">
        <div class="section-header text-center">
            <h2>What SkillHive Offers</h2>
            <p>A complete platform for student freelancers and buyers alike.</p>
        </div>
        <div class="row g-4">
            @php
                $offerings = [
                    ['icon' => 'bi-grid-3x3-gap', 'title' => 'Service Marketplace', 'text' => 'Browse and hire from a growing catalog of services across web development, design, video, writing, programming and more.'],
                    ['icon' => 'bi-person-badge', 'title' => 'Freelancer Profiles', 'text' => 'Detailed freelancer profiles with bio, skills, portfolio projects, ratings and verified student status.'],
                    ['icon' => 'bi-images', 'title' => 'Portfolio Builder', 'text' => 'Students can showcase their best work directly on SkillHive — no external portfolio site needed.'],
                    ['icon' => 'bi-star', 'title' => 'Reviews & Ratings', 'text' => 'A transparent review system that holds everyone accountable and helps buyers make informed decisions.'],
                    ['icon' => 'bi-chat-dots', 'title' => 'Direct Messaging', 'text' => 'Communicate privately with sellers to discuss project requirements before placing an order.'],
                    ['icon' => 'bi-bag-check', 'title' => 'Order Management', 'text' => 'Track project progress, communicate with your freelancer, and approve deliverables — all in one place.'],
                ];
            @endphp
            @foreach($offerings as $offering)
                <div class="col-lg-4 col-md-6">
                    <div class="card-sh value-card h-100">
                        <div class="value-card__icon">
                            <i class="{{ $offering['icon'] }}"></i>
                        </div>
                        <h5>{{ $offering['title'] }}</h5>
                        <p>{{ $offering['text'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- For Students --}}
<section class="section-sh">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="badge-sh badge-sh-primary mb-3">For Students</span>
                <h2 style="font-size: var(--fs-2xl); margin-bottom: var(--space-4);">Your skills deserve an audience.</h2>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); line-height: var(--lh-relaxed); margin-bottom: var(--space-4);">
                    Whether you are a developer, designer, writer, editor, or analyst — SkillHive gives you a professional platform to offer your services to real clients.
                </p>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); line-height: var(--lh-relaxed); margin-bottom: var(--space-6);">
                    Build your portfolio, get reviews, earn income and graduate with proof of real work experience.
                </p>
                <a href="{{ route('register') }}" class="btn-sh btn-sh-primary btn-sh-lg">
                    <i class="bi bi-person-plus"></i> Become a Seller
                </a>
            </div>
            <div class="col-lg-6">
                @php
                    $freelancers = \App\Models\User::where('role', 'user')->has('services')->take(4)->get()->map(fn($u) => $u->toFreelancerArray());
                @endphp
                <div class="row g-3">
                    @foreach($freelancers as $freelancer)
                        <div class="col-6">
                            @include('components.freelancer-card', ['freelancer' => $freelancer])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- College Project Note --}}
<section class="section-sh section-sh--alt">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="card-sh p-5" style="background: var(--color-primary-subtle); border-color: var(--color-primary-light);">
                    <i class="bi bi-mortarboard" style="font-size: 2.5rem; color: var(--color-primary); margin-bottom: var(--space-4); display: block;"></i>
                    <h4 style="margin-bottom: var(--space-3);">A College Project with Purpose</h4>
                    <p style="color: var(--color-text-secondary); font-size: var(--fs-md); line-height: var(--lh-relaxed); max-width: 560px; margin: 0 auto var(--space-5);">
                        SkillHive started as a college project with a simple idea — students have real skills that deserve a real platform. It is built to be functional, realistic, and genuinely useful.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('explore') }}" class="btn-sh btn-sh-primary">Explore Services</a>
                        <a href="{{ route('how-it-works') }}" class="btn-sh btn-sh-outline">How It Works</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
