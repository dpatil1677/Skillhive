@extends('layouts.guest')

@section('title', 'How It Works')
@section('meta_description', 'Learn how SkillHive works — find a service, connect with a student freelancer, place your order and get your work done.')

@section('content')

{{-- Page Header --}}
<div style="background-color: var(--color-bg-alt); border-bottom: 1px solid var(--color-border); padding: var(--space-10) 0;">
    <div class="container">
        @include('components.breadcrumb', ['items' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'How It Works'],
        ]])
        <h1 style="font-size: var(--fs-3xl); margin-bottom: var(--space-2);">How SkillHive Works</h1>
        <p style="color: var(--color-text-secondary); font-size: var(--fs-lg); margin: 0; max-width: 540px;">Getting your project done with a student freelancer is simple and transparent.</p>
    </div>
</div>

{{-- Steps --}}
<section class="section-sh-lg">
    <div class="container">
        {{-- Step 1 --}}
        <div class="row align-items-center g-5 mb-5 pb-5" style="border-bottom: 1px solid var(--color-border);">
            <div class="col-lg-6">
                <span class="badge-sh badge-sh-primary mb-3" style="font-size: var(--fs-sm); padding: var(--space-2) var(--space-4);">Step 1</span>
                <h2 style="font-size: var(--fs-2xl); margin-bottom: var(--space-4);">Find a Service</h2>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); margin-bottom: var(--space-6); line-height: var(--lh-relaxed);">
                    Browse through hundreds of services listed by verified student freelancers. Use our search and category filters to narrow down exactly what you need.
                </p>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Search & Filter</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Filter by category, price range, delivery time, and seller rating.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">View Portfolios</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">See sample work and past projects before making any decisions.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Read Reviews</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Check verified reviews from previous buyers to make an informed decision.</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="card-sh p-5" style="background: var(--color-bg-alt);">
                    <div class="text-center mb-4">
                        <div style="width: 80px; height: 80px; background: var(--color-primary-light); border-radius: var(--radius-full); display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--color-primary);">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                    @include('components.search-bar', ['placeholder' => 'e.g. Logo Design, Python Script...'])
                    <div class="d-flex flex-wrap gap-2 mt-3">
                        <span class="badge-sh badge-sh-secondary">Web Dev</span>
                        <span class="badge-sh badge-sh-secondary">Design</span>
                        <span class="badge-sh badge-sh-secondary">Python</span>
                        <span class="badge-sh badge-sh-secondary">Video</span>
                        <span class="badge-sh badge-sh-secondary">Writing</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2 --}}
        <div class="row align-items-center g-5 mb-5 pb-5" style="border-bottom: 1px solid var(--color-border);">
            <div class="col-lg-6 order-lg-2">
                <span class="badge-sh badge-sh-primary mb-3" style="font-size: var(--fs-sm); padding: var(--space-2) var(--space-4);">Step 2</span>
                <h2 style="font-size: var(--fs-2xl); margin-bottom: var(--space-4);">Connect With a Student</h2>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); margin-bottom: var(--space-6); line-height: var(--lh-relaxed);">
                    Once you find a service you like, view the freelancer's full profile, portfolio, and past reviews. Send a message to discuss your specific requirements.
                </p>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Full Profile View</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">See the student's college, skills, experience and completed projects.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Direct Messaging</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Chat directly to clarify requirements, scope, and timeline.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Verified Badge</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">All verified students display a badge confirming their student status.</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 order-lg-1 d-none d-lg-block">
                <div class="card-sh p-5" style="background: var(--color-bg-alt);">
                    <div style="display: flex; align-items: center; gap: var(--space-4); margin-bottom: var(--space-5);">
                        <img src="https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=80" alt="Dhruv" style="width: 64px; height: 64px; border-radius: 50%;">
                        <div>
                            <strong style="display: block;">Dhruv Patil</strong>
                            <span style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Full Stack Developer · MIT Pune</span>
                            <div style="display: flex; align-items: center; gap: 4px; margin-top: 4px;">
                                <i class="bi bi-star-fill" style="color: var(--color-star); font-size: 0.75rem;"></i>
                                <strong style="font-size: var(--fs-sm);">4.9</strong>
                                <span style="font-size: var(--fs-sm); color: var(--color-text-secondary);">(47 reviews)</span>
                                <span class="badge-sh badge-sh-success ms-1"><i class="bi bi-patch-check-fill"></i> Verified</span>
                            </div>
                        </div>
                    </div>
                    <div style="background: var(--color-bg); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: var(--space-3) var(--space-4); font-size: var(--fs-sm); color: var(--color-text-secondary); margin-bottom: var(--space-3);">
                        <i class="bi bi-chat-dots-fill" style="color: var(--color-primary); margin-right: 4px;"></i>
                        "Hi! I'd love to help build your project. Can you share more details?"
                    </div>
                    <a href="{{ route('login') }}" class="btn-sh btn-sh-primary w-100" onclick="event.preventDefault(); showAuthModal();">
                        <i class="bi bi-chat"></i> Send a Message
                    </a>
                </div>
            </div>
        </div>

        {{-- Step 3 --}}
        <div class="row align-items-center g-5 mb-5 pb-5" style="border-bottom: 1px solid var(--color-border);">
            <div class="col-lg-6">
                <span class="badge-sh badge-sh-primary mb-3" style="font-size: var(--fs-sm); padding: var(--space-2) var(--space-4);">Step 3</span>
                <h2 style="font-size: var(--fs-2xl); margin-bottom: var(--space-4);">Place Your Order</h2>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); margin-bottom: var(--space-6); line-height: var(--lh-relaxed);">
                    Once you agree on scope and price, place your order securely through the platform. Your payment is held safely until the work is delivered to your satisfaction.
                </p>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Secure Payment</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Payments are held in escrow and only released after your approval.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Clear Timeline</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Every order has a defined delivery date with progress tracking.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">No Hidden Fees</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">The price you see is the price you pay — completely transparent.</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="card-sh p-5" style="background: var(--color-bg-alt);">
                    <h6 style="margin-bottom: var(--space-5);">Order Summary</h6>
                    <div style="display: flex; justify-content: space-between; padding: var(--space-3) 0; border-bottom: 1px solid var(--color-border); font-size: var(--fs-sm);">
                        <span style="color: var(--color-text-secondary);">Modern Laravel Website</span>
                        <strong>₹2,499</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: var(--space-3) 0; border-bottom: 1px solid var(--color-border); font-size: var(--fs-sm);">
                        <span style="color: var(--color-text-secondary);">Delivery Time</span>
                        <span>5 days</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: var(--space-3) 0; border-bottom: 1px solid var(--color-border); font-size: var(--fs-sm);">
                        <span style="color: var(--color-text-secondary);">Service Fee</span>
                        <span>₹0</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding: var(--space-4) 0 var(--space-5);">
                        <strong>Total</strong>
                        <strong style="color: var(--color-primary); font-size: var(--fs-lg);">₹2,499</strong>
                    </div>
                    <div class="d-grid">
                        <a href="{{ route('login') }}" class="btn-sh btn-sh-primary btn-sh-lg" onclick="event.preventDefault(); showAuthModal();">
                            <i class="bi bi-bag-check"></i> Place Order
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4 --}}
        <div class="row align-items-center g-5">
            <div class="col-lg-6 order-lg-2">
                <span class="badge-sh badge-sh-primary mb-3" style="font-size: var(--fs-sm); padding: var(--space-2) var(--space-4);">Step 4</span>
                <h2 style="font-size: var(--fs-2xl); margin-bottom: var(--space-4);">Get Your Work</h2>
                <p style="color: var(--color-text-secondary); font-size: var(--fs-md); margin-bottom: var(--space-6); line-height: var(--lh-relaxed);">
                    Receive your completed deliverables on time. Review the work, request revisions if needed, and approve to release the payment. Leave a review to help the community.
                </p>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">On-Time Delivery</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Freelancers are committed to their promised delivery dates.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Free Revisions</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Most services include revision rounds so you get exactly what you need.</span>
                        </div>
                    </li>
                    <li style="display: flex; align-items: flex-start; gap: var(--space-3);">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: var(--fs-lg); margin-top: 2px; flex-shrink: 0;"></i>
                        <div>
                            <strong style="display: block; margin-bottom: 2px;">Leave a Review</strong>
                            <span style="color: var(--color-text-secondary); font-size: var(--fs-sm);">Share your experience to help future buyers make better decisions.</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 order-lg-1 d-none d-lg-block">
                <div class="card-sh p-5" style="background: var(--color-bg-alt); text-align: center;">
                    <div style="font-size: 4rem; color: var(--color-success); margin-bottom: var(--space-4);">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h5 style="margin-bottom: var(--space-2);">Order Completed!</h5>
                    <p style="color: var(--color-text-secondary); font-size: var(--fs-sm); margin-bottom: var(--space-5);">Your project has been delivered. Review and approve to release payment.</p>
                    <div class="d-flex justify-content-center mb-3">
                        @include('components.star-rating', ['rating' => 5])
                    </div>
                    <a href="{{ route('login') }}" class="btn-sh btn-sh-primary" onclick="event.preventDefault(); showAuthModal();">
                        <i class="bi bi-star"></i> Leave a Review
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="section-sh section-sh--alt">
    <div class="container">
        <div class="section-header text-center">
            <h2>Frequently Asked Questions</h2>
            <p>Common questions about using SkillHive.</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    @php
                        $faqs = [
                            ['q' => 'Is SkillHive only for students?', 'a' => 'Freelancers on SkillHive must be verified students. However, anyone can hire — students, professionals, businesses, and more.'],
                            ['q' => 'How does payment work?', 'a' => 'When you place an order, your payment is held securely. It is only released to the freelancer after you approve the delivered work.'],
                            ['q' => 'What if I am not satisfied with the work?', 'a' => 'You can request revisions as specified in the service listing. If a resolution cannot be reached, our dispute process ensures fair outcomes.'],
                            ['q' => 'How do I become a seller?', 'a' => 'Register with your student email, complete verification with your college ID, and create your first service listing.'],
                            ['q' => 'Are prices negotiable?', 'a' => 'Listed prices are fixed, but you can message a freelancer to discuss custom packages or bulk requirements.'],
                        ];
                    @endphp
                    @foreach($faqs as $index => $faq)
                        <div class="accordion-item" style="border: 1px solid var(--color-border); margin-bottom: var(--space-2); border-radius: var(--radius-md) !important; overflow: hidden;">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}" style="font-size: var(--fs-base); font-weight: var(--fw-medium); background: var(--color-bg); color: var(--color-text);">
                                    {{ $faq['q'] }}
                                </button>
                            </h2>
                            <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: var(--fs-base); color: var(--color-text-secondary); background: var(--color-bg);">
                                    {{ $faq['a'] }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-banner">
    <div class="container">
        <h2>Ready to get started?</h2>
        <p>Browse hundreds of services from verified student freelancers.</p>
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
