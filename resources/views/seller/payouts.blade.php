@extends('layouts.guest')

@section('title', 'Earnings & Payouts — SkillHive Freelancer')

@section('content')

<div class="container section-sh-sm">
    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home', 'url' => route('home')],
        ['label' => 'Freelancer Dashboard', 'url' => route('seller.dashboard')],
        ['label' => 'Earnings & Payouts'],
    ]])

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h1 class="mb-1" style="font-size: var(--fs-2xl); font-weight: var(--fw-bold);">
                Earnings &amp; Payouts
            </h1>
            <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">
                Track your completed project earnings and withdraw funds directly to your UPI or bank account.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-sh btn-sh-primary btn-sh-lg" data-bs-toggle="modal" data-bs-target="#requestPayoutModal" {{ $availableBalance < 100 ? 'disabled' : '' }}>
                <i class="bi bi-wallet2 me-1"></i> Request Withdrawal
            </button>
        </div>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="row g-4 mb-5">
        <div class="col-lg-4 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: var(--color-success-light); color: var(--color-success); font-size: 1.6rem;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">₹{{ number_format($totalEarnings, 2) }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Lifetime Net Earnings</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3" style="border: 2px solid var(--color-primary-light); background: linear-gradient(135deg, var(--color-primary-subtle) 0%, #fff 100%);">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: var(--color-primary-light); color: var(--color-primary); font-size: 1.6rem;">
                    <i class="bi bi-wallet-fill"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-primary);">₹{{ number_format($availableBalance, 2) }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Available to Withdraw</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card-sh p-4 d-flex align-items-center gap-3">
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: #e0f2fe; color: #0284c7; font-size: 1.6rem;">
                    <i class="bi bi-check2-all"></i>
                </div>
                <div>
                    <div style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); color: var(--color-text);">₹{{ number_format($withdrawnSum, 2) }}</div>
                    <div style="font-size: var(--fs-sm); color: var(--color-text-secondary);">Total Withdrawn</div>
                </div>
            </div>
        </div>
    </div>

    @if($availableBalance < 100)
        <div class="alert alert-info d-flex align-items-center gap-3 p-3 mb-4 rounded-3 border">
            <i class="bi bi-info-circle-fill fs-4 text-primary"></i>
            <div>
                <strong>Minimum withdrawal is ₹100.</strong> Once your clients approve submitted deliverables, your earnings are automatically credited to your withdrawable balance.
            </div>
        </div>
    @endif

    {{-- Withdrawal History Table --}}
    <div class="card-sh p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h4 style="font-size: var(--fs-lg); font-weight: var(--fw-semibold); margin: 0;">
                Withdrawal &amp; Payout History
            </h4>
            <span class="text-secondary" style="font-size: var(--fs-sm);">{{ $withdrawals->count() }} records</span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr class="text-secondary" style="font-size: var(--fs-xs); text-transform: uppercase;">
                        <th>Reference ID</th>
                        <th>Date Requested</th>
                        <th>Method</th>
                        <th>Payout Destination</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($withdrawals as $w)
                        <tr>
                            <td>
                                <strong class="text-dark" style="font-size: var(--fs-sm);">{{ $w->reference_id }}</strong>
                            </td>
                            <td class="text-secondary" style="font-size: var(--fs-sm);">
                                {{ $w->created_at ? $w->created_at->format('M d, Y h:i A') : '—' }}
                            </td>
                            <td>
                                @if($w->method === 'upi')
                                    <span class="badge bg-light text-dark border"><i class="bi bi-phone me-1 text-primary"></i> UPI</span>
                                @else
                                    <span class="badge bg-light text-dark border"><i class="bi bi-bank me-1 text-primary"></i> Bank</span>
                                @endif
                            </td>
                            <td style="font-size: var(--fs-sm);">
                                @if($w->method === 'upi')
                                    <span class="fw-medium text-dark">{{ $w->payout_details['upi_id'] ?? '—' }}</span>
                                @else
                                    <div><strong class="text-dark">{{ $w->payout_details['bank_name'] ?? 'Bank' }}</strong></div>
                                    <span class="text-muted" style="font-size: 11px;">A/C: ••••{{ substr($w->payout_details['account_number'] ?? '', -4) }} • IFSC: {{ $w->payout_details['ifsc'] ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                <strong class="text-dark" style="font-size: var(--fs-md);">₹{{ number_format($w->amount, 2) }}</strong>
                            </td>
                            <td>
                                <span class="badge-sh {{ $w->status_badge }}">{{ ucfirst($w->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-2 d-block mb-2 text-secondary"></i>
                                No withdrawal requests yet. When you complete projects, request payouts here!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Request Payout Modal --}}
<div class="modal fade" id="requestPayoutModal" tabindex="-1" aria-labelledby="requestPayoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="requestPayoutModalLabel">Request Fund Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('seller.payouts.request') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3 mb-3 rounded-3" style="background: var(--color-primary-subtle); border: 1px solid var(--color-primary-light);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-secondary" style="font-size: var(--fs-sm);">Available Balance:</span>
                            <strong class="text-primary" style="font-size: var(--fs-lg);">₹{{ number_format($availableBalance, 2) }}</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="withdrawAmount">Withdrawal Amount (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">₹</span>
                            <input type="number" step="1" min="100" max="{{ (int) $availableBalance }}" class="form-control-sh w-100" id="withdrawAmount" name="amount" value="{{ (int) $availableBalance }}" required>
                        </div>
                        <span class="text-muted" style="font-size: 11px;">Minimum ₹100. Payout processing takes 2-24 hours.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh">Payout Method <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check p-3 border rounded-3 flex-fill bg-white cursor-pointer" onclick="document.getElementById('methodUpi').checked = true; togglePayoutMethod('upi');">
                                <input class="form-check-input" type="radio" name="method" id="methodUpi" value="upi" checked onchange="togglePayoutMethod('upi')">
                                <label class="form-check-label fw-semibold cursor-pointer" for="methodUpi">
                                    <i class="bi bi-phone text-primary me-1"></i> UPI Transfer
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded-3 flex-fill bg-white cursor-pointer" onclick="document.getElementById('methodBank').checked = true; togglePayoutMethod('bank');">
                                <input class="form-check-input" type="radio" name="method" id="methodBank" value="bank" onchange="togglePayoutMethod('bank')">
                                <label class="form-check-label fw-semibold cursor-pointer" for="methodBank">
                                    <i class="bi bi-bank text-primary me-1"></i> Bank Account
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- UPI Form --}}
                    <div id="upiFields" class="mb-3">
                        <label class="form-label-sh" for="upiId">UPI VPA ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control-sh w-100" id="upiId" name="upi_id" placeholder="e.g. yourname@okaxis, yourname@upi">
                        <span class="text-muted" style="font-size: 11px;">Supports GPay, PhonePe, Paytm, BHIM and any bank UPI ID.</span>
                    </div>

                    {{-- Bank Fields --}}
                    <div id="bankFields" class="mb-3" style="display: none;">
                        <div class="mb-2">
                            <label class="form-label-sh" for="accHolder">Account Holder Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100" id="accHolder" name="account_holder" placeholder="Full name as in bank passbook">
                        </div>
                        <div class="mb-2">
                            <label class="form-label-sh" for="bankName">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control-sh w-100" id="bankName" name="bank_name" placeholder="e.g. State Bank of India, HDFC Bank">
                        </div>
                        <div class="row g-2">
                            <div class="col-7">
                                <label class="form-label-sh" for="accNumber">Account Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-sh w-100" id="accNumber" name="account_number" placeholder="Bank Account Number">
                            </div>
                            <div class="col-5">
                                <label class="form-label-sh" for="ifscCode">IFSC Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control-sh w-100 text-uppercase" id="ifscCode" name="ifsc" placeholder="SBIN0001234">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">
                        <i class="bi bi-arrow-up-right-circle me-1"></i> Submit Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePayoutMethod(method) {
    const upi = document.getElementById('upiFields');
    const bank = document.getElementById('bankFields');
    const upiInput = document.getElementById('upiId');
    const accHolder = document.getElementById('accHolder');
    const bankName = document.getElementById('bankName');
    const accNumber = document.getElementById('accNumber');
    const ifscCode = document.getElementById('ifscCode');

    if (method === 'upi') {
        upi.style.display = 'block';
        bank.style.display = 'none';
        if (upiInput) upiInput.required = true;
        if (accHolder) accHolder.required = false;
        if (bankName) bankName.required = false;
        if (accNumber) accNumber.required = false;
        if (ifscCode) ifscCode.required = false;
    } else {
        upi.style.display = 'none';
        bank.style.display = 'block';
        if (upiInput) upiInput.required = false;
        if (accHolder) accHolder.required = true;
        if (bankName) bankName.required = true;
        if (accNumber) accNumber.required = true;
        if (ifscCode) ifscCode.required = true;
    }
}
</script>

@endsection
