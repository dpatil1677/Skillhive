@extends('layouts.admin')

@section('title', 'Services Management')

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Services Management</h1>
        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Moderate and manage all service listings offered on SkillHive marketplace.</p>
    </div>
    <button type="button" class="btn-sh btn-sh-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Service
    </button>
</div>

{{-- Search Bar --}}
<div class="card-sh p-3 mb-4">
    <form action="{{ route('admin.services') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-8">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" value="{{ $query }}" class="form-control-sh border-start-0" placeholder="Search services by title or category...">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-sh btn-sh-primary w-100">Search</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.services') }}" class="btn-sh btn-sh-outline w-100">Reset</a>
        </div>
    </form>
</div>

{{-- Services Table --}}
<div class="card-sh p-0 overflow-hidden mb-5">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-sm);">
            <thead class="bg-alt border-bottom">
                <tr>
                    <th class="ps-4 py-3">Service Details</th>
                    <th class="py-3">Freelancer</th>
                    <th class="py-3">Category</th>
                    <th class="py-3">Starting Price</th>
                    <th class="py-3">Rating</th>
                    <th class="pe-4 py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $s)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $s['image'] }}" alt="{{ $s['title'] }}" style="width: 64px; height: 44px; object-fit: cover; border-radius: var(--radius-sm); flex-shrink: 0;">
                                <div>
                                    <strong class="d-block text-dark fw-semibold">
                                        <a href="{{ route('service.show', $s['slug']) }}" class="text-dark text-decoration-none" target="_blank" rel="noopener noreferrer">{{ $s['title'] }}</a>
                                    </strong>
                                    <span class="text-muted" style="font-size: 11px;">Delivery: {{ $s['delivery_days'] }} days</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $s['freelancer']['avatar'] }}" alt="{{ $s['freelancer']['name'] }}" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                                <span>{{ $s['freelancer']['name'] }}</span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="badge-sh badge-sh-primary" style="font-size: 11px;">{{ $s['category'] }}</span>
                        </td>
                        <td class="py-3">
                            <strong class="text-primary">₹{{ number_format($s['price']) }}</strong>
                        </td>
                        <td class="py-3">
                            <i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($s['rating'], 1) }}
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <a href="{{ route('service.show', $s['slug']) }}" class="btn-sh btn-sh-outline btn-sh-sm py-1 px-2" title="View Public Page" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn-sh btn-sh-secondary btn-sh-sm py-1 px-2" title="Edit Service"
                                    onclick="openEditService({{ json_encode($s) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn-sh btn-sh-danger btn-sh-sm py-1 px-2"
                                    onclick="confirmDeleteAdminService({{ $s['id'] }}, '{{ addslashes($s['title']) }}')"
                                    title="Delete Service">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">No services found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== ADD SERVICE MODAL ===== --}}
<div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i> Add New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.services.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-sh" for="svcTitle">Service Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100" id="svcTitle" name="title" placeholder="e.g. Professional Logo Design" required minlength="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="svcFreelancer">Freelancer (Owner) <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="svcFreelancer" name="user_id" required>
                                <option value="">— Select Freelancer —</option>
                                @foreach($freelancers as $fl)
                                    <option value="{{ $fl->id }}">{{ $fl->name }} ({{ $fl->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="svcCategory">Category <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="svcCategory" name="category_id" required>
                                <option value="">— Select Category —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="svcPrice">Base Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control-sh w-100" id="svcPrice" name="price" placeholder="500" min="50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="svcDelivery">Delivery Days <span class="text-danger">*</span></label>
                            <input type="number" class="form-control-sh w-100" id="svcDelivery" name="delivery_days" placeholder="3" min="1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="svcDesc">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control-sh w-100" id="svcDesc" name="description" rows="3" placeholder="Describe the service in detail..." required minlength="15"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="svcImage">Service Image URL <small class="text-muted">(optional)</small></label>
                            <input type="text" class="form-control-sh w-100" id="svcImage" name="image" placeholder="https://...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Create Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== EDIT SERVICE MODAL ===== --}}
<div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editServiceForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label-sh" for="editSvcTitle">Service Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100" id="editSvcTitle" name="title" required minlength="5">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editSvcCategory">Category <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="editSvcCategory" name="category_id" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-sh" for="editSvcPrice">Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control-sh w-100" id="editSvcPrice" name="price" min="50" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-sh" for="editSvcDelivery">Delivery Days <span class="text-danger">*</span></label>
                            <input type="number" class="form-control-sh w-100" id="editSvcDelivery" name="delivery_days" min="1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="editSvcDesc">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control-sh w-100" id="editSvcDesc" name="description" rows="3" required minlength="15"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="editSvcImage">Image URL <small class="text-muted">(optional)</small></label>
                            <input type="text" class="form-control-sh w-100" id="editSvcImage" name="image">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== DELETE SERVICE MODAL ===== --}}
<div class="modal fade" id="deleteAdminServiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="deleteAdminServiceName">this service</strong>?</p>
                <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">This action will immediately remove the service from marketplace search results.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteAdminServiceForm" method="POST" action="">
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
function openEditService(s) {
    document.getElementById('editSvcTitle').value    = s.title;
    document.getElementById('editSvcCategory').value = s.category_id;
    document.getElementById('editSvcPrice').value    = s.price;
    document.getElementById('editSvcDelivery').value = s.delivery_days;
    document.getElementById('editSvcDesc').value     = s.description || '';
    document.getElementById('editSvcImage').value    = s.image || '';
    document.getElementById('editServiceForm').action = '/admin/services/' + s.id + '/update';
    new bootstrap.Modal(document.getElementById('editServiceModal')).show();
}

function confirmDeleteAdminService(id, title) {
    document.getElementById('deleteAdminServiceName').textContent = '"' + title + '"';
    document.getElementById('deleteAdminServiceForm').action = '/admin/services/' + id + '/delete';
    new bootstrap.Modal(document.getElementById('deleteAdminServiceModal')).show();
}
</script>
@endpush
