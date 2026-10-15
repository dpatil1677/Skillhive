@extends('layouts.guest')

@section('title', 'Dashboard — SkillHive')

@section('content')

<div class="container section-sh-sm">
    {{-- Dashboard Header --}}
    <div class="card-sh p-4 mb-5" style="background: linear-gradient(135deg, var(--color-primary-subtle) 0%, var(--color-bg) 100%); border-color: var(--color-primary-light);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary);">
                <div>
                    <h2 class="mb-1" style="font-size: var(--fs-2xl); font-weight: var(--fw-bold);">
                        Good {{ date('H') < 12 ? 'morning' : (date('H') < 17 ? 'afternoon' : 'evening') }}, {{ $user['name'] }}!
                    </h2>
                    <div class="text-secondary" style="font-size: var(--fs-sm);">
                        <i class="bi bi-mortarboard-fill me-1 text-primary"></i> {{ $user['college'] }} • {{ $user['course'] }}
                        <span class="badge-sh badge-sh-primary ms-2"><i class="bi bi-person-badge"></i> Client & Seller</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('explore') }}" class="btn-sh btn-sh-primary btn-sh-sm">
                    <i class="bi bi-compass"></i> Explore Services
                </a>
                <a href="{{ route('profile.edit') }}" class="btn-sh btn-sh-outline btn-sh-sm">
                    <i class="bi bi-gear"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: var(--color-primary-light); color: var(--color-primary); font-size: 1.5rem;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">{{ $activeOrdersCount }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Active Orders</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: var(--color-success-light); color: var(--color-success); font-size: 1.5rem;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">{{ $completedOrdersCount }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Completed Orders</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #e0f2fe; color: #0284c7; font-size: 1.5rem;">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">₹{{ number_format($totalSpent) }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Total Spent</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: var(--color-warning-light); color: #92400E; font-size: 1.5rem;">
                    <i class="bi bi-star"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">{{ number_format($avgRating, 1) }} ★</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Average Rating</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        {{-- Left Column: Recent Orders & Recent Messages --}}
        <div class="col-lg-8">
            {{-- Recent Orders Section --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 style="font-size: var(--fs-lg); font-weight: var(--fw-semibold); margin: 0;">Recent Orders</h4>
                <a href="{{ route('orders.index') }}" class="btn-sh btn-sh-outline btn-sh-sm">View All Orders</a>
            </div>

            <div class="d-flex flex-column gap-3 mb-5">
                @forelse($recentOrders as $order)
                    <div class="card-sh p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $order['service']['image'] }}" alt="{{ $order['service']['title'] }}" style="width: 72px; height: 50px; object-fit: cover; border-radius: var(--radius-sm);">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <strong class="text-dark" style="font-size: var(--fs-sm);">{{ $order['order_number'] }}</strong>
                                    @php
                                        $statusBadgeClass = match($order['status']) {
                                            'Completed' => 'badge-sh-success',
                                            'Cancelled' => 'badge-sh-danger',
                                            'In Progress', 'Accepted' => 'badge-sh-primary',
                                            default => 'badge-sh-warning',
                                        };
                                    @endphp
                                    <span class="badge-sh {{ $statusBadgeClass }}">{{ $order['status'] }}</span>
                                </div>
                                <div class="fw-medium text-dark" style="font-size: var(--fs-sm);">
                                    <a href="{{ route('orders.show', $order['order_number']) }}" class="text-dark text-decoration-none">{{ $order['service']['title'] }}</a>
                                </div>
                                <div class="text-secondary" style="font-size: var(--fs-xs);">
                                    Freelancer: {{ $order['freelancer']['name'] }} • {{ $order['created_at'] }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end">
                                <strong class="d-block text-dark" style="font-size: var(--fs-md);">₹{{ number_format($order['total_amount']) }}</strong>
                                <span class="text-muted" style="font-size: var(--fs-xs);">{{ $order['package_name'] }}</span>
                            </div>
                            <a href="{{ route('orders.show', $order['order_number']) }}" class="btn-sh btn-sh-secondary btn-sh-sm">
                                View Order
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="card-sh p-4 text-center text-muted">No orders yet.</div>
                @endforelse
            </div>

            {{-- Recent Messages Section --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 style="font-size: var(--fs-lg); font-weight: var(--fw-semibold); margin: 0;">Recent Messages</h4>
                <span class="text-muted" style="font-size: var(--fs-sm);">Inbox</span>
            </div>

            <div class="card-sh p-0 overflow-hidden mb-4">
                @foreach($recentMessages as $msg)
                    <div class="p-3 border-bottom-sh d-flex align-items-center justify-content-between gap-3 {{ $msg['unread'] ? 'bg-alt' : '' }}">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $msg['sender_avatar'] }}" alt="{{ $msg['sender_name'] }}" style="width: 44px; height: 44px; border-radius: 50%; object-fit: cover;">
                            <div>
                                <strong class="d-block text-dark" style="font-size: var(--fs-sm);">
                                    {{ $msg['sender_name'] }}
                                    @if($msg['unread'])
                                        <span class="badge bg-primary ms-1" style="font-size: 10px;">New</span>
                                    @endif
                                </strong>
                                <span class="text-secondary d-block text-truncate" style="max-width: 380px; font-size: var(--fs-xs);">
                                    {{ $msg['message'] }}
                                </span>
                            </div>
                        </div>
                        <span class="text-muted" style="font-size: var(--fs-xs); white-space: nowrap;">{{ $msg['time'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right Column: Quick Actions & Account Info --}}
        <div class="col-lg-4">
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('explore') }}" class="btn-sh btn-sh-primary text-start">
                        <i class="bi bi-search me-2"></i> Explore Services
                    </a>
                    <a href="{{ route('seller.services.create') }}" class="btn-sh btn-sh-secondary text-start">
                        <i class="bi bi-plus-circle me-2"></i> Create New Service
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn-sh btn-sh-secondary text-start">
                        <i class="bi bi-bag me-2"></i> View All Orders
                    </a>
                    <a href="{{ route('profile.edit') }}" class="btn-sh btn-sh-secondary text-start">
                        <i class="bi bi-person-gear me-2"></i> Edit Profile Settings
                    </a>
                </div>
            </div>

            <div class="card-sh p-4 bg-alt">
                <h5 class="service-section-title mb-3">Account Information</h5>
                <div class="d-flex flex-column gap-2" style="font-size: var(--fs-sm);">
                    <div><span class="text-secondary">Email:</span> <strong class="text-dark d-block">{{ $user['email'] }}</strong></div>
                    <div><span class="text-secondary">College:</span> <strong class="text-dark d-block">{{ $user['college'] }}</strong></div>
                    <div><span class="text-secondary">Course:</span> <strong class="text-dark d-block">{{ $user['course'] }}</strong></div>
                    <div>
                        <span class="text-secondary mb-1 d-block">Skills:</span>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($user['skills'] as $skill)
                                <span class="badge-sh badge-sh-secondary" style="font-size: 11px;">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recommended Services Section --}}
    @if(count($recommendedServices) > 0)
        <div class="pt-4 border-top-sh">
            <h4 class="mb-4" style="font-size: var(--fs-xl);">Recommended Services For You</h4>
            <div class="row g-4">
                @foreach($recommendedServices as $service)
                    <div class="col-lg-3 col-md-6">
                        @include('components.service-card', ['service' => $service])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@endsection
