@extends('layouts.app')

@section('title', 'Order Confirmed #' . $order->order_number . ' — AstroVaani')

@push('styles')
<style>
    .confirmation-hero {
        background: linear-gradient(135deg, #065F46 0%, #059669 60%, #10B981 100%);
        padding: 50px 0 40px;
        color: #fff;
        text-align: center;
    }
    .confetti-icon {
        font-size: 4rem;
        animation: bounce 1s ease infinite alternate;
    }
    @keyframes bounce { from { transform: translateY(-8px); } to { transform: translateY(8px); } }
    .order-card { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; border-radius: 30px; font-size: 0.85rem; font-weight: 700; }
    .timeline-step { display: flex; align-items: center; gap: 14px; padding: 12px 0; }
    .timeline-dot { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .timeline-dot.done { background: rgba(5, 150, 105, 0.12); color: #059669; }
    .timeline-dot.pending { background: #F3F4F6; color: #9CA3AF; }
    .item-thumb { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; border: 1px solid #E5E7EB; }
    .item-thumb-ph { width: 56px; height: 56px; border-radius: 10px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #F5B041; font-size: 1.5rem; border: 1px solid #E5E7EB; }
</style>
@endpush

@section('content')

{{-- Success Hero --}}
<div class="confirmation-hero">
    <div class="container py-2">
        <div class="confetti-icon mb-3">🎉</div>
        <h1 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif;">Order Placed Successfully!</h1>
        <p class="text-white-75 mb-3 lead">
            Your sacred items have been ordered and will be dispatched within 24–48 hours.
        </p>
        <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill bg-white text-dark fw-bold shadow-sm">
            <i class="bi bi-bag-check-fill text-success"></i>
            Order #{{ $order->order_number }}
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        {{-- Order Details --}}
        <div class="col-lg-7">
            {{-- Order Items --}}
            <div class="order-card p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                    <i class="bi bi-box2-heart me-2 text-warning"></i>Sacred Items Ordered
                </h5>

                <div class="d-flex flex-column gap-3">
                    @foreach($order->items as $item)
                        <div class="d-flex align-items-center gap-3">
                            @if($item->product?->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product_name }}" class="item-thumb flex-shrink-0">
                            @else
                                <div class="item-thumb-ph flex-shrink-0"><i class="bi bi-gem"></i></div>
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-bold text-dark" style="line-height: 1.3;">{{ $item->product_name }}</div>
                                <div class="text-muted small">SKU: {{ $item->product_sku }} &bull; Qty: {{ $item->quantity }}</div>
                                @if($item->is_discounted)
                                    <div class="text-muted small"><span class="text-decoration-line-through me-1">₹{{ number_format($item->original_price, 2) }}</span></div>
                                @endif
                            </div>
                            <div class="text-end fw-bold text-dark">₹{{ number_format($item->subtotal, 2) }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="border-top mt-3 pt-3">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span>₹{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                        <div class="d-flex justify-content-between small mb-1 text-success">
                            <span>Coupon ({{ $order->coupon_code }})</span>
                            <span>− ₹{{ number_format($order->coupon_discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Shipping</span>
                        <span>{{ $order->shipping_charge > 0 ? '₹' . number_format($order->shipping_charge, 2) : 'Free' }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold fs-5 border-top pt-2">
                        <span class="text-dark">Total Paid</span>
                        <span class="text-dark">₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Delivery Timeline --}}
            <div class="order-card p-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">
                    <i class="bi bi-truck me-2 text-primary"></i>Order Status Timeline
                </h5>
                @php
                    $steps = [
                        ['icon' => 'bi-check-circle-fill', 'label' => 'Order Placed', 'sub' => 'Your order has been received.', 'done' => true],
                        ['icon' => 'bi-patch-check-fill', 'label' => 'Order Confirmed', 'sub' => 'Our team is verifying availability.', 'done' => in_array($order->status, ['confirmed','processing','shipped','delivered'])],
                        ['icon' => 'bi-fire', 'label' => 'Vedic Energization', 'sub' => 'Your item is being consecrated by our Vedic priests.', 'done' => in_array($order->status, ['processing','shipped','delivered'])],
                        ['icon' => 'bi-truck', 'label' => 'Shipped', 'sub' => 'Item dispatched & handed to courier.', 'done' => in_array($order->status, ['shipped','delivered'])],
                        ['icon' => 'bi-house-heart-fill', 'label' => 'Delivered', 'sub' => 'Sacred item received at your doorstep.', 'done' => $order->status === 'delivered'],
                    ];
                @endphp
                @foreach($steps as $step)
                    <div class="timeline-step border-bottom border-light">
                        <div class="timeline-dot {{ $step['done'] ? 'done' : 'pending' }}">
                            <i class="bi {{ $step['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="fw-semibold text-dark {{ !$step['done'] ? 'text-muted' : '' }}" style="font-size: 0.9rem;">{{ $step['label'] }}</div>
                            <div class="text-muted" style="font-size: 0.8rem;">{{ $step['sub'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="col-lg-5">
            {{-- Order Meta --}}
            <div class="order-card p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-info-circle me-2"></i>Order Information
                </h5>
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Order Number</span>
                        <span class="fw-bold text-dark">#{{ $order->order_number }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Order Date</span>
                        <span>{{ $order->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Status</span>
                        <span class="badge bg-{{ $order->status_badge['class'] }}">{{ $order->status_badge['label'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Payment</span>
                        <span class="fw-semibold">{{ strtoupper($order->payment_method) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Payment Status</span>
                        <span class="badge bg-{{ $order->payment_status_badge['class'] }}">{{ $order->payment_status_badge['label'] }}</span>
                    </div>
                </div>
            </div>

            {{-- Shipping Info --}}
            <div class="order-card p-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                    <i class="bi bi-house-fill me-2"></i>Shipping To
                </h5>
                <address class="mb-0 small text-secondary" style="line-height: 1.7;">
                    <strong class="text-dark">{{ $order->shipping_name }}</strong><br>
                    {{ $order->shipping_address_line1 }}<br>
                    @if($order->shipping_address_line2){{ $order->shipping_address_line2 }}<br>@endif
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_pincode }}<br>
                    {{ $order->shipping_country }}<br>
                    <i class="bi bi-telephone-fill me-1"></i>{{ $order->shipping_phone }}
                </address>
            </div>

            {{-- CTA Buttons --}}
            <div class="d-grid gap-2">
                <a href="{{ route('user.orders.show', $order) }}" class="btn btn-outline-primary fw-semibold">
                    <i class="bi bi-eye me-1"></i> Track My Order
                </a>
                <a href="{{ route('shop.index') }}" class="btn btn-warning fw-bold">
                    <i class="bi bi-shop me-1"></i> Continue Shopping
                </a>
                <a href="{{ route('astrologers.index') }}" class="btn btn-outline-secondary fw-semibold">
                    <i class="bi bi-chat-dots me-1"></i> Consult an Astrologer
                </a>
            </div>

            {{-- Spiritual reminder --}}
            <div class="mt-4 p-3 rounded-3 text-center" style="background: rgba(245, 176, 65, 0.08); border: 1px solid rgba(245, 176, 65, 0.3);">
                <i class="bi bi-stars text-warning d-block mb-2 fs-4"></i>
                <p class="small text-muted mb-0">
                    An energization certificate will be included in your package. Our Vedic priests will chant the prescribed mantras before dispatch. <strong>Om Shanti 🙏</strong>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
