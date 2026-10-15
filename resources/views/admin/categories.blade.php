@extends('layouts.admin')

@section('title', 'Category Management')

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Category Management</h1>
        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Add, edit, or reorganize marketplace categories across SkillHive.</p>
    </div>
    <button type="button" class="btn-sh btn-sh-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-lg me-1"></i> Add New Category
    </button>
</div>

{{-- Categories Table --}}
<div class="card-sh p-0 overflow-hidden mb-5">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-sm);">
            <thead class="bg-alt border-bottom">
                <tr>
                    <th class="ps-4 py-3">Icon & Name</th>
                    <th class="py-3">URL Slug</th>
                    <th class="py-3">Services Count</th>
                    <th class="py-3">Subcategories</th>
                    <th class="pe-4 py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded p-2 bg-alt text-primary fs-5 border" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi {{ $cat['icon'] }}"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark fw-semibold">
                                        <a href="{{ route('category', $cat['slug']) }}" class="text-dark text-decoration-none" target="_blank" rel="noopener noreferrer">{{ $cat['name'] }}</a>
                                    </strong>
                                    <span class="text-muted" style="font-size: 11px;">{{ Str::limit($cat['description'] ?? '', 45) }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-secondary font-monospace" style="font-size: var(--fs-xs);">/category/{{ $cat['slug'] }}</td>
                        <td class="py-3 fw-semibold">{{ $cat['count'] ?? 0 }} services</td>
                        <td class="py-3">
                            @if(isset($cat['subcategories']) && is_array($cat['subcategories']))
                                <span class="text-secondary" style="font-size: 11px;">{{ implode(', ', array_slice($cat['subcategories'], 0, 3)) }}...</span>
                            @endif
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="btn-group">
                                <a href="{{ route('category', $cat['slug']) }}" class="btn-sh btn-sh-outline btn-sh-sm py-1 px-2" title="View Category Page" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="btn-sh btn-sh-secondary btn-sh-sm py-1 px-2" onclick="editCategoryItem({{ json_encode($cat) }})" title="Edit Category">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn-sh btn-sh-danger btn-sh-sm py-1 px-2" onclick="confirmDeleteCategory({{ $cat['id'] }}, '{{ addslashes($cat['name']) }}')" title="Delete Category">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Add Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i> Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-sh" for="catName">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="catName" name="name" placeholder="e.g. AI & Machine Learning" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="catIcon">Bootstrap Icon Class <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="catIcon" name="icon" value="bi-cpu-fill" placeholder="bi-cpu-fill" required>
                        <span class="text-muted" style="font-size: 11px;">e.g. bi-code-slash, bi-palette, bi-camera-video</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="catDesc">Description</label>
                        <textarea class="form-control-sh w-100" id="catDesc" name="description" rows="3" placeholder="Brief description of this service category..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="catSub">Subcategories (comma-separated)</label>
                        <input type="text" class="form-control-sh w-100" id="catSub" name="subcategories" placeholder="AI Prompts, Model Training, Fine Tuning">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-sh" for="editCatName">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="editCatName" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="editCatIcon">Icon Class <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="editCatIcon" name="icon" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="editCatDesc">Description</label>
                        <textarea class="form-control-sh w-100" id="editCatDesc" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="editCatSub">Subcategories (comma-separated)</label>
                        <input type="text" class="form-control-sh w-100" id="editCatSub" name="subcategories">
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

{{-- Delete Category Modal --}}
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i> Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete category <strong id="deleteCatName">this category</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCategoryForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn-sh btn-sh-danger">Delete Category</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editCategoryItem(cat) {
    document.getElementById('editCatName').value = cat.name;
    document.getElementById('editCatIcon').value = cat.icon;
    document.getElementById('editCatDesc').value = cat.description || '';
    document.getElementById('editCatSub').value = Array.isArray(cat.subcategories) ? cat.subcategories.join(', ') : '';
    document.getElementById('editCategoryForm').action = "/admin/categories/" + cat.id + "/update";

    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    modal.show();
}

function confirmDeleteCategory(id, name) {
    document.getElementById('deleteCatName').textContent = '"' + name + '"';
    document.getElementById('deleteCategoryForm').action = "/admin/categories/" + id + "/delete";
    const modal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));
    modal.show();
}
</script>
@endpush
