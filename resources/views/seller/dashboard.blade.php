@extends('layouts.guest')

@section('title', 'Seller Dashboard — SkillHive')

@section('content')

<div class="container section-sh-sm">
    {{-- Seller Header --}}
    <div class="card-sh p-4 mb-5" style="background: linear-gradient(135deg, var(--color-success-light) 0%, var(--color-bg) 100%); border-color: #bbf7d0;">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-success);">
                <div>
                    <h2 class="mb-1" style="font-size: var(--fs-2xl); font-weight: var(--fw-bold);">
                        Seller Dashboard
                    </h2>
                    <div class="text-secondary" style="font-size: var(--fs-sm);">
                        Welcome back, <strong>{{ $user->name }}</strong> • Verified Freelancer at {{ $user->college ?: 'SkillHive' }}
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('seller.services.create') }}" class="btn-sh btn-sh-primary btn-sh-sm">
                    <i class="bi bi-plus-circle me-1"></i> Create New Service
                </a>
                <a href="{{ route('seller.orders') }}" class="btn-sh btn-sh-secondary btn-sh-sm">
                    <i class="bi bi-bag-check me-1"></i> Incoming Client Orders
                </a>
                <a href="{{ route('seller.portfolio') }}" class="btn-sh btn-sh-outline btn-sh-sm">
                    <i class="bi bi-images me-1"></i> Manage Portfolio
                </a>
            </div>
        </div>
    </div>

    {{-- How Freelancer Earning Works Guide Banner --}}
    <div class="card-sh p-3 mb-4" style="background: rgba(16, 185, 129, 0.06); border: 1px solid rgba(16, 185, 129, 0.2);">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
            <span class="d-flex align-items-center gap-2 fw-semibold text-success" style="font-size: var(--fs-sm);">
                <i class="bi bi-info-circle-fill"></i> How Freelancing Works on SkillHive:
            </span>
            <span class="badge-sh badge-sh-success" style="font-size: 11px;">You Earn & Get Paid — Never Pay</span>
        </div>
        <div class="row g-2 text-secondary" style="font-size: var(--fs-xs);">
            <div class="col-md-3 d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-circle px-2 py-1">1</span>
                <span><strong>Publish Service:</strong> You set packages & pricing.</span>
            </div>
            <div class="col-md-3 d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-circle px-2 py-1">2</span>
                <span><strong>Client Pays:</strong> Funds held securely in Escrow.</span>
            </div>
            <div class="col-md-3 d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-circle px-2 py-1">3</span>
                <span><strong>Accept & Deliver:</strong> Review project brief & submit work.</span>
            </div>
            <div class="col-md-3 d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-circle px-2 py-1">4</span>
                <span><strong>Get Paid:</strong> Earnings released directly to you!</span>
            </div>
        </div>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: var(--color-success-light); color: var(--color-success); font-size: 1.5rem;">
                    <i class="bi bi-currency-rupee"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">₹{{ number_format($totalEarnings) }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Total Earnings</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: var(--color-primary-light); color: var(--color-primary); font-size: 1.5rem;">
                    <i class="bi bi-bag-check"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">{{ $activeOrdersCount }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Active Orders</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: #e0f2fe; color: #0284c7; font-size: 1.5rem;">
                    <i class="bi bi-trophy"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">{{ $completedOrdersCount }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Completed Orders</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; background: var(--color-warning-light); color: #92400E; font-size: 1.5rem;">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">{{ number_format($avgRating, 1) }} ★</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Average Seller Rating</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 mb-5">
        {{-- Revenue Chart Column --}}
        <div class="col-lg-8">
            <div class="card-sh p-4 mb-5">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="service-section-title mb-0" style="border-bottom: none; padding-bottom: 0;">Earnings Overview (Monthly)</h5>
                    <span class="badge-sh badge-sh-success"><i class="bi bi-graph-up-arrow me-1"></i> +24% Growth</span>
                </div>
                <div style="position: relative; height: 260px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Recent Seller Orders --}}
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 style="font-size: var(--fs-lg); font-weight: var(--fw-semibold); margin: 0;">Active Seller Orders</h4>
                <a href="{{ route('seller.orders') }}" class="btn-sh btn-sh-outline btn-sh-sm">Manage All Orders</a>
            </div>

            <div class="d-flex flex-column gap-3 mb-5">
                @forelse($recentOrders as $order)
                    @php
                        $oService = $order->service;
                        $oTitle   = $oService ? $oService->title : 'Service';
                        $oImg     = $oService ? ($oService->image ?: 'https://placehold.co/60x42/e8e0f5/6D28D9?text=SH') : 'https://placehold.co/60x42/e8e0f5/6D28D9?text=SH';
                        $oClient  = $order->client ? $order->client->name : 'Client';
                    @endphp
                    <div class="card-sh p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $oImg }}" alt="{{ $oTitle }}"
                                 style="width: 60px; height: 42px; object-fit: cover; border-radius: var(--radius-sm);">
                            <div>
                                <strong class="d-block text-dark" style="font-size: var(--fs-sm);">
                                    {{ $order->order_number }} — {{ $oTitle }}
                                </strong>
                                <span class="text-secondary" style="font-size: var(--fs-xs);">
                                    Buyer: {{ $oClient }} • Status:
                                    <strong class="text-primary">{{ $order->status }}</strong>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <strong class="text-primary">₹{{ number_format($order->price) }}</strong>
                            <a href="{{ route('seller.orders.show', $order->order_number) }}" class="btn-sh btn-sh-secondary btn-sh-sm">Details</a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted p-3 text-center">No active seller orders yet.</div>
                @endforelse
            </div>
        </div>

        {{-- Quick Actions & My Services Sidebar --}}
        <div class="col-lg-4">
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="{{ route('seller.services.create') }}" class="btn-sh btn-sh-primary text-start">
                        <i class="bi bi-plus-lg me-2"></i> Create New Service
                    </a>
                    <a href="{{ route('seller.orders') }}" class="btn-sh btn-sh-secondary text-start">
                        <i class="bi bi-bag-check me-2"></i> Manage Seller Orders
                    </a>
                    <a href="{{ route('seller.portfolio') }}" class="btn-sh btn-sh-secondary text-start">
                        <i class="bi bi-images me-2"></i> Edit Portfolio Manager
                    </a>
                    <a href="{{ route('seller.services') }}" class="btn-sh btn-sh-secondary text-start">
                        <i class="bi bi-journal-text me-2"></i> Manage My Services
                    </a>
                    <a href="{{ route('seller.payouts') }}" class="btn-sh btn-sh-outline text-start">
                        <i class="bi bi-wallet2 me-2"></i> Earnings &amp; Withdrawals
                    </a>
                </div>
            </div>

            <div class="card-sh p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="mb-0" style="font-size: var(--fs-md);">My Active Services</h5>
                    <a href="{{ route('seller.services') }}" style="font-size: var(--fs-xs);">View All</a>
                </div>
                
                <div class="d-flex flex-column gap-3">
                    @foreach($myServices as $s)
                        <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                            <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                <img src="{{ $s->image ?: 'https://placehold.co/40x30/e8e0f5/6D28D9?text=SH' }}" alt="{{ $s->title }}" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px;">
                                <span class="text-truncate text-dark fw-medium" style="font-size: var(--fs-xs);">{{ $s->title }}</span>
                            </div>
                            <strong class="text-primary text-nowrap" style="font-size: var(--fs-xs);">₹{{ number_format($s->price) }}</strong>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;

    const chartData = @json($chartData);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Earnings (₹)',
                data: chartData.earnings,
                borderColor: '#6D28D9',
                backgroundColor: 'rgba(109, 40, 217, 0.1)',
                fill: true,
                tension: 0.3,
                borderWidth: 3,
                pointBackgroundColor: '#6D28D9',
                pointRadius: 4,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) { return '₹' + value; }
                    }
                }
            }
        }
    });
});
</script>
@endpush
