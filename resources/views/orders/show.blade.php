@extends('layouts.guest')

@section('title', 'Order ' . $order->order_number . ' — SkillHive')

@section('content')

@php
    $rawSt        = $order->raw_status;
    $statusLabel  = $order->status;
    $badgeClass   = match($rawSt) {
        'completed'                          => 'badge-sh-success',
        'cancelled'                          => 'badge-sh-danger',
        'accepted','in_progress','submitted' => 'badge-sh-primary',
        default                              => 'badge-sh-warning',
    };
    $service         = $order->service;
    $freelancerModel = $order->freelancer;
    $serviceTitle    = $service ? $service->title : 'Service';
    $serviceSlug     = $service ? $service->slug  : '#';
    $serviceImg      = $service ? ($service->image ?: 'https://placehold.co/900x540/e8e0f5/6D28D9?text=Service') : '';
    $serviceCategory = $service ? ($service->category_name ?? 'General') : 'General';
    $freelancerName   = $freelancerModel ? $freelancerModel->name   : 'Freelancer';
    $freelancerAvatar = $freelancerModel ? $freelancerModel->avatar : 'https://ui-avatars.com/api/?name=FL&background=6D28D9&color=fff&size=128';
    $freelancerSlug   = $freelancerModel ? $freelancerModel->slug   : '#';
    $freelancerCollege= $freelancerModel ? ($freelancerModel->college ?: 'SkillHive Member') : '';
    $freelancerRating = $freelancerModel && $freelancerModel->reviewsAsFreelancer()->exists()
        ? round($freelancerModel->reviewsAsFreelancer()->avg('rating'), 1)
        : 5.0;
    $packageName      = $order->package_name;
    $isReviewed       = $order->reviewed;
@endphp

