@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Admin Dashboard</h1>
        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">SkillHive Platform Administration & Analytics Overview</p>
    </div>
    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 fw-semibold">
        <i class="bi bi-shield-check me-1"></i> System Status: Operational
    </span>
</div>

{{-- Top 6 Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card-sh p-3">
            <span class="text-secondary d-block mb-1" style="font-size: var(--fs-xs);">Total Users</span>
            <strong class="text-dark" style="font-size: var(--fs-xl);">{{ number_format($totalUsers) }}</strong>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card-sh p-3">
            <span class="text-secondary d-block mb-1" style="font-size: var(--fs-xs);">Total Services</span>
            <strong class="text-dark" style="font-size: var(--fs-xl);">{{ number_format($totalServices) }}</strong>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card-sh p-3">
            <span class="text-secondary d-block mb-1" style="font-size: var(--fs-xs);">Total Orders</span>
            <strong class="text-dark" style="font-size: var(--fs-xl);">{{ number_format($totalOrders) }}</strong>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card-sh p-3">
            <span class="text-secondary d-block mb-1" style="font-size: var(--fs-xs);">Completed Orders</span>
            <strong class="text-success" style="font-size: var(--fs-xl);">{{ number_format($completedOrders) }}</strong>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card-sh p-3">
            <span class="text-secondary d-block mb-1" style="font-size: var(--fs-xs);">Pending Orders</span>
            <strong class="text-warning" style="font-size: var(--fs-xl);">{{ number_format($pendingOrders) }}</strong>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-6">
        <div class="card-sh p-3">
            <span class="text-secondary d-block mb-1" style="font-size: var(--fs-xs);">Platform Revenue</span>
            <strong class="text-primary" style="font-size: var(--fs-xl);">₹{{ number_format($platformRevenue) }}</strong>
        </div>
    </div>
</div>

{{-- Analytics Charts Row --}}
<div class="row g-4 mb-4">
    <div class="col-lg-7">
        <div class="card-sh p-4 h-100">
            <h5 class="service-section-title mb-3" style="font-size: var(--fs-base);">Orders Over Time</h5>
            <div style="position: relative; height: 230px;">
                <canvas id="adminOrdersChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card-sh p-4 h-100">
            <h5 class="service-section-title mb-3" style="font-size: var(--fs-base);">Service Categories Breakdown</h5>
            <div style="position: relative; height: 230px;">
                <canvas id="adminCategoriesChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Tables Row: Recent Users & Recent Orders --}}
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card-sh p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0" style="font-size: var(--fs-base);">Recent Users</h5>
                <a href="{{ route('admin.users') }}" style="font-size: var(--fs-xs);">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-xs);">
                    <thead class="bg-alt">
                        <tr>
                            <th>User</th>
                            <th>College</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $u)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $u['avatar'] }}" alt="{{ $u['name'] }}" style="width: 28px; height: 28px; border-radius: 50%;">
                                        <strong class="text-dark">{{ $u['name'] }}</strong>
                                    </div>
                                </td>
                                <td>{{ $u['college'] }}</td>
                                <td><span class="badge bg-secondary" style="font-size: 10px;">{{ $u['role'] }}</span></td>
                                <td><span class="badge bg-success" style="font-size: 10px;">{{ $u['status'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card-sh p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0" style="font-size: var(--fs-base);">Recent Orders</h5>
                <a href="{{ route('admin.orders') }}" style="font-size: var(--fs-xs);">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-xs);">
                    <thead class="bg-alt">
                        <tr>
                            <th>Order ID</th>
                            <th>Service</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $o)
                            <tr>
                                <td><strong class="text-dark">{{ $o['order_number'] }}</strong></td>
                                <td class="text-truncate" style="max-width: 180px;">{{ $o['service']['title'] }}</td>
                                <td><strong class="text-primary">₹{{ number_format($o['total_amount']) }}</strong></td>
                                <td><span class="badge bg-primary" style="font-size: 10px;">{{ $o['status'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Orders over time line chart
    const ctxOrders = document.getElementById('adminOrdersChart');
    if (ctxOrders) {
        new Chart(ctxOrders, {
            type: 'line',
            data: {
                labels: @json($chartOrders['labels']),
                datasets: [{
                    label: 'Orders',
                    data: @json($chartOrders['data']),
                    borderColor: '#6D28D9',
                    backgroundColor: 'rgba(109, 40, 217, 0.1)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // Categories donut chart
    const ctxCats = document.getElementById('adminCategoriesChart');
    if (ctxCats) {
        new Chart(ctxCats, {
            type: 'doughnut',
            data: {
                labels: @json($chartCategories['labels']),
                datasets: [{
                    data: @json($chartCategories['data']),
                    backgroundColor: ['#6D28D9', '#0284c7', '#059669', '#dc2626', '#d97706', '#7c3aed'],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });
    }
});
</script>
@endpush
