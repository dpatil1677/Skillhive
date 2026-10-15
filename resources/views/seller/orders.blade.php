@extends('layouts.guest')

@section('title', 'Incoming Orders — SkillHive Seller')

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Seller Dashboard', 'url' => route('seller.dashboard')],
        ['label' => 'Incoming Orders'],
    ]])

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Incoming Orders</h1>
            <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Manage, accept, and fulfil client orders for your services.</p>
        </div>
        <a href="{{ route('seller.services.create') }}" class="btn-sh btn-sh-primary btn-sh-sm">
            <i class="bi bi-plus-lg me-1"></i> Add New Service
        </a>
    </div>

    {{-- Filter Tabs --}}
    <div class="profile-tabs mb-4">
        <a href="{{ route('seller.orders', ['tab' => 'all']) }}" class="profile-tab {{ $activeTab === 'all' ? 'active' : '' }}">
            All ({{ $counts['all'] }})
        </a>
        <a href="{{ route('seller.orders', ['tab' => 'pending']) }}" class="profile-tab {{ $activeTab === 'pending' ? 'active' : '' }}">
            🔔 Pending ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('seller.orders', ['tab' => 'active']) }}" class="profile-tab {{ $activeTab === 'active' ? 'active' : '' }}">
            ⚡ Active Work ({{ $counts['active'] }})
        </a>
        <a href="{{ route('seller.orders', ['tab' => 'completed']) }}" class="profile-tab {{ $activeTab === 'completed' ? 'active' : '' }}">
            ✅ Completed ({{ $counts['completed'] }})
        </a>
        <a href="{{ route('seller.orders', ['tab' => 'cancelled']) }}" class="profile-tab {{ $activeTab === 'cancelled' ? 'active' : '' }}">
            Cancelled ({{ $counts['cancelled'] }})
        </a>
    </div>

    {{-- Orders List --}}
    <div class="d-flex flex-column gap-3 mb-5">
        @forelse($orders as $order)
            @php
                $rawSt     = $order->raw_status;
                $statusLabel = $order->status; // uses the mutator → "Pending", "Accepted", etc.
                $badgeClass  = match($rawSt) {
                    'completed'             => 'badge-sh-success',
                    'cancelled'             => 'badge-sh-danger',
                    'accepted','in_progress','submitted' => 'badge-sh-primary',
                    default                 => 'badge-sh-warning',
                };
                $serviceTitle = $order->service ? $order->service->title : 'Unknown Service';
                $serviceImage = $order->service ? ($order->service->image ?: 'https://placehold.co/120x80/e8e0f5/6D28D9?text=Service') : 'https://placehold.co/120x80/e8e0f5/6D28D9?text=Service';
                $serviceSlug  = $order->service ? $order->service->slug : '#';
                $clientName   = $order->client  ? $order->client->name  : 'Client';
                $clientAvatar = $order->client  ? $order->client->avatar : 'https://ui-avatars.com/api/?name=Client&background=6D28D9&color=fff&size=64';
            @endphp
            <div class="card-sh p-4">
                <div class="row align-items-center g-3">

                    {{-- Service Info --}}
                    <div class="col-lg-5 col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $serviceImage }}" alt="{{ $serviceTitle }}"
                                 style="width: 84px; height: 56px; object-fit: cover; border-radius: var(--radius-md); flex-shrink: 0;">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <strong class="text-dark" style="font-size: var(--fs-sm);">{{ $order->order_number }}</strong>
                                    <span class="badge-sh {{ $badgeClass }}">{{ $statusLabel }}</span>
                                </div>
                                <h6 class="mb-0 fw-semibold" style="font-size: var(--fs-sm);">
                                    <a href="{{ route('seller.orders.show', $order->order_number) }}" class="text-dark text-decoration-none">{{ $serviceTitle }}</a>
                                </h6>
                                <span class="text-muted" style="font-size: 11px;">
                                    Placed {{ $order->created_at ? $order->created_at->diffForHumans() : 'Recently' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Client Info --}}
                    <div class="col-lg-3 col-md-3">
                        <span class="text-secondary" style="font-size: var(--fs-xs);">Client:</span>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <img src="{{ $clientAvatar }}" alt="{{ $clientName }}"
                                 style="width: 26px; height: 26px; border-radius: 50%; object-fit: cover;">
                            <strong class="text-dark" style="font-size: var(--fs-sm);">{{ $clientName }}</strong>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="col-lg-2 col-md-3 text-md-end">
                        <span class="text-secondary" style="font-size: var(--fs-xs);">Earn:</span>
                        <strong class="text-primary d-block" style="font-size: var(--fs-md);">
                            ₹{{ number_format($order->price) }}
                        </strong>
                    </div>

                    {{-- Actions --}}
                    <div class="col-lg-2 col-md-12 text-lg-end">
                        <div class="d-flex gap-2 justify-content-lg-end flex-wrap">
                            <a href="{{ route('seller.orders.show', $order->order_number) }}" class="btn-sh btn-sh-outline btn-sh-sm">
                                View Details
                            </a>
                            @if($rawSt === 'pending')
                                <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="status" value="Accepted">
                                    <button type="submit" class="btn-sh btn-sh-primary btn-sh-sm">
                                        ✓ Accept
                                    </button>
                                </form>
                            @elseif(in_array($rawSt, ['accepted', 'in_progress']))
                                <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="status" value="Submitted">
                                    <button type="submit" class="btn-sh btn-sh-success btn-sh-sm">
                                        Submit Work
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            @include('components.empty-state', [
                'icon'        => 'bi-inbox',
                'title'       => 'No orders found',
                'text'        => 'You have no ' . ($activeTab !== 'all' ? $activeTab : '') . ' orders yet.',
                'actionUrl'   => route('seller.services.create'),
                'actionLabel' => 'Create a Service to Attract Clients',
            ])
        @endforelse
    </div>
</div>

@endsection
