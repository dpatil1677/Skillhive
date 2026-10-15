@extends('layouts.admin')

@section('title', 'User Management')

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">User Management</h1>
        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Manage registered students, clients, and freelancers on SkillHive.</p>
    </div>
    <button type="button" class="btn-sh btn-sh-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus-fill me-1"></i> Add New User
    </button>
</div>

{{-- Filters & Search Bar --}}
<div class="card-sh p-3 mb-4">
    <form action="{{ route('admin.users') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="q" value="{{ $query }}" class="form-control-sh border-start-0" placeholder="Search users by name or email...">
            </div>
        </div>
        <div class="col-md-2">
            <select name="role" class="form-select-sh w-100" onchange="this.form.submit()">
                <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>All Roles</option>
                <option value="user" {{ $roleFilter === 'user' ? 'selected' : '' }}>Clients</option>
                <option value="seller" {{ $roleFilter === 'seller' ? 'selected' : '' }}>Freelancers</option>
                <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admins</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select-sh w-100" onchange="this.form.submit()">
                <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn-sh btn-sh-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.users') }}" class="btn-sh btn-sh-outline w-100">Reset</a>
        </div>
    </form>
</div>

{{-- Users Table --}}
<div class="card-sh p-0 overflow-hidden mb-5">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-sm);">
            <thead class="bg-alt border-bottom">
                <tr>
                    <th class="ps-4 py-3">User</th>
                    <th class="py-3">Email</th>
                    <th class="py-3">College</th>
                    <th class="py-3">Role</th>
                    <th class="py-3">Services</th>
                    <th class="py-3">Orders</th>
                    <th class="py-3">Status</th>
                    <th class="pe-4 py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $u['avatar'] }}" alt="{{ $u['name'] }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                                <div>
                                    <strong class="d-block text-dark fw-semibold">{{ $u['name'] }}</strong>
                                    <span class="text-muted" style="font-size: 11px;">Joined {{ $u['joined'] }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-secondary">{{ $u['email'] }}</td>
                        <td class="py-3">{{ $u['college'] }}</td>
                        <td class="py-3">
                            @php
                                $roleBadge = match($u['role']) {
                                    'admin'  => 'badge-sh-danger',
                                    'seller' => 'badge-sh-success',
                                    default  => 'badge-sh-primary',
                                };
                            @endphp
                            <span class="badge-sh {{ $roleBadge }}" style="font-size: 11px;">{{ $u['role_label'] }}</span>
                        </td>
                        <td class="py-3 fw-semibold text-center">{{ $u['services_count'] }}</td>
                        <td class="py-3 fw-semibold text-center">{{ $u['orders_count'] }}</td>
                        <td class="py-3">
                            @if($u['status'] === 'Active')
                                <span class="badge-sh badge-sh-success" style="font-size: 11px;">Active</span>
                            @else
                                <span class="badge-sh badge-sh-danger" style="font-size: 11px;">Suspended</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-flex gap-1 justify-content-end flex-wrap">
                                {{-- View Profile --}}
                                <a href="{{ route('freelancer.show', \Illuminate\Support\Str::slug($u['name'])) }}" class="btn-sh btn-sh-outline btn-sh-sm py-1 px-2" title="View Profile" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-eye"></i>
                                </a>
                                {{-- Edit User --}}
                                <button type="button" class="btn-sh btn-sh-secondary btn-sh-sm py-1 px-2" title="Edit User"
                                    onclick="openEditUser({{ json_encode($u) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Access Dashboard (Impersonate) --}}
                                @if($u['role'] !== 'admin')
                                <a href="{{ route('admin.users.impersonate', $u['id']) }}"
                                   class="btn-sh btn-sh-sm py-1 px-2"
                                   style="background:#7c3aed;color:#fff;border-radius:6px;"
                                   title="Access {{ $u['role'] === 'seller' ? 'Freelancer' : 'Client' }} Dashboard">
                                    <i class="bi bi-person-badge"></i>
                                </a>
                                @endif
                                {{-- Suspend / Reactivate --}}
                                @if($u['status'] === 'Active')
                                    <button type="button" class="btn-sh btn-sh-danger btn-sh-sm py-1 px-2"
                                        onclick="confirmUserAction({{ $u['id'] }}, '{{ addslashes($u['name']) }}', 'Suspended')"
                                        title="Suspend User">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn-sh btn-sh-success btn-sh-sm py-1 px-2"
                                        onclick="confirmUserAction({{ $u['id'] }}, '{{ addslashes($u['name']) }}', 'Active')"
                                        title="Reactivate User">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                @endif
                                {{-- Delete User --}}
                                <button type="button" class="btn-sh btn-sh-danger btn-sh-sm py-1 px-2"
                                    onclick="confirmDeleteUser({{ $u['id'] }}, '{{ addslashes($u['name']) }}')"
                                    title="Delete User">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== ADD USER MODAL ===== --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus-fill text-primary me-2"></i> Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="addName">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100" id="addName" name="name" placeholder="e.g. Riya Sharma" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="addEmail">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control-sh w-100" id="addEmail" name="email" placeholder="riya@college.edu.in" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="addPassword">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control-sh w-100" id="addPassword" name="password" placeholder="Min 6 characters" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="addRole">Role <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="addRole" name="role" required>
                                <option value="user">Client (Buyer)</option>
                                <option value="seller">Freelancer (Seller)</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="addCollege">College / Institution</label>
                            <input type="text" class="form-control-sh w-100" id="addCollege" name="college" placeholder="e.g. MIT Pune">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="addCourse">Course / Department</label>
                            <input type="text" class="form-control-sh w-100" id="addCourse" name="course" placeholder="e.g. B.Tech Computer Science">
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="addBio">Short Bio</label>
                            <textarea class="form-control-sh w-100" id="addBio" name="bio" rows="2" placeholder="Tell us a bit about this user..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== EDIT USER MODAL ===== --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editName">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100" id="editName" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editEmail">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control-sh w-100" id="editEmail" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editRole">Role <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="editRole" name="role" required>
                                <option value="user">Client (Buyer)</option>
                                <option value="seller">Freelancer (Seller)</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editPasswordField">New Password <small class="text-muted">(leave blank to keep)</small></label>
                            <input type="password" class="form-control-sh w-100" id="editPasswordField" name="password" placeholder="Enter new password or leave blank">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editCollege">College / Institution</label>
                            <input type="text" class="form-control-sh w-100" id="editCollege" name="college">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="editCourse">Course / Department</label>
                            <input type="text" class="form-control-sh w-100" id="editCourse" name="course">
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="editBio">Bio</label>
                            <textarea class="form-control-sh w-100" id="editBio" name="bio" rows="2"></textarea>
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

{{-- ===== STATUS CHANGE MODAL ===== --}}
<div class="modal fade" id="userStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="userStatusModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change the status of <strong id="modalUserName">User</strong> to <strong id="modalTargetStatus">Suspended</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="userStatusForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="status" id="modalStatusInput" value="">
                    <button type="submit" class="btn-sh btn-sh-primary" id="modalConfirmBtn">Confirm Change</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ===== DELETE USER MODAL ===== --}}
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i> Delete User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong>permanently delete</strong> user <strong id="deleteUserName">this user</strong>?</p>
                <p class="text-danger mb-0" style="font-size: var(--fs-sm);"><i class="bi bi-exclamation-triangle-fill me-1"></i> This will also delete all their services. This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteUserForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn-sh btn-sh-danger">Delete Permanently</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmUserAction(userId, userName, newStatus) {
    document.getElementById('modalUserName').textContent = userName;
    document.getElementById('modalTargetStatus').textContent = newStatus;
    document.getElementById('modalStatusInput').value = newStatus;
    document.getElementById('userStatusForm').action = '/admin/users/' + userId + '/status';
    new bootstrap.Modal(document.getElementById('userStatusModal')).show();
}

function openEditUser(u) {
    document.getElementById('editName').value    = u.name;
    document.getElementById('editEmail').value   = u.email;
    document.getElementById('editRole').value    = u.role;
    document.getElementById('editCollege').value = u.college || '';
    document.getElementById('editCourse').value  = u.course || '';
    document.getElementById('editBio').value     = u.bio || '';
    document.getElementById('editPasswordField').value = '';
    document.getElementById('editUserForm').action = '/admin/users/' + u.id + '/update';
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function confirmDeleteUser(userId, userName) {
    document.getElementById('deleteUserName').textContent = '"' + userName + '"';
    document.getElementById('deleteUserForm').action = '/admin/users/' + userId + '/delete';
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>
@endpush
