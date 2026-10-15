@extends('layouts.admin')

@section('title', 'Orders Management')

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Orders Management</h1>
        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">View, create, update status and manage all platform orders and escrow transactions.</p>
    </div>
    <button type="button" class="btn-sh btn-sh-primary" data-bs-toggle="modal" data-bs-target="#createOrderModal">
        <i class="bi bi-plus-lg me-1"></i> Create Order
    </button>
</div>

{{-- Filters --}}
<div class="card-sh p-3 mb-4">
    <form action="{{ route('admin.orders') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" value="{{ $query }}" class="form-control-sh border-start-0" placeholder="Search orders by ID, service title, or client...">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select-sh w-100" onchange="this.form.submit()">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="accepted" {{ $statusFilter === 'accepted' ? 'selected' : '' }}>Accepted</option>
                <option value="in progress" {{ $statusFilter === 'in progress' ? 'selected' : '' }}>In Progress</option>
                <option value="submitted" {{ $statusFilter === 'submitted' ? 'selected' : '' }}>Submitted</option>
                <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-sh btn-sh-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.orders') }}" class="btn-sh btn-sh-outline w-100">Reset</a>
        </div>
    </form>
</div>

{{-- Orders Table --}}
<div class="card-sh p-0 overflow-hidden mb-5">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-sm);">
            <thead class="bg-alt border-bottom">
                <tr>
                    <th class="ps-4 py-3">Order ID</th>
                    <th class="py-3">Service</th>
                    <th class="py-3">Client</th>
                    <th class="py-3">Freelancer</th>
                    <th class="py-3">Amount</th>
                    <th class="py-3">Status</th>
                    <th class="py-3">Date</th>
                    <th class="pe-4 py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $o)
                    <tr>
                        <td class="ps-4 py-3">
                            <strong class="text-dark">{{ $o['order_number'] }}</strong>
                        </td>
                        <td class="py-3">
                            <span class="fw-medium text-dark text-truncate d-block" style="max-width: 200px;">
                                {{ $o['service_title'] }}
                            </span>
                        </td>
                        <td class="py-3">{{ $o['client_name'] }}</td>
                        <td class="py-3">{{ $o['freelancer_name'] }}</td>
                        <td class="py-3"><strong class="text-primary">₹{{ number_format($o['total_amount']) }}</strong></td>
                        <td class="py-3">
                            @php
                                $sl = strtolower($o['status']);
                                $badgeClass = match(true) {
                                    $sl === 'completed' => 'badge-sh-success',
                                    $sl === 'cancelled' => 'badge-sh-danger',
                                    in_array($sl, ['in progress','accepted','submitted']) => 'badge-sh-primary',
                                    default => 'badge-sh-warning',
                                };
                            @endphp
                            <span class="badge-sh {{ $badgeClass }}" style="font-size: 11px;">{{ $o['status'] }}</span>
                        </td>
                        <td class="py-3 text-secondary" style="font-size: var(--fs-xs);">{{ $o['created_at'] }}</td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('orders.show', $o['order_number']) }}" class="btn-sh btn-sh-outline btn-sh-sm py-1 px-2" target="_blank" rel="noopener noreferrer" title="View Order">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn-sh btn-sh-secondary btn-sh-sm py-1 px-2" title="Update Status"
                                    onclick="openUpdateStatus('{{ $o['order_number'] }}', '{{ addslashes($o['raw_status']) }}')">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                                <button type="button" class="btn-sh btn-sh-danger btn-sh-sm py-1 px-2" title="Delete Order"
                                    onclick="confirmDeleteOrder('{{ $o['order_number'] }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No orders found matching filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== CREATE ORDER MODAL ===== --}}
<div class="modal fade" id="createOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-bag-plus text-primary me-2"></i> Create New Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.orders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="ordService">Service <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="ordService" name="service_id" required>
                                <option value="">— Select Service —</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}">{{ $svc->title }} (₹{{ number_format($svc->price) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="ordClient">Client (Buyer) <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="ordClient" name="client_id" required>
                                <option value="">— Select Client —</option>
                                @foreach($clients as $cl)
                                    <option value="{{ $cl->id }}">{{ $cl->name }} ({{ $cl->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="ordPackage">Package</label>
                            <select class="form-select-sh w-100" id="ordPackage" name="package">
                                <option value="standard">Standard</option>
                                <option value="basic">Basic</option>
                                <option value="premium">Premium</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="ordRequirements">Project Requirements <span class="text-danger">*</span></label>
                            <textarea class="form-control-sh w-100" id="ordRequirements" name="requirements" rows="3" placeholder="Describe what the client needs..." required minlength="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Place Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== UPDATE STATUS MODAL ===== --}}
<div class="modal fade" id="updateOrderStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrow-repeat text-primary me-2"></i> Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateOrderStatusForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">Order: <strong id="updateOrderNumber" class="text-dark"></strong></p>
                    <label class="form-label-sh" for="newOrderStatus">New Status <span class="text-danger">*</span></label>
                    <select class="form-select-sh w-100" id="newOrderStatus" name="status" required>
                        <option value="pending">Pending</option>
                        <option value="accepted">Accepted</option>
                        <option value="in_progress">In Progress</option>
                        <option value="submitted">Submitted</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== DELETE ORDER MODAL ===== --}}
<div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i> Delete Order</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete order <strong id="deleteOrderNum"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: var(--fs-sm);"><i class="bi bi-exclamation-triangle-fill me-1"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteOrderForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn-sh btn-sh-danger">Delete Order</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openUpdateStatus(orderNumber, currentStatus) {
    document.getElementById('updateOrderNumber').textContent = orderNumber;
    document.getElementById('updateOrderStatusForm').action = '/admin/orders/' + orderNumber + '/status';
    const sel = document.getElementById('newOrderStatus');
    const statusMap = {'in progress': 'in_progress', 'in_progress': 'in_progress'};
    const mapped = statusMap[currentStatus.toLowerCase()] || currentStatus.toLowerCase();
    for (let opt of sel.options) {
        opt.selected = opt.value === mapped;
    }
    new bootstrap.Modal(document.getElementById('updateOrderStatusModal')).show();
}

function confirmDeleteOrder(orderNumber) {
    document.getElementById('deleteOrderNum').textContent = '#' + orderNumber;
    document.getElementById('deleteOrderForm').action = '/admin/orders/' + orderNumber + '/delete';
    new bootstrap.Modal(document.getElementById('deleteOrderModal')).show();
}
</script>
@endpush
