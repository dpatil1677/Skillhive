@extends('layouts.guest')

@section('title', 'Order ' . $order->order_number . ' — Seller View')

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
    $service      = $order->service;
    $client       = $order->client;
    $serviceTitle = $service ? $service->title : 'Service';
    $serviceSlug  = $service ? $service->slug  : '#';
    $serviceImg   = $service ? ($service->image ?: 'https://placehold.co/900x540/e8e0f5/6D28D9?text=Service') : '';
    $serviceCategory = $service ? ($service->category_name ?? 'General') : 'General';
    $clientName   = $client ? $client->name   : 'Client';
    $clientAvatar = $client ? $client->avatar : 'https://ui-avatars.com/api/?name=Client&background=6D28D9&color=fff&size=128';
    $packageName  = $order->package_name;
@endphp

<div class="container section-sh-sm">

    @include('components.breadcrumb', ['items' => [
        ['label' => 'Home',             'url' => route('home')],
        ['label' => 'Seller Dashboard', 'url' => route('seller.dashboard')],
        ['label' => 'Incoming Orders',  'url' => route('seller.orders')],
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
                Placed {{ $order->created_at ? $order->created_at->format('M d, Y h:i A') : 'Recently' }}
                • Due {{ $order->due_date }}
            </span>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('orders.invoice', $order->order_number) }}" target="_blank" class="btn-sh btn-sh-outline btn-sh-sm">
                <i class="bi bi-file-earmark-text me-1"></i> Tax Invoice
            </a>
            <a href="{{ route('seller.orders') }}" class="btn-sh btn-sh-outline btn-sh-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to My Orders
            </a>
        </div>
    </div>

    {{-- Progress Timeline --}}
    <div class="card-sh p-4 mb-5">
        <h5 class="service-section-title mb-4">Order Progress</h5>
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

        {{-- LEFT: Client Requirements & Deliverables Action Panel --}}
        <div class="col-lg-8">

            {{-- Service Info --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Service Ordered</h5>
                <div class="d-flex align-items-start gap-3">
                    <img src="{{ $serviceImg }}" alt="{{ $serviceTitle }}"
                         style="width: 130px; height: 86px; object-fit: cover; border-radius: var(--radius-md); flex-shrink: 0;">
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

            {{-- Client Requirements --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">
                    <i class="bi bi-file-text me-2 text-primary"></i>Client's Project Requirements
                </h5>
                <div class="p-3 bg-alt border rounded text-dark"
                     style="font-size: var(--fs-sm); line-height: var(--lh-relaxed); white-space: pre-line; min-height: 80px;">
                    {{ $order->requirements ?: 'No requirements submitted by client yet.' }}
                </div>
            </div>

            {{-- Deliverables Submission & Action Hub --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">
                    <i class="bi bi-box-seam me-2 text-primary"></i>Deliverables & Work Hub
                </h5>

                {{-- STATE 1: PENDING ACCEPTANCE --}}
                @if($rawSt === 'pending')
                    <div class="p-4 border rounded text-center mb-3" style="background: var(--color-warning-light); border-color: #FCD34D !important;">
                        <i class="bi bi-hourglass-split text-warning fs-1 mb-2 d-block"></i>
                        <h5 class="mb-2">New Order Waiting for Acceptance</h5>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                            Review the client's requirements above. Click <strong>Accept Order</strong> to confirm and start working on this project.
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="status" value="Accepted">
                                <button type="submit" class="btn-sh btn-sh-primary btn-sh-lg">
                                    <i class="bi bi-check-lg me-1"></i> Accept Order & Start Working
                                </button>
                            </form>
                            <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="status" value="Cancelled">
                                <button type="submit" class="btn-sh btn-sh-outline text-danger"
                                        onclick="return confirm('Decline this order? The client will be refunded.')">
                                    <i class="bi bi-x-lg me-1"></i> Decline
                                </button>
                            </form>
                        </div>
                    </div>

                {{-- STATE 2: ACCEPTED OR IN PROGRESS — WORK SUBMISSION FORM --}}
                @elseif(in_array($rawSt, ['accepted', 'in_progress']))
                    <div class="p-4 border rounded mb-4" style="background: #eff6ff; border-color: #93c5fd !important;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                            <div>
                                <span class="badge-sh badge-sh-primary mb-1">
                                    {{ $rawSt === 'accepted' ? 'Order Accepted' : 'In Progress' }}
                                </span>
                                <h5 class="mb-0 text-primary">Submit Completed Deliverables</h5>
                            </div>
                            @if($rawSt === 'accepted')
                                <form action="{{ route('orders.update-status', $order->order_number) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="status" value="In Progress">
                                    <button type="submit" class="btn-sh btn-sh-outline btn-sh-sm">
                                        <i class="bi bi-play-circle me-1"></i> Mark as In Progress
                                    </button>
                                </form>
                            @endif
                        </div>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                            Upload your finished work, provide download files, GitHub repository links, or live demo URLs for the client to review and approve.
                        </p>

                        <form action="{{ route('orders.submit-work', $order->order_number) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark" style="font-size: var(--fs-sm);">
                                    Work Summary & Notes for Client <span class="text-danger">*</span>
                                </label>
                                <textarea name="deliverables_note" rows="4" class="form-control-sh w-100"
                                          placeholder="Explain what deliverables you have completed, how to run or access the project, and any instructions..."
                                          required>{{ old('deliverables_note', $order->deliverables_note) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark" style="font-size: var(--fs-sm);">
                                    Project / Live Demo / GitHub URL (Optional)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border"><i class="bi bi-link-45deg"></i></span>
                                    <input type="url" name="deliverables_link" class="form-control-sh flex-1"
                                           placeholder="https://github.com/... or https://yourproject.com"
                                           value="{{ old('deliverables_link', $order->deliverables_link) }}">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark" style="font-size: var(--fs-sm);">
                                    Attach Deliverable File (ZIP, PDF, DOCX, Code, Images — Max 25MB)
                                </label>
                                <input type="file" name="deliverables_file" class="form-control-sh w-100" style="font-size: var(--fs-sm);">
                                @if($order->deliverables_file)
                                    <div class="mt-2 text-muted" style="font-size: var(--fs-xs);">
                                        Current file: <a href="{{ $order->deliverables_file }}" target="_blank" class="fw-semibold">{{ basename($order->deliverables_file) }}</a>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn-sh btn-sh-success btn-sh-lg">
                                    <i class="bi bi-upload me-1"></i> Submit Completed Deliverables
                                </button>
                                <span class="text-muted" style="font-size: var(--fs-xs);">Client will be notified to review and release funds</span>
                            </div>
                        </form>
                    </div>

                {{-- STATE 3: SUBMITTED (AWAITING APPROVAL) --}}
                @elseif($rawSt === 'submitted')
                    <div class="p-4 border rounded mb-4" style="background: var(--color-success-light); border-color: #86efac !important;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check2-circle text-success fs-3"></i>
                            <h5 class="mb-0 text-success">Deliverables Submitted — Awaiting Client Approval</h5>
                        </div>
                        <p class="text-secondary mb-3" style="font-size: var(--fs-sm);">
                            Your deliverables were successfully submitted {{ $order->submitted_at ? $order->submitted_at->diffForHumans() : 'recently' }}. Once the client approves, your payment of <strong>₹{{ number_format($order->price - $order->platform_fee) }}</strong> will be released.
                        </p>

                        <div class="card p-3 mb-3 bg-white border">
                            <h6 class="fw-semibold mb-2" style="font-size: var(--fs-sm);">Submitted Work Details:</h6>
                            <p class="mb-2 text-dark" style="font-size: var(--fs-sm); white-space: pre-line;">{{ $order->deliverables_note }}</p>

                            @if($order->deliverables_link)
                                <div class="mb-2">
                                    <span class="text-secondary" style="font-size: var(--fs-xs);">Project Link:</span>
                                    <a href="{{ $order->deliverables_link }}" target="_blank" class="btn-sh btn-sh-outline btn-sh-sm ms-2">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Open Live Link
                                    </a>
                                </div>
                            @endif

                            @if($order->deliverables_file)
                                <div>
                                    <span class="text-secondary" style="font-size: var(--fs-xs);">Attached Deliverables:</span>
                                    <a href="{{ $order->deliverables_file }}" download class="btn-sh btn-sh-secondary btn-sh-sm ms-2">
                                        <i class="bi bi-download me-1"></i> Download Deliverables File
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Toggle to re-upload / edit deliverables --}}
                        <button class="btn-sh btn-sh-outline btn-sh-sm" type="button" data-bs-toggle="collapse" data-bs-target="#editDeliverablesCollapse">
                            <i class="bi bi-pencil me-1"></i> Re-submit or Update Deliverables
                        </button>

                        <div class="collapse mt-3" id="editDeliverablesCollapse">
                            <form action="{{ route('orders.submit-work', $order->order_number) }}" method="POST" enctype="multipart/form-data" class="card p-3 bg-white border">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label fw-semibold text-dark" style="font-size: var(--fs-sm);">Updated Notes:</label>
                                    <textarea name="deliverables_note" rows="3" class="form-control-sh w-100" required>{{ $order->deliverables_note }}</textarea>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold text-dark" style="font-size: var(--fs-sm);">Updated Link:</label>
                                    <input type="url" name="deliverables_link" class="form-control-sh w-100" value="{{ $order->deliverables_link }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold text-dark" style="font-size: var(--fs-sm);">Replace File:</label>
                                    <input type="file" name="deliverables_file" class="form-control-sh w-100">
                                </div>
                                <button type="submit" class="btn-sh btn-sh-primary btn-sh-sm">Update Submission</button>
                            </form>
                        </div>
                    </div>

                {{-- STATE 4: COMPLETED --}}
                @elseif($rawSt === 'completed')
                    <div class="p-4 border rounded mb-3 text-center" style="background: var(--color-success-light); border-color: #86efac !important;">
                        <i class="bi bi-trophy-fill text-success fs-1 mb-2 d-block"></i>
                        <h4 class="mb-2 text-success">Order Completed & Payment Released!</h4>
                        <p class="text-secondary mb-2" style="font-size: var(--fs-sm);">
                            The client has approved your work and released your earnings of:
                        </p>
                        <div class="badge-sh badge-sh-success py-2 px-4 fs-5 mb-3">
                            ₹{{ number_format($order->price - $order->platform_fee) }} Earned
                        </div>

                        @if($order->deliverables_note || $order->deliverables_file || $order->deliverables_link)
                            <div class="card p-3 text-start bg-white border mt-3">
                                <h6 class="fw-semibold mb-2" style="font-size: var(--fs-sm);">Approved Deliverables:</h6>
                                <p class="mb-2 text-dark" style="font-size: var(--fs-sm); white-space: pre-line;">{{ $order->deliverables_note }}</p>
                                @if($order->deliverables_link)
                                    <a href="{{ $order->deliverables_link }}" target="_blank" class="btn-sh btn-sh-outline btn-sh-sm me-2 mb-1">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Live Project Link
                                    </a>
                                @endif
                                @if($order->deliverables_file)
                                    <a href="{{ $order->deliverables_file }}" download class="btn-sh btn-sh-secondary btn-sh-sm mb-1">
                                        <i class="bi bi-download me-1"></i> Deliverable File
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                {{-- STATE 5: CANCELLED --}}
                @elseif($rawSt === 'cancelled')
                    <div class="p-4 border rounded text-center mb-3" style="background: #fef2f2; border-color: #fca5a5 !important;">
                        <i class="bi bi-x-circle-fill text-danger fs-1 mb-2 d-block"></i>
                        <h5 class="mb-2 text-danger">Order Cancelled</h5>
                        <p class="text-secondary mb-0" style="font-size: var(--fs-sm);">This order was cancelled and is no longer active.</p>
                    </div>
                @endif

                {{-- Message Client Button --}}
                @if(!in_array($rawSt, ['cancelled']))
                    <div class="d-flex justify-content-start mt-3">
                        <a href="{{ route('chat.index', ['with' => $order->client_id]) }}" class="btn-sh btn-sh-outline btn-sh-sm">
                            <i class="bi bi-chat-dots me-1"></i> Open Direct Chat with Client
                        </a>
                    </div>
                @endif
            </div>

        </div>

        {{-- RIGHT: Order Summary & Client Card --}}
        <div class="col-lg-4">

            {{-- Financial Breakdown --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Order Breakdown</h5>

                <div class="d-flex justify-content-between py-2 border-bottom-sh" style="font-size: var(--fs-sm);">
                    <span class="text-secondary">Service Price</span>
                    <strong>₹{{ number_format($order->price) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom-sh" style="font-size: var(--fs-sm);">
                    <span class="text-secondary">Platform Fee (deducted)</span>
                    <strong class="text-danger">−₹{{ number_format($order->platform_fee) }}</strong>
                </div>
                <div class="d-flex justify-content-between py-3 my-2">
                    <strong style="font-size: var(--fs-md);">Your Net Earnings</strong>
                    <strong class="text-success" style="font-size: var(--fs-xl);">
                        ₹{{ number_format($order->price - $order->platform_fee) }}
                    </strong>
                </div>
                <div class="badge-sh badge-sh-{{ $rawSt === 'completed' ? 'success' : 'warning' }} w-100 text-center py-2" style="font-size: var(--fs-xs);">
                    <i class="bi bi-shield-check me-1"></i>
                    {{ $rawSt === 'completed' ? 'Payment Released to You' : 'Payment Held in Escrow' }}
                </div>
            </div>

            {{-- Client Card --}}
            <div class="card-sh p-4 mb-4">
                <h5 class="service-section-title mb-3">Client (Buyer)</h5>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ $clientAvatar }}" alt="{{ $clientName }}"
                         style="width: 56px; height: 56px; border-radius: 50%; object-fit: cover;">
                    <div>
                        <h6 class="mb-1 fw-semibold">{{ $clientName }}</h6>
                        <span class="text-secondary d-block" style="font-size: var(--fs-xs);">
                            {{ $client ? ($client->college ?: 'SkillHive Member') : '' }}
                        </span>
                    </div>
                </div>
                <a href="{{ route('chat.index', ['with' => $order->client_id]) }}" class="btn-sh btn-sh-primary btn-sh-sm w-100">
                    <i class="bi bi-chat-dots me-1"></i> Open Chat with Client
                </a>
            </div>

            {{-- Order Meta Info --}}
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
                        <span class="text-secondary">Placed</span>
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

@endsection
