@extends('layouts.guest')

@section('title', 'My Orders — SkillHive')

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home',      'url' => route('home')],
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'My Orders'],
    ]])

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">My Orders</h1>
            <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Track and manage all your purchased services.</p>
        </div>
        <a href="{{ route('explore') }}" class="btn-sh btn-sh-primary">
            <i class="bi bi-plus-lg me-1"></i> Browse Services
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="profile-tabs mb-4">
        <a href="{{ route('orders.index', ['tab' => 'all']) }}" class="profile-tab {{ $activeTab === 'all' ? 'active' : '' }}">
            All Orders ({{ $counts['all'] }})
        </a>
        <a href="{{ route('orders.index', ['tab' => 'active']) }}" class="profile-tab {{ $activeTab === 'active' ? 'active' : '' }}">
            Active ({{ $counts['active'] }})
        </a>
        <a href="{{ route('orders.index', ['tab' => 'completed']) }}" class="profile-tab {{ $activeTab === 'completed' ? 'active' : '' }}">
            Completed ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('orders.index', ['tab' => 'cancelled']) }}" class="profile-tab {{ $activeTab === 'cancelled' ? 'active' : '' }}">
            Cancelled ({{ $counts['cancelled'] }})
        </a>
    </div>

    {{-- Orders List --}}
    <div class="d-flex flex-column gap-3 mb-5">
        @forelse($orders as $order)
            @php
                $rawSt      = $order->raw_status;
                $badgeClass = match($rawSt) {
                    'completed'                          => 'badge-sh-success',
                    'cancelled'                          => 'badge-sh-danger',
                    'accepted','in_progress','submitted' => 'badge-sh-primary',
                    default                              => 'badge-sh-warning',
                };
                $service         = $order->service;
                $freelancerModel = $order->freelancer;
                $serviceTitle    = $service ? $service->title : 'Service';
                $serviceImg      = $service ? ($service->image ?: 'https://placehold.co/90x60/e8e0f5/6D28D9?text=Service') : '';
                $freelancerName   = $freelancerModel ? $freelancerModel->name   : 'Freelancer';
                $freelancerAvatar = $freelancerModel ? $freelancerModel->avatar : 'https://ui-avatars.com/api/?name=FL&background=6D28D9&color=fff&size=64';
                $freelancerSlug   = $freelancerModel ? $freelancerModel->slug   : '#';
            @endphp
            <div class="card-sh p-4">
                <div class="row align-items-center g-3">

                    {{-- Service Info --}}
                    <div class="col-lg-5 col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $serviceImg }}" alt="{{ $serviceTitle }}"
                                 style="width: 90px; height: 60px; object-fit: cover; border-radius: var(--radius-md); flex-shrink: 0;">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <strong class="text-dark" style="font-size: var(--fs-sm);">{{ $order->order_number }}</strong>
                                    <span class="badge-sh {{ $badgeClass }}">{{ $order->status }}</span>
                                </div>
                                <h6 class="mb-1 fw-semibold" style="font-size: var(--fs-base);">
                                    <a href="{{ route('orders.show', $order->order_number) }}" class="text-dark text-decoration-none">{{ $serviceTitle }}</a>
                                </h6>
                                <span class="text-secondary d-block" style="font-size: var(--fs-xs);">
                                    Package: <strong>{{ $order->package_name }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Freelancer --}}
                    <div class="col-lg-3 col-md-3">
                        <div class="text-secondary" style="font-size: var(--fs-xs);">Freelancer</div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <img src="{{ $freelancerAvatar }}" alt="{{ $freelancerName }}"
                                 style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                            <a href="{{ route('freelancer.show', $freelancerSlug) }}" class="text-dark fw-medium text-decoration-none" style="font-size: var(--fs-sm);">
                                {{ $freelancerName }}
                            </a>
                        </div>
                        <span class="text-muted d-block mt-1" style="font-size: 11px;">
                            Placed {{ $order->created_at ? $order->created_at->diffForHumans() : '—' }}
                        </span>
                    </div>

                    {{-- Amount --}}
                    <div class="col-lg-2 col-md-3 text-md-end">
                        <div class="text-secondary" style="font-size: var(--fs-xs);">Total Amount</div>
                        <strong class="text-primary d-block" style="font-size: var(--fs-lg);">
                            ₹{{ number_format($order->total_amount) }}
                        </strong>
                    </div>

                    {{-- Action --}}
                    <div class="col-lg-2 col-md-12 text-lg-end">
                        <a href="{{ route('orders.show', $order->order_number) }}" class="btn-sh btn-sh-primary btn-sh-sm w-100">
                            View Order
                        </a>
                    </div>
                </div>
            </div>
        @empty
            @include('components.empty-state', [
                'icon'        => 'bi-inbox',
                'title'       => 'No orders found',
                'text'        => 'You do not have any ' . ($activeTab !== 'all' ? $activeTab . ' ' : '') . 'orders yet.',
                'actionUrl'   => route('explore'),
                'actionLabel' => 'Explore Marketplace',
            ])
        @endforelse
    </div>
</div>

@endsection
