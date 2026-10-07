@extends('layouts.admin')

@section('title', 'Commission #' . $commission->commission_number)
@section('page_title', 'Commission Audit Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.commissions.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Commissions
    </a>
</div>

<div class="row g-4">
    <!-- Left Details Column -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Commission Reference</span>
                    <h4 class="fw-bold mb-0 font-monospace text-dark">{{ $commission->commission_number }}</h4>
                </div>
                <div>
                    @php
                        $badges = [
                            'pending'   => 'bg-warning text-dark',
                            'approved'  => 'bg-primary text-white',
                            'available' => 'bg-success text-white',
                            'paid'      => 'bg-info text-white',
                            'rejected'  => 'bg-danger text-white',
                            'reversed'  => 'bg-secondary text-white',
                        ];
                        $bClass = $badges[$commission->status] ?? 'bg-light text-dark';
                    @endphp
                    <span class="badge {{ $bClass }} fs-6 px-3 py-2 text-capitalize">
                        {{ $commission->status }}
                    </span>
                </div>
            </div>

            <div class="card-body p-4 pt-2">
                <!-- Highlight Financial Row -->
                <div class="row g-3 bg-light p-3 rounded-3 border mb-4">
                    <div class="col-sm-4 text-center border-end">
                        <small class="text-muted text-uppercase fw-semibold d-block">Order Base Amount</small>
                        <h4 class="fw-bold mb-0 text-dark">₹{{ number_format($commission->order_amount, 2) }}</h4>
                    </div>
                    <div class="col-sm-4 text-center border-end">
                        <small class="text-muted text-uppercase fw-semibold d-block">Commission Rate</small>
                        <h4 class="fw-bold mb-0 text-primary">
                            @if($commission->commission_type === 'percent')
                                {{ $commission->commission_rate }}%
                            @else
                                ₹{{ number_format($commission->commission_rate, 2) }} Fixed
                            @endif
                        </h4>
                    </div>
                    <div class="col-sm-4 text-center">
                        <small class="text-muted text-uppercase fw-semibold d-block">Commission Liability</small>
                        <h4 class="fw-bold mb-0 text-success">₹{{ number_format($commission->commission_amount, 2) }}</h4>
                    </div>
                </div>

                <!-- Product Snapshot -->
                <h6 class="fw-bold mb-3"><i class="bi bi-box-seam text-warning me-2"></i>Product Details</h6>
                @if($commission->product)
                    <div class="d-flex align-items-center gap-3 p-3 bg-white rounded-3 border mb-4">
                        @if($commission->product->image_url)
                            <img src="{{ $commission->product->image_url }}" alt="{{ $commission->product->name }}" style="width: 70px; height: 70px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-secondary" style="width: 70px; height: 70px;">
                                <i class="bi bi-image fs-3"></i>
                            </div>
                        @endif
                        <div>
                            <h6 class="fw-bold mb-1">{{ $commission->product->name }}</h6>
                            <div class="d-flex gap-3 small text-muted">
                                <span>SKU: <strong class="font-monospace text-dark">{{ $commission->product->sku ?? 'N/A' }}</strong></span>
                                <span>Unit Price: <strong class="text-dark">₹{{ number_format($commission->product->price, 2) }}</strong></span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-light border mb-4">Product details unavailable.</div>
                @endif

                <!-- Order Details -->
                <h6 class="fw-bold mb-3"><i class="bi bi-bag-check text-warning me-2"></i>Order Context</h6>
                @if($commission->order)
                    <div class="p-3 bg-white rounded-3 border mb-4">
                        <div class="row g-2 small">
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Order Number</span>
                                <strong class="font-monospace text-dark">{{ $commission->order->order_number }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Order Date</span>
                                <strong>{{ $commission->order->created_at->format('d M Y') }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Order Total</span>
                                <strong class="text-dark">₹{{ number_format($commission->order->total_amount, 2) }}</strong>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Order Status</span>
                                <span class="badge bg-light text-dark border text-capitalize">{{ $commission->order->status }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Controls -->
                @if($commission->status === 'pending')
                    <div class="d-flex gap-2 justify-content-end pt-3 border-top">
                        <form method="POST" action="{{ route('admin.commissions.approve', $commission->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success fw-bold px-4" onclick="return confirm('Approve this commission immediately?')">
                                <i class="bi bi-check-circle me-1"></i> Approve Commission
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger fw-bold px-4" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle me-1"></i> Reject Commission
                        </button>
                    </div>

                    <div class="modal fade" id="rejectModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('admin.commissions.reject', $commission->id) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h6 class="modal-title fw-bold">Reject Commission #{{ $commission->commission_number }}</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Reason for Rejection</label>
                                            <textarea name="reason" rows="3" class="form-control" placeholder="Specify valid reason (e.g. Returned product, self-referral, policy breach)" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold">Confirm Reject</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Sidebar Column: Astrologer & Wallet -->
    <div class="col-lg-4">
        <!-- Astrologer Summary Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-person-badge text-warning me-2"></i>Beneficiary Astrologer</h6>
            </div>
            <div class="card-body p-4 pt-2">
                @if($commission->astrologer)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 50px; height: 50px;">
                            {{ strtoupper(substr($commission->astrologer->display_name ?? 'A', 0, 1)) }}
                        </div>
                        <div>
                            <strong class="d-block text-dark">{{ $commission->astrologer->display_name }}</strong>
                            <span class="badge bg-light text-warning border font-monospace">{{ $commission->astrologer->referral_code }}</span>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Available Balance:</span>
                            <strong class="text-success">₹{{ number_format($commission->astrologer->wallet->available_balance ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Pending Balance:</span>
                            <strong class="text-warning">₹{{ number_format($commission->astrologer->wallet->pending_balance ?? 0, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Lifetime Earnings:</span>
                            <strong class="text-dark">₹{{ number_format($commission->astrologer->wallet->lifetime_earnings ?? 0, 2) }}</strong>
                        </div>
                    </div>
                @else
                    <p class="text-muted small mb-0">No astrologer linked.</p>
                @endif
            </div>
        </div>

        <!-- Timeline Audit Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-warning me-2"></i>Lifecycle Audit Log</h6>
            </div>
            <div class="card-body p-4 pt-2">
                <ul class="list-unstyled mb-0 small">
                    <li class="mb-2">
                        <span class="text-muted d-block">Created On</span>
                        <strong>{{ $commission->created_at->format('d M Y, h:i A') }}</strong>
                    </li>
                    <li class="mb-2">
                        <span class="text-muted d-block">Available Release Date</span>
                        <strong>{{ $commission->available_at ? $commission->available_at->format('d M Y, h:i A') : '—' }}</strong>
                    </li>
                    @if($commission->approved_at)
                    <li class="mb-2">
                        <span class="text-muted d-block">Approved On</span>
                        <strong>{{ $commission->approved_at->format('d M Y, h:i A') }}</strong>
                    </li>
                    @endif
                    @if($commission->rejected_at)
                    <li class="mb-2">
                        <span class="text-muted d-block text-danger">Rejected Reason</span>
                        <strong class="text-danger">{{ $commission->rejection_reason }}</strong>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
