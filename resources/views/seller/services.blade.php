@extends('layouts.guest')

@section('title', 'My Services — SkillHive')

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Seller Dashboard', 'url' => route('seller.dashboard')],
        ['label' => 'My Services'],
    ]])

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">My Services</h1>
            <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Manage your active service listings and create new offers.</p>
        </div>
        <a href="{{ route('seller.services.create') }}" class="btn-sh btn-sh-primary">
            <i class="bi bi-plus-lg me-1"></i> Create New Service
        </a>
    </div>

    <div class="card-sh p-0 overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-sm);">
                <thead class="bg-alt" style="border-bottom: 1px solid var(--color-border);">
                    <tr>
                        <th class="ps-4 py-3">Service Details</th>
                        <th class="py-3">Category</th>
                        <th class="py-3">Starting Price</th>
                        <th class="py-3">Orders</th>
                        <th class="py-3">Rating</th>
                        <th class="pe-4 py-3 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $s)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $s->image ?: 'https://placehold.co/64x44/e8e0f5/6D28D9?text=SH' }}" alt="{{ $s->title }}" style="width: 64px; height: 44px; object-fit: cover; border-radius: var(--radius-sm); flex-shrink: 0;">
                                    <div>
                                        <strong class="d-block text-dark fw-semibold">
                                            <a href="{{ route('service.show', $s->slug) }}" class="text-dark text-decoration-none">{{ $s->title }}</a>
                                        </strong>
                                        <span class="text-muted" style="font-size: 11px;">Delivery: {{ $s->delivery_days }} days</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="badge-sh badge-sh-primary" style="font-size: 11px;">{{ $s->category_name }}</span>
                            </td>
                            <td class="py-3">
                                <strong class="text-primary">₹{{ number_format($s->price) }}</strong>
                            </td>
                            <td class="py-3">
                                {{ ($s->reviews_count ?? 0) * 2 + 3 }} orders
                            </td>
                            <td class="py-3">
                                <div class="d-flex align-items-center gap-1">
                                    <i class="bi bi-star-fill text-warning" style="font-size: 12px;"></i>
                                    <strong>{{ number_format($s->rating ?? 5.0, 1) }}</strong>
                                    <span class="text-muted" style="font-size: 11px;">({{ $s->reviews_count ?? 0 }})</span>
                                </div>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group">
                                    <a href="{{ route('service.show', $s->slug) }}" class="btn-sh btn-sh-outline btn-sh-sm py-1 px-2" title="View Public Page">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('seller.services.edit', $s->id) }}" class="btn-sh btn-sh-secondary btn-sh-sm py-1 px-2" title="Edit Service">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn-sh btn-sh-danger btn-sh-sm py-1 px-2" title="Delete Service" onclick="confirmDeleteService('{{ $s->id }}', '{{ addslashes($s->title) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                No active services. Create your first service to start offering skills on SkillHive.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteServiceName">this service</strong>?</p>
                <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">This action will remove the service listing from the marketplace.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteServiceForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn-sh btn-sh-danger">Delete Service</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmDeleteService(serviceId, serviceTitle) {
    document.getElementById('deleteServiceName').textContent = '"' + serviceTitle + '"';
    document.getElementById('deleteServiceForm').action = "/seller/services/" + serviceId + "/delete";
    const modal = new bootstrap.Modal(document.getElementById('deleteServiceModal'));
    modal.show();
}
</script>
@endpush
