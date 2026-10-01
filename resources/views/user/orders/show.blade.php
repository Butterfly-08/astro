@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' — AstroVani')

@section('content')
<div class="container py-4 my-3">

    {{-- Back Button --}}
    <a href="{{ route('user.orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill mb-4">
        <i class="bi bi-arrow-left me-1"></i> Back to My Orders
    </a>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Order Header Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-4" style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 100%); border-bottom: 3px solid #F5B041;">
                <div class="row align-items-center">
                    <div class="col-md-6 text-white">
                        <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold">
                            <i class="bi bi-bag-check me-1"></i> Order Details
                        </span>
                        <h4 class="fw-bold mb-0">#{{ $order->order_number }}</h4>
                        <small class="text-white-50">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <div class="d-flex gap-2 justify-content-md-end">
                            <span class="badge bg-{{ $order->status_badge['class'] }} px-3 py-2 fs-6">
                                {{ $order->status_badge['label'] }}
                            </span>
                            <span class="badge bg-{{ $order->payment_status_badge['class'] }} px-3 py-2 fs-6">
                                <i class="bi bi-credit-card me-1"></i>{{ $order->payment_status_badge['label'] }}
                            </span>
                        </div>
                        @if($order->isCancellable())
                            <form method="POST" action="{{ route('user.orders.cancel', $order) }}" class="mt-2"
                                  onsubmit="return confirm('Cancel order #{{ $order->order_number }}? Stock will be restored.')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                    <i class="bi bi-x-circle me-1"></i> Cancel Order
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Order Progress Tracker --}}
            <div class="px-4 py-3 bg-light border-bottom">
                @php
                    $steps   = ['pending','confirmed','processing','shipped','delivered'];
                    $current = array_search($order->status, $steps);
                    $cancelled = $order->status === 'cancelled';
                @endphp
                @if($cancelled)
                    <div class="text-center text-danger fw-semibold py-2">
                        <i class="bi bi-x-circle-fill me-2"></i> This order was cancelled.
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-between position-relative" id="order-progress">
                        <div class="progress position-absolute w-100" style="height:3px; top:18px; z-index:0; left:0; right:0;">
                            <div class="progress-bar bg-warning"
                                 style="width: {{ $current !== false ? ($current / (count($steps) - 1)) * 100 : 0 }}%"></div>
                        </div>
                        @foreach($steps as $i => $step)
                            @php $done = ($current !== false && $i <= $current); @endphp
                            <div class="text-center position-relative" style="z-index:1; flex:1;">
                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center border-2 fw-bold"
                                     style="width:36px; height:36px; font-size:0.8rem;
                                            background: {{ $done ? '#F5B041' : '#fff' }};
                                            border: 2px solid {{ $done ? '#F5B041' : '#dee2e6' }};
                                            color: {{ $done ? '#1A0B2E' : '#adb5bd' }};">
                                    @if($done && $i < $current)
                                        <i class="bi bi-check-lg"></i>
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>
                                <div class="small mt-1 {{ $done ? 'fw-semibold text-dark' : 'text-muted' }}">
                                    {{ ucfirst($step) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left: Items + Tracking --}}
        <div class="col-lg-8">

            {{-- Order Items --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom fw-semibold py-3">
                    <i class="bi bi-box-seam me-2 text-warning"></i> Ordered Items
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end pe-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <div class="d-flex align-items-center gap-3">
                                                @if($item->product && $item->product->image)
                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                         alt="{{ $item->product_name }}"
                                                         class="rounded-2 object-fit-cover"
                                                         style="width:52px; height:52px;">
                                                @else
                                                    <div class="rounded-2 bg-light text-muted d-flex align-items-center justify-content-center"
                                                         style="width:52px; height:52px; font-size:1.5rem;">🔮</div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $item->product_name }}</div>
                                                    @if($item->product_sku)
                                                        <small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="text-end pe-3 fw-semibold">₹{{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tracking Info --}}
            @if($order->tracking_number)
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white border-bottom fw-semibold py-3">
                        <i class="bi bi-truck me-2 text-warning"></i> Shipment Tracking
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <small class="text-muted text-uppercase fw-semibold d-block mb-1">Carrier</small>
                                <span class="fw-semibold">{{ $order->carrier_name ?? 'N/A' }}</span>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted text-uppercase fw-semibold d-block mb-1">Tracking Number</small>
                                <span class="fw-semibold text-primary">{{ $order->tracking_number }}</span>
                            </div>
                            @if($order->estimated_delivery)
                                <div class="col-sm-6">
                                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Estimated Delivery</small>
                                    <span class="fw-semibold">{{ $order->estimated_delivery->format('d M Y') }}</span>
                                </div>
                            @endif
                            @if($order->delivered_at)
                                <div class="col-sm-6">
                                    <small class="text-muted text-uppercase fw-semibold d-block mb-1">Delivered On</small>
                                    <span class="fw-semibold text-success">{{ $order->delivered_at->format('d M Y, h:i A') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Summary + Shipping --}}
        <div class="col-lg-4">

            {{-- Order Summary --}}
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-white border-bottom fw-semibold py-3">
                    <i class="bi bi-receipt me-2 text-warning"></i> Order Summary
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Coupon ({{ $order->coupon_code }})</span>
                            <span>−₹{{ number_format($order->coupon_discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between mb-2 text-muted">
                        <span>Shipping</span>
                        <span>{{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'Free' }}</span>
                    </div>
                    @if($order->tax_amount > 0)
                        <div class="d-flex justify-content-between mb-2 text-muted">
                            <span>Tax</span>
                            <span>₹{{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                    @endif
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total Paid</span>
                        <span class="text-dark">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="mt-2 text-muted small">
                        <i class="bi bi-credit-card me-1"></i>
                        Payment: {{ ucwords(str_replace('_', ' ', $order->payment_method)) }}
                    </div>
                </div>
            </div>

            {{-- Shipping Address --}}
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom fw-semibold py-3">
                    <i class="bi bi-geo-alt me-2 text-warning"></i> Shipping Address
                </div>
                <div class="card-body">
                    <address class="mb-0">
                        <strong>{{ $order->shipping_name }}</strong><br>
                        {{ $order->shipping_address_line1 }}<br>
                        @if($order->shipping_address_line2)
                            {{ $order->shipping_address_line2 }}<br>
                        @endif
                        {{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_pincode }}<br>
                        {{ $order->shipping_country }}<br>
                        <i class="bi bi-telephone me-1 mt-2 d-inline-block"></i>{{ $order->shipping_phone }}
                    </address>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
