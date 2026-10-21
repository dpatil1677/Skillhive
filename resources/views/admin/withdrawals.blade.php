@extends('layouts.admin')

@section('title', 'Payout Requests — Admin')

@section('content')

<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div>
        <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin-bottom: 4px;">Payouts &amp; Withdrawals</h1>
        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">Review student freelancer withdrawal requests, approve UPI and bank payouts.</p>
    </div>
</div>

{{-- Filters --}}
<div class="card-sh p-3 mb-4">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="{{ route('admin.withdrawals') }}" class="btn-sh {{ empty($currentStatus) ? 'btn-sh-primary' : 'btn-sh-outline' }} btn-sh-xs">All</a>
        <a href="{{ route('admin.withdrawals', ['status' => 'pending']) }}" class="btn-sh {{ $currentStatus === 'pending' ? 'btn-sh-primary' : 'btn-sh-outline' }} btn-sh-xs">Pending</a>
        <a href="{{ route('admin.withdrawals', ['status' => 'processing']) }}" class="btn-sh {{ $currentStatus === 'processing' ? 'btn-sh-primary' : 'btn-sh-outline' }} btn-sh-xs">Processing</a>
        <a href="{{ route('admin.withdrawals', ['status' => 'completed']) }}" class="btn-sh {{ $currentStatus === 'completed' ? 'btn-sh-primary' : 'btn-sh-outline' }} btn-sh-xs">Completed</a>
        <a href="{{ route('admin.withdrawals', ['status' => 'rejected']) }}" class="btn-sh {{ $currentStatus === 'rejected' ? 'btn-sh-primary' : 'btn-sh-outline' }} btn-sh-xs">Rejected</a>
    </div>
</div>

{{-- Table --}}
<div class="card-sh p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr class="text-secondary" style="font-size: var(--fs-xs); text-transform: uppercase;">
                    <th>Reference</th>
                    <th>Freelancer</th>
                    <th>Amount</th>
                    <th>Method &amp; Details</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $w)
                    <tr>
                        <td>
                            <strong class="text-dark">{{ $w->reference_id }}</strong>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ $w->user ? $w->user->avatar : '' }}" style="width: 32px; height: 32px; border-radius: 50%;">
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size: var(--fs-sm);">{{ $w->user ? $w->user->name : 'Freelancer' }}</div>
                                    <span class="text-muted" style="font-size: 11px;">{{ $w->user ? $w->user->email : '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong class="text-primary" style="font-size: var(--fs-md);">₹{{ number_format($w->amount, 2) }}</strong>
                        </td>
                        <td style="font-size: var(--fs-sm);">
                            @if($w->method === 'upi')
                                <div><span class="badge bg-light text-dark border">UPI</span> <strong>{{ $w->payout_details['upi_id'] ?? '' }}</strong></div>
                            @else
                                <div><span class="badge bg-light text-dark border">Bank</span> <strong>{{ $w->payout_details['bank_name'] ?? '' }}</strong></div>
                                <span class="text-muted" style="font-size: 11px;">A/C: {{ $w->payout_details['account_number'] ?? '' }} • IFSC: {{ $w->payout_details['ifsc'] ?? '' }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-sh {{ $w->status_badge }}">{{ ucfirst($w->status) }}</span>
                        </td>
                        <td class="text-secondary" style="font-size: var(--fs-xs);">
                            {{ $w->created_at ? $w->created_at->format('M d, Y') : '—' }}
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn-sh btn-sh-outline btn-sh-xs" data-bs-toggle="modal" data-bs-target="#editWithdrawalModal{{ $w->id }}">
                                Update Status
                            </button>
                        </td>
                    </tr>

                    {{-- Modal for updating status --}}
                    <div class="modal fade" id="editWithdrawalModal{{ $w->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold">Update Payout #{{ $w->reference_id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.withdrawals.status', $w->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label-sh">Status</label>
                                            <select name="status" class="form-select-sh w-100">
                                                <option value="pending" {{ $w->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="processing" {{ $w->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                <option value="completed" {{ $w->status === 'completed' ? 'selected' : '' }}>Completed (Funds Dispatched)</option>
                                                <option value="rejected" {{ $w->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label-sh">Admin Notes / Transaction UTR</label>
                                            <input type="text" name="admin_notes" class="form-control-sh w-100" value="{{ $w->admin_notes }}" placeholder="e.g. UTR / IMPS ref: 928374928374">
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn-sh btn-sh-primary">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No withdrawal requests found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
