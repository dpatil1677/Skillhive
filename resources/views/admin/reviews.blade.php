@extends('layouts.admin')

@section('title', 'Reviews Moderation')

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Reviews Moderation</h1>
        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Moderate client ratings and written feedback across SkillHive marketplace.</p>
    </div>
    <button type="button" class="btn-sh btn-sh-primary" data-bs-toggle="modal" data-bs-target="#addReviewModal">
        <i class="bi bi-plus-lg me-1"></i> Add Review
    </button>
</div>

{{-- Reviews Table --}}
<div class="card-sh p-0 overflow-hidden mb-5">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: var(--fs-sm);">
            <thead class="bg-alt border-bottom">
                <tr>
                    <th class="ps-4 py-3">Reviewer</th>
                    <th class="py-3">Freelancer</th>
                    <th class="py-3">Service</th>
                    <th class="py-3">Rating</th>
                    <th class="py-3">Comment</th>
                    <th class="py-3">Date</th>
                    <th class="py-3">Status</th>
                    <th class="pe-4 py-3 text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reviews as $rev)
                    <tr>
                        <td class="ps-4 py-3">
                            <strong class="text-dark">{{ $rev['reviewer'] }}</strong>
                        </td>
                        <td class="py-3 fw-medium text-dark">{{ $rev['freelancer'] }}</td>
                        <td class="py-3 text-truncate" style="max-width: 180px;">{{ $rev['service'] }}</td>
                        <td class="py-3">
                            <div class="d-flex align-items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $rev['rating'] ? '-fill text-warning' : ' text-muted' }}" style="font-size: 12px;"></i>
                                @endfor
                                <strong class="ms-1">{{ $rev['rating'] }}</strong>
                            </div>
                        </td>
                        <td class="py-3 text-secondary text-truncate" style="max-width: 220px;" title="{{ $rev['comment'] }}">
                            {{ $rev['comment'] }}
                        </td>
                        <td class="py-3 text-muted" style="font-size: 11px;">{{ $rev['date'] }}</td>
                        <td class="py-3">
                            <span class="badge-sh badge-sh-success" style="font-size: 11px;">{{ $rev['status'] }}</span>
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                <button type="button" class="btn-sh btn-sh-secondary btn-sh-sm py-1 px-2" title="Edit Review"
                                    onclick="openEditReview({{ json_encode($rev) }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn-sh btn-sh-danger btn-sh-sm py-1 px-2" title="Delete Review"
                                    onclick="confirmDeleteReview({{ $rev['id'] }}, '{{ addslashes($rev['reviewer']) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No reviews submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== ADD REVIEW MODAL ===== --}}
<div class="modal fade" id="addReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-star-fill text-warning me-2"></i> Add Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.reviews.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label-sh" for="revUserId">Reviewer (Client) <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="revUserId" name="user_id" required>
                                <option value="">— Select Reviewer —</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-sh" for="revFreelancerId">Freelancer (Reviewed) <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="revFreelancerId" name="freelancer_id" required>
                                <option value="">— Select Freelancer —</option>
                                @foreach($freelancers as $fl)
                                    <option value="{{ $fl->id }}">{{ $fl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-sh" for="revRating">Rating <span class="text-danger">*</span></label>
                            <select class="form-select-sh w-100" id="revRating" name="rating" required>
                                <option value="5">⭐⭐⭐⭐⭐ (5 — Excellent)</option>
                                <option value="4">⭐⭐⭐⭐ (4 — Very Good)</option>
                                <option value="3">⭐⭐⭐ (3 — Good)</option>
                                <option value="2">⭐⭐ (2 — Fair)</option>
                                <option value="1">⭐ (1 — Poor)</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-sh" for="revComment">Review Comment <span class="text-danger">*</span></label>
                            <textarea class="form-control-sh w-100" id="revComment" name="comment" rows="3" placeholder="Write the review feedback..." required minlength="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Add Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== EDIT REVIEW MODAL ===== --}}
<div class="modal fade" id="editReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i> Edit Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editReviewForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-sh" for="editRevRating">Rating <span class="text-danger">*</span></label>
                        <select class="form-select-sh w-100" id="editRevRating" name="rating" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5 — Excellent)</option>
                            <option value="4">⭐⭐⭐⭐ (4 — Very Good)</option>
                            <option value="3">⭐⭐⭐ (3 — Good)</option>
                            <option value="2">⭐⭐ (2 — Fair)</option>
                            <option value="1">⭐ (1 — Poor)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-sh" for="editRevComment">Comment <span class="text-danger">*</span></label>
                        <textarea class="form-control-sh w-100" id="editRevComment" name="comment" rows="4" required minlength="3"></textarea>
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

{{-- ===== DELETE REVIEW MODAL ===== --}}
<div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger"><i class="bi bi-trash-fill me-2"></i> Delete Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to permanently delete the review by <strong id="deleteRevName"></strong>?</p>
                <p class="text-danger mb-0" style="font-size: var(--fs-sm);"><i class="bi bi-exclamation-triangle-fill me-1"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteReviewForm" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn-sh btn-sh-danger">Delete Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditReview(rev) {
    document.getElementById('editRevRating').value  = rev.rating;
    document.getElementById('editRevComment').value = rev.comment;
    document.getElementById('editReviewForm').action = '/admin/reviews/' + rev.id + '/update';
    new bootstrap.Modal(document.getElementById('editReviewModal')).show();
}

function confirmDeleteReview(id, name) {
    document.getElementById('deleteRevName').textContent = name;
    document.getElementById('deleteReviewForm').action = '/admin/reviews/' + id + '/delete';
    new bootstrap.Modal(document.getElementById('deleteReviewModal')).show();
}
</script>
@endpush