<div class="container section-sh-sm">

    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home',      'url' => route('home')],
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'My Orders', 'url' => route('orders.index')],
        ['label' => $order->order_number],
    ]])

    {{-- Order Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h1 style="font-size: var(--fs-2xl); font-weight: var(--fw-bold); margin: 0;">
                    Order {{ $order->order_number }}
                </h1>
                <span class="badge-sh {{ $badgeClass }}" style="font-size: var(--fs-sm);">{{ $statusLabel }}</span>
            </div>
            <span class="text-secondary" style="font-size: var(--fs-sm);">
                Placed on {{ $order->created_at ? $order->created_at->format('M d, Y') : '—' }}
                • Expected delivery: {{ $order->due_date }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="btn-sh btn-sh-outline btn-sh-sm">
                <i class="bi bi-file-earmark-text me-1"></i> Tax Invoice
            </a>
            @if(in_array($rawSt, ['pending', 'accepted', 'in_progress', 'submitted']))
                <button type="button" class="btn-sh btn-sh-outline btn-sh-sm text-danger" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
                    <i class="bi bi-x-circle me-1"></i> Cancel Order
                </button>
            @endif
            <a href="{{ route('orders.index') }}" class="btn-sh btn-sh-outline btn-sh-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to My Orders
            </a>
        </div>
    </div>

    {{-- Progress Timeline --}}
    <div class="card-sh p-4 mb-5">
        <h5 class="service-section-title mb-4">Order Progress Status</h5>
        <div class="d-flex justify-content-between position-relative flex-wrap gap-3">
            @foreach($order->timeline as $stepIdx => $step)
                <div class="d-flex flex-column align-items-center text-center flex-1 position-relative" style="min-width: 120px;">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mb-2"
                         style="width: 38px; height: 38px; font-weight: 600; font-size: var(--fs-sm);
                                {{ $step['completed'] ? 'background: var(--color-success); color: #fff;' : 'background: var(--color-border); color: var(--color-text-secondary);' }}">
                        @if($step['completed'])
                            <i class="bi bi-check-lg"></i>
                        @else
                            {{ $stepIdx + 1 }}
                        @endif
                    </div>
                    <strong style="font-size: var(--fs-xs); color: {{ $step['completed'] ? 'var(--color-text)' : 'var(--color-text-secondary)' }};">
                        {{ $step['title'] }}
                    </strong>
                    <span class="text-muted" style="font-size: 11px;">{{ $step['time'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="row g-5">

        {{-- LEFT: Details, Requirements & Deliverables --}}
        <div class="col-lg-8">

            {{-- Service Info Card --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Service Information</h5>
                <div class="d-flex align-items-start gap-3">
                    <img src="{{ $serviceImg }}" alt="{{ $serviceTitle }}"
                         style="width: 120px; height: 80px; object-fit: cover; border-radius: var(--radius-md); flex-shrink: 0;">
                    <div>
                        <span class="badge-sh badge-sh-primary mb-1">{{ $serviceCategory }}</span>
                        <h5 class="mb-1 fw-semibold">
                            <a href="{{ route('service.show', $serviceSlug) }}" class="text-dark text-decoration-none">{{ $serviceTitle }}</a>
                        </h5>
                        <div class="text-secondary" style="font-size: var(--fs-sm);">
                            Package: <strong class="text-dark">{{ $packageName }} Tier</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Project Requirements --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Submitted Project Requirements</h5>
                <div class="p-3 bg-alt border rounded text-dark"
                     style="font-size: var(--fs-sm); line-height: var(--lh-relaxed); white-space: pre-line;">
                    {{ $order->requirements ?: 'No requirements submitted.' }}
                </div>
            </div>

            {{-- Deliverables & Client Review Hub --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Deliverables & Order Status
                </h5>

                {{-- STATUS 1: PENDING ACCEPTANCE --}}
                @if($rawSt === 'pending')
                    <div class="p-4 bg-alt rounded text-center border">
                        <i class="bi bi-hourglass-split text-warning fs-1 mb-2 d-block"></i>
                        <h6 class="mb-1">Waiting for Freelancer Acceptance</h6>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                            <strong>{{ $freelancerName }}</strong> has been notified and will review your project brief shortly.
                        </p>
                        <a href="{{ route('chat.index', ['with' => $order->freelancer_id]) }}" class="btn-sh btn-sh-outline btn-sh-sm">
                            <i class="bi bi-chat-dots me-1"></i> Message Freelancer
                        </a>
                    </div>

                {{-- STATUS 2: ACCEPTED / IN PROGRESS --}}
                @elseif(in_array($rawSt, ['accepted', 'in_progress']))
                    <div class="p-4 rounded border text-center" style="background: #eff6ff; border-color: #93c5fd !important;">
                        <i class="bi bi-tools text-primary fs-1 mb-2 d-block"></i>
                        <h5 class="text-primary mb-1">Project is In Progress</h5>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                            <strong>{{ $freelancerName }}</strong> has accepted your order and is actively working on your deliverables.
                        </p>
                        <a href="{{ route('chat.index', ['with' => $order->freelancer_id]) }}" class="btn-sh btn-sh-primary btn-sh-sm">
                            <i class="bi bi-chat-text-fill me-1"></i> Chat with Freelancer
                        </a>
                    </div>

                {{-- STATUS 3: SUBMITTED (DELIVERABLES READY FOR REVIEW) --}}
                @elseif($rawSt === 'submitted')
                    <div class="p-4 bg-primary-subtle rounded border border-primary mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-box-seam text-primary fs-3"></i>
                            <h5 class="text-primary mb-0">Deliverables Submitted for Your Approval!</h5>
                        </div>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                            The freelancer has completed the project deliverables. Please review the notes and download the files below.
                        </p>

                        <div class="card p-3 mb-3 bg-white border">
                            <h6 class="fw-semibold mb-2" style="font-size: var(--fs-sm);">Freelancer's Deliverables Note:</h6>
                            <p class="mb-3 text-dark" style="font-size: var(--fs-sm); white-space: pre-line;">{{ $order->deliverables_note ?: 'No specific notes provided.' }}</p>

                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                @if($order->deliverables_link)
                                    <a href="{{ $order->deliverables_link }}" target="_blank" class="btn-sh btn-sh-outline btn-sh-sm">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Open Project / Demo Link
                                    </a>
                                @endif

                                @if($order->deliverables_file)
                                    <a href="{{ $order->deliverables_file }}" download class="btn-sh btn-sh-secondary btn-sh-sm">
                                        <i class="bi bi-download me-1"></i> Download Deliverable File ({{ basename($order->deliverables_file) }})
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Client Approval & Revision Actions --}}
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="status" value="Completed">
                                <button type="submit" class="btn-sh btn-sh-success btn-sh-lg"
                                        onclick="return confirm('Approve these deliverables? Payment of ₹{{ number_format($order->price) }} will be released to {{ $freelancerName }}.')">
                                    <i class="bi bi-check-circle me-1"></i> Approve & Release Payment
                                </button>
                            </form>

                            <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="status" value="In Progress">
                                <button type="submit" class="btn-sh btn-sh-outline text-warning"
                                        onclick="return confirm('Request revisions? The order will be moved back to In Progress.')">
                                    <i class="bi bi-arrow-repeat me-1"></i> Request Revision
                                </button>
                            </form>
                        </div>
                    </div>

                {{-- STATUS 4: COMPLETED --}}
                @elseif($rawSt === 'completed')
                    <div class="p-4 bg-success-light rounded text-center border border-success mb-3">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-2 d-block"></i>
                        <h5 class="text-success mb-1">Order Completed & Approved</h5>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                            Work delivered and approved. Funds released to <strong>{{ $freelancerName }}</strong>.
                        </p>

                        @if($order->deliverables_note || $order->deliverables_file || $order->deliverables_link)
                            <div class="card p-3 text-start bg-white border mb-3">
                                <h6 class="fw-semibold mb-2" style="font-size: var(--fs-sm);">Approved Deliverables:</h6>
                                <p class="mb-2 text-dark" style="font-size: var(--fs-sm); white-space: pre-line;">{{ $order->deliverables_note }}</p>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if($order->deliverables_link)
                                        <a href="{{ $order->deliverables_link }}" target="_blank" class="btn-sh btn-sh-outline btn-sh-sm">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Project Link
                                        </a>
                                    @endif
                                    @if($order->deliverables_file)
                                        <a href="{{ $order->deliverables_file }}" download class="btn-sh btn-sh-secondary btn-sh-sm">
                                            <i class="bi bi-download me-1"></i> Download Deliverable File
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(!$isReviewed)
                            <button type="button" class="btn-sh btn-sh-primary" data-bs-toggle="modal" data-bs-target="#leaveReviewModal">
                                <i class="bi bi-star-fill me-1"></i> Leave a Review for {{ $freelancerName }}
                            </button>
                        @else
                            <span class="badge-sh badge-sh-success" style="font-size: var(--fs-xs);">
                                <i class="bi bi-check2 me-1"></i> Review Submitted
                            </span>
                        @endif
                    </div>

                {{-- STATUS 5: CANCELLED --}}
                @elseif($rawSt === 'cancelled')
                    <div class="p-4 rounded text-center border" style="background: #fef2f2; border-color: #fca5a5 !important;">
                        <i class="bi bi-x-circle-fill text-danger fs-1 mb-2 d-block"></i>
                        <h5 class="text-danger mb-1">Order Cancelled</h5>
                        @if($order->cancellation_reason)
                            <div class="p-3 my-3 bg-white rounded border text-start">
                                <span class="text-secondary d-block" style="font-size: 11px; text-transform: uppercase; font-weight: 600;">
                                    Cancellation Reason (Cancelled by {{ ucfirst($order->cancelled_by ?: 'client') }}):
                                </span>
                                <p class="mb-0 text-dark mt-1" style="font-size: var(--fs-sm);">{{ $order->cancellation_reason }}</p>
                            </div>
                        @endif
                        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">
                            This order is closed and the simulated escrow amount of <strong>₹{{ number_format($order->total_amount, 2) }}</strong> has been returned to your payment balance.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Summary & Freelancer Card --}}
        <div class="col-lg-4">

            {{-- Financial Breakdown --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Order Summary</h5>

                <div class="d-flex justify-content-between py-2 border-bottom-sh" style="font-size: var(--fs-sm);">
                    <span class="text-secondary">Subtotal ({{ $packageName }})</span>
                    <strong>₹{{ number_format($order->price) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom-sh" style="font-size: var(--fs-sm);">
                    <span class="text-secondary">Platform Fee</span>
                    <strong>₹{{ number_format($order->platform_fee) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-3 my-2">
                    <strong style="font-size: var(--fs-md);">Total Paid</strong>
                    <strong class="text-primary" style="font-size: var(--fs-xl);">
                        ₹{{ number_format($order->total_amount) }}
                    </strong>
                </div>
                <div class="badge-sh badge-sh-{{ $rawSt === 'completed' ? 'success' : 'primary' }} w-100 text-center py-2" style="font-size: var(--fs-xs);">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ $rawSt === 'completed' ? 'Payment Completed' : 'Payment Protected in Escrow' }}
                </div>
            </div>

            {{-- Freelancer Profile Card --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Freelancer</h5>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $freelancerAvatar }}" alt="{{ $freelancerName }}"
                         style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <h6 class="mb-1 fw-semibold">{{ $freelancerName }}</h6>
                        <span class="text-secondary d-block" style="font-size: var(--fs-xs);">{{ $freelancerCollege }}</span>
                        <div class="d-flex align-items-center gap-1 mt-1">
                            <i class="bi bi-star-fill text-warning" style="font-size: 11px;"></i>
                            <strong style="font-size: var(--fs-xs);">{{ $freelancerRating }}</strong>
                        </div>
                    </div>
                </div>
                <a href="{{ route('chat.index', ['with' => $order->freelancer_id]) }}" class="btn-sh btn-sh-primary btn-sh-sm w-100">
                    <i class="bi bi-chat-dots me-1"></i> Chat with Freelancer
                </a>
            </div>

            {{-- Order Details Meta Card --}}
            <div class="card-sh p-4">
                <h5 class="service-section-title mb-3">Order Details</h5>
                <ul class="list-unstyled mb-0" style="font-size: var(--fs-sm);">
                    <li class="d-flex justify-content-between py-1 border-bottom-sh">
                        <span class="text-secondary">Order #</span>
                        <strong>{{ $order->order_number }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom-sh">
                        <span class="text-secondary">Package</span>
                        <strong>{{ $packageName }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom-sh">
                        <span class="text-secondary">Status</span>
                        <span class="badge-sh {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </li>
                    <li class="d-flex justify-content-between py-1 border-bottom-sh">
                        <span class="text-secondary">Placed On</span>
                        <strong>{{ $order->created_at ? $order->created_at->format('M d, Y') : '—' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between py-1">
                        <span class="text-secondary">Due Date</span>
                        <strong>{{ $order->due_date }}</strong>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>

{{-- Leave Review Modal --}}
@if($rawSt === 'completed' && !$isReviewed)
<div class="modal fade" id="leaveReviewModal" tabindex="-1" aria-labelledby="leaveReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveReviewModalLabel">Review Your Experience with {{ $freelancerName }}</h5>
                <button type="button" class="btn-close" data-bs-toggle="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reviews.store') }}" method="POST">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="service_id" value="{{ $order->service_id }}">
                <input type="hidden" name="freelancer_id" value="{{ $order->freelancer_id }}">
                <div class="modal-body p-4">
                    <div class="mb-3 text-center">
                        <label class="form-label d-block text-secondary">Rating</label>
                        <div class="d-flex justify-content-center gap-2" id="starRatingSelect">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star-fill text-muted fs-3 cursor-pointer rating-star" data-rating="{{ $i }}"></i>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="selectedRating" value="5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reviewComment">Your Feedback</label>
                        <textarea class="form-control-sh w-100" id="reviewComment" name="comment" rows="4" placeholder="How was the quality of work, communication, and delivery speed?" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-sh btn-sh-primary">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('selectedRating');

    function highlightStars(val) {
        stars.forEach((s, idx) => {
            if (idx < val) {
                s.classList.remove('text-muted');
                s.classList.add('text-warning');
            } else {
                s.classList.remove('text-warning');
                s.classList.add('text-muted');
            }
        });
    }

    highlightStars(5);

    stars.forEach(s => {
        s.addEventListener('click', function () {
            const val = parseInt(this.dataset.rating);
            ratingInput.value = val;
            highlightStars(val);
        });
    });
});
</script>
@endpush
@endif

{{-- Cancel Order Modal --}}
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="cancelOrderModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Cancel Order #{{ $order->order_number }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.cancel', $order->order_number) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning p-3 rounded-3 mb-3 border">
                        <strong class="d-block mb-1">Escrow Refund Protection:</strong>
                        Your total payment of <strong>₹{{ number_format($order->total_amount, 2) }}</strong> will be released from escrow and refunded.
                    </div>

                    <div class="mb-3">
                        <label class="form-label-sh" for="cancelReason">Reason for Cancellation <span class="text-danger">*</span></label>
                        <select class="form-select mb-2" onchange="if(this.value && this.value !== 'Other') document.getElementById('cancelReason').value = this.value;">
                            <option value="">Select a reason...</option>
                            <option value="Changed project requirements">Changed project requirements</option>
                            <option value="Placed by mistake / Duplicate order">Placed by mistake / Duplicate order</option>
                            <option value="Found alternative solution">Found alternative solution</option>
                            <option value="Freelancer unresponsive / Unable to fulfill">Freelancer unresponsive / Unable to fulfill</option>
                            <option value="Other">Other reason...</option>
                        </select>
                        <textarea class="form-control-sh w-100" id="cancelReason" name="cancellation_reason" rows="3" placeholder="Please explain why you are cancelling this order..." required minlength="5"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn-sh btn-sh-outline" data-bs-dismiss="modal">Keep Order</button>
                    <button type="submit" class="btn-sh btn-sh-danger" onclick="return confirm('Confirm cancellation? Escrow funds will be refunded.');">
                        <i class="bi bi-x-circle me-1"></i> Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
