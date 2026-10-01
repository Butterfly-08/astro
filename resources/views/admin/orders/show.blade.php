@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 brand-font">Order #{{ $order->order_number }}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" class="text-decoration-none">Orders</a></li>
                <li class="breadcrumb-item active">{{ $order->order_number }}</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary rounded-pill">
        <i class="bi bi-arrow-left me-1"></i> Back to Orders
    </a>
</div>

{{-- Flash Messages --}}
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

<div class="row g-4">
    {{-- ====================================================================
         Left Column: Items + Timeline
    ===================================================================== --}}
    <div class="col-lg-8">

        {{-- Order Header --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4"
                 style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 100%); border-radius: 0.5rem; border-bottom: 3px solid #F5B041;">
                <div class="row align-items-center text-white">
                    <div class="col-sm-6">
                        <div class="small text-white-50 mb-1">Order Placed</div>
                        <div class="fw-semibold">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </div>
                    <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                        <span class="badge bg-{{ $order->status_badge['class'] }} px-3 py-2 me-1 fs-6">
                            {{ $order->status_badge['label'] }}
                        </span>
                        <span class="badge bg-{{ $order->payment_status_badge['class'] }} px-3 py-2 fs-6">
                            {{ $order->payment_status_badge['label'] }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ordered Items --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-bottom fw-semibold py-3">
                <i class="bi bi-box-seam me-2 text-warning"></i> Ordered Items
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Product</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Orig. Price</th>
                                <th class="text-end pe-4">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                                     alt="{{ $item->product_name }}"
                                                     class="rounded-2 object-fit-cover"
                                                     style="width:52px; height:52px;">
                                            @else
                                                <div class="rounded-2 bg-light text-muted d-flex align-items-center justify-content-center"
                                                     style="width:52px; height:52px; font-size:1.4rem;">🔮</div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold small">{{ $item->product_name }}</div>
                                                @if($item->product_sku)
                                                    <small class="text-muted font-monospace">{{ $item->product_sku }}</small>
                                                @endif
                                                @if($item->product)
                                                    <div class="small mt-1">
                                                        <span class="badge bg-{{ $item->product->in_stock ? 'success' : 'danger' }}-subtle
                                                                     text-{{ $item->product->in_stock ? 'success' : 'danger' }} border
                                                                     border-{{ $item->product->in_stock ? 'success' : 'danger' }}-subtle">
                                                            Stock now: {{ $item->product->stock }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center fw-semibold">{{ $item->quantity }}</td>
                                    <td class="text-end">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end text-muted">
                                        @if($item->original_price && $item->original_price != $item->unit_price)
                                            <del>₹{{ number_format($item->original_price, 2) }}</del>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-end pe-4 fw-bold">₹{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-semibold pe-3 py-3">Subtotal</td>
                                <td class="text-end pe-4 fw-semibold py-3">₹{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            @if($order->coupon_discount > 0)
                                <tr>
                                    <td colspan="4" class="text-end text-success pe-3 py-2">
                                        Coupon ({{ $order->coupon_code }})
                                    </td>
                                    <td class="text-end pe-4 text-success py-2">−₹{{ number_format($order->coupon_discount, 2) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end fw-semibold pe-3 py-2">Shipping</td>
                                <td class="text-end pe-4 py-2">
                                    {{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'Free' }}
                                </td>
                            </tr>
                            @if($order->tax_amount > 0)
                                <tr>
                                    <td colspan="4" class="text-end pe-3 py-2">Tax</td>
                                    <td class="text-end pe-4 py-2">₹{{ number_format($order->tax_amount, 2) }}</td>
                                </tr>
                            @endif
                            <tr class="table-warning">
                                <td colspan="4" class="text-end fw-bold pe-3 py-3 fs-5">Grand Total</td>
                                <td class="text-end pe-4 fw-bold py-3 fs-5">₹{{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Admin Notes (if any) --}}
        @if($order->admin_notes)
            <div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-warning border-3">
                <div class="card-body">
                    <h6 class="fw-semibold mb-2"><i class="bi bi-sticky me-2 text-warning"></i> Admin Notes</h6>
                    <p class="mb-0 text-muted">{{ $order->admin_notes }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- ====================================================================
         Right Column: Actions + Info
    ===================================================================== --}}
    <div class="col-lg-4">

        {{-- Update Status --}}
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white border-bottom fw-semibold py-3">
                <i class="bi bi-arrow-repeat me-2 text-warning"></i> Update Order Status
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Order Status</label>
                        <select name="status" class="form-select form-select-sm">
                            @foreach(\App\Models\Order::allStatuses() as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Tracking Number</label>
                        <input type="text" name="tracking_number" class="form-control form-control-sm"
                               placeholder="e.g. 1234567890IN"
                               value="{{ old('tracking_number', $order->tracking_number) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Carrier / Courier Name</label>
                        <input type="text" name="carrier_name" class="form-control form-control-sm"
                               placeholder="e.g. India Post, Delhivery"
                               value="{{ old('carrier_name', $order->carrier_name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Admin Notes</label>
                        <textarea name="admin_notes" rows="2" class="form-control form-control-sm"
                                  placeholder="Internal notes (not shown to customer)">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-warning fw-semibold w-100">
                        <i class="bi bi-save me-1"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- Update Payment Status --}}
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white border-bottom fw-semibold py-3">
                <i class="bi bi-credit-card me-2 text-warning"></i> Payment Status
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-payment-status', $order) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Status</label>
                        <select name="payment_status" class="form-select form-select-sm">
                            @foreach(['unpaid','paid','failed','refunded'] as $ps)
                                <option value="{{ $ps }}" {{ $order->payment_status === $ps ? 'selected' : '' }}>
                                    {{ ucfirst($ps) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Reference ID</label>
                        <input type="text" name="payment_id" class="form-control form-control-sm"
                               placeholder="Gateway transaction ID"
                               value="{{ old('payment_id', $order->payment_id) }}">
                    </div>
                    <button type="submit" class="btn btn-outline-success fw-semibold w-100">
                        <i class="bi bi-save me-1"></i> Update Payment
                    </button>
                </form>
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white border-bottom fw-semibold py-3">
                <i class="bi bi-person me-2 text-warning"></i> Customer
            </div>
            <div class="card-body small">
                @if($order->user)
                    <div class="fw-semibold mb-1">{{ $order->user->full_name }}</div>
                    <div class="text-muted mb-1">{{ $order->user->email }}</div>
                    <div class="text-muted">{{ $order->user->phone }}</div>
                @else
                    <span class="text-muted fst-italic">Customer account deleted</span>
                @endif
            </div>
        </div>

        {{-- Shipping Address --}}
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-header bg-white border-bottom fw-semibold py-3">
                <i class="bi bi-geo-alt me-2 text-warning"></i> Shipping Address
            </div>
            <div class="card-body small">
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

        {{-- Shipping / Tracking Summary --}}
        @if($order->tracking_number || $order->delivered_at)
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom fw-semibold py-3">
                    <i class="bi bi-truck me-2 text-warning"></i> Shipment
                </div>
                <div class="card-body small">
                    @if($order->carrier_name)
                        <div class="mb-2">
                            <span class="text-muted">Carrier:</span>
                            <strong class="ms-1">{{ $order->carrier_name }}</strong>
                        </div>
                    @endif
                    @if($order->tracking_number)
                        <div class="mb-2">
                            <span class="text-muted">Tracking #:</span>
                            <strong class="ms-1 font-monospace">{{ $order->tracking_number }}</strong>
                        </div>
                    @endif
                    @if($order->estimated_delivery)
                        <div class="mb-2">
                            <span class="text-muted">Est. Delivery:</span>
                            <strong class="ms-1">{{ $order->estimated_delivery->format('d M Y') }}</strong>
                        </div>
                    @endif
                    @if($order->delivered_at)
                        <div>
                            <span class="text-muted">Delivered:</span>
                            <strong class="ms-1 text-success">{{ $order->delivered_at->format('d M Y, h:i A') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
