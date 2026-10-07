@extends('astrologer.layouts.app')

@section('title', 'Commission #' . $commission->commission_number)
@section('page_title', 'Commission Details')

@section('content')
<div class="mb-3">
    <a href="{{ route('astrologer.commissions.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Commissions
    </a>
</div>

<div class="row g-4">
    <!-- Main Details Column -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
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
                        <small class="text-muted text-uppercase fw-semibold d-block">Order Item Value</small>
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
                        <small class="text-muted text-uppercase fw-semibold d-block">Your Commission</small>
                        <h4 class="fw-bold mb-0 text-success">₹{{ number_format($commission->commission_amount, 2) }}</h4>
                    </div>
                </div>

                <!-- Product Snapshot -->
                <h6 class="fw-bold mb-3"><i class="bi bi-box-seam text-warning me-2"></i>Product Information</h6>
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
                                <span>Catalog Price: <strong class="text-dark">₹{{ number_format($commission->product->price, 2) }}</strong></span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-light border mb-4">Product details unavailable.</div>
                @endif

                <!-- Order Snapshot -->
                <h6 class="fw-bold mb-3"><i class="bi bi-bag-check text-warning me-2"></i>Associated Order</h6>
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
                                <span class="text-muted d-block">Order Status</span>
                                <span class="badge bg-light text-dark border text-capitalize">{{ $commission->order->status }}</span>
                            </div>
                            <div class="col-6 col-md-3">
                                <span class="text-muted d-block">Payment Method</span>
                                <span class="text-uppercase fw-semibold">{{ $commission->order->payment_method ?? 'Online' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-light border mb-4">Order record unavailable.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Timeline & Hold Column -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-calendar-event text-warning me-2"></i>Timeline & Lifecycle</h6>
            </div>
            <div class="card-body p-4 pt-2">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3 d-flex gap-3">
                        <div class="text-primary"><i class="bi bi-check-circle-fill fs-5"></i></div>
                        <div>
                            <strong class="d-block small">Earned on Purchase</strong>
                            <small class="text-muted">{{ $commission->created_at->format('d M Y, h:i A') }}</small>
                        </div>
                    </li>

                    <li class="mb-3 d-flex gap-3">
                        @if($commission->available_at && $commission->available_at->isPast())
                            <div class="text-success"><i class="bi bi-check-circle-fill fs-5"></i></div>
                            <div>
                                <strong class="d-block small">Hold Period Matured</strong>
                                <small class="text-success">Available since {{ $commission->available_at->format('d M Y') }}</small>
                            </div>
                        @else
                            <div class="text-warning"><i class="bi bi-clock-history fs-5"></i></div>
                            <div>
                                <strong class="d-block small">In Return Hold Window</strong>
                                <small class="text-muted">Releases on {{ $commission->available_at ? $commission->available_at->format('d M Y') : 'Pending verification' }}</small>
                            </div>
                        @endif
                    </li>

                    @if($commission->approved_at)
                    <li class="mb-3 d-flex gap-3">
                        <div class="text-info"><i class="bi bi-patch-check-fill fs-5"></i></div>
                        <div>
                            <strong class="d-block small">Approved by Admin</strong>
                            <small class="text-muted">{{ $commission->approved_at->format('d M Y, h:i A') }}</small>
                        </div>
                    </li>
                    @endif

                    @if($commission->rejected_at)
                    <li class="mb-3 d-flex gap-3">
                        <div class="text-danger"><i class="bi bi-x-circle-fill fs-5"></i></div>
                        <div>
                            <strong class="d-block small text-danger">Rejected</strong>
                            <small class="text-muted">{{ $commission->rejected_at->format('d M Y') }} ({{ $commission->rejection_reason }})</small>
                        </div>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
