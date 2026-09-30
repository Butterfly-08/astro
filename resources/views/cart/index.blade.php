@extends('layouts.app')

@section('title', 'Your Spiritual Cart — AstroVaani')
@section('meta_description', 'Review your selected sacred items, apply a coupon code, and proceed to checkout.')

@push('styles')
<style>
    .cart-hero {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%);
        padding: 45px 0 35px;
        color: #fff;
        border-bottom: 3px solid #F5B041;
    }
    .cart-table-card { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; overflow: hidden; }
    .cart-item-img { width: 80px; height: 80px; border-radius: 10px; object-fit: cover; background: #F9FAFB; border: 1px solid #E5E7EB; }
    .cart-item-img-placeholder { width: 80px; height: 80px; border-radius: 10px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; font-size: 2rem; color: #F5B041; border: 1px solid #E5E7EB; }
    .qty-control { display: flex; align-items: center; gap: 0; border: 1.5px solid #E5E7EB; border-radius: 8px; overflow: hidden; }
    .qty-control button { width: 32px; height: 36px; border: none; background: #F9FAFB; color: #374151; font-weight: 700; transition: background 0.2s; }
    .qty-control button:hover { background: #E5E7EB; }
    .qty-control input { width: 48px; height: 36px; border: none; border-left: 1.5px solid #E5E7EB; border-right: 1.5px solid #E5E7EB; text-align: center; font-weight: 700; outline: none; }
    .order-summary-card { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; position: sticky; top: 90px; }
    .trust-pill { background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 30px; padding: 6px 14px; font-size: 0.82rem; color: #065F46; display: flex; align-items: center; gap: 6px; }
</style>
@endpush

@section('content')

<div class="cart-hero">
    <div class="container">
        <h1 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Your Sacred Cart</h1>
        <p class="text-white-50 mb-0">Review your astrological remedies before proceeding to checkout.</p>
    </div>
</div>

<div class="container py-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($items->isEmpty())
        {{-- Empty Cart State --}}
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-cart-x" style="font-size: 5rem; color: #D1D5DB;"></i>
            </div>
            <h3 class="fw-bold text-dark mb-2">Your Vedic Cart is Empty</h3>
            <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">
                You have not added any sacred items yet. Explore our collection of certified gemstones, Rudraksha beads, sanctified yantras, and puja essentials.
            </p>
            <a href="{{ route('shop.index') }}" class="btn btn-warning btn-lg px-5 fw-bold shadow-sm">
                <i class="bi bi-shop me-2"></i> Browse Spiritual Shop
            </a>
        </div>
    @else
        <div class="row g-4">
            {{-- Cart Items Table --}}
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark mb-0">Cart Items ({{ $items->count() }})</h5>
                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                    </a>
                </div>

                <div class="cart-table-card shadow-sm">
                    @foreach($items as $item)
                        <div class="d-flex align-items-center gap-3 p-3 border-bottom" id="cart-row-{{ $item->id }}">
                            {{-- Product Image --}}
                            @if($item->product?->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="cart-item-img flex-shrink-0">
                            @else
                                <div class="cart-item-img-placeholder flex-shrink-0">
                                    <i class="bi bi-gem"></i>
                                </div>
                            @endif

                            {{-- Product Info --}}
                            <div class="flex-grow-1 min-w-0">
                                <a href="{{ route('shop.product.show', $item->product?->slug ?? '#') }}" class="fw-bold text-dark text-decoration-none small d-block mb-1" style="line-height: 1.3;">
                                    {{ $item->product?->name ?? 'Product Removed' }}
                                </a>
                                @if($item->product?->category)
                                    <div class="text-muted" style="font-size: 0.78rem;">{{ $item->product->category->name }}</div>
                                @endif
                                <div class="text-muted" style="font-size: 0.78rem;">SKU: {{ $item->product?->sku ?? 'N/A' }}</div>

                                {{-- Mobile price --}}
                                <div class="d-sm-none mt-1">
                                    <span class="fw-bold text-dark small">₹{{ number_format($item->line_total, 2) }}</span>
                                </div>
                            </div>

                            {{-- Unit Price --}}
                            <div class="d-none d-sm-block text-end" style="min-width: 80px;">
                                <div class="fw-semibold text-dark small">₹{{ number_format($item->product?->effective_price ?? 0, 2) }}</div>
                                @if($item->product?->is_on_sale)
                                    <div class="text-decoration-line-through text-muted" style="font-size: 0.75rem;">
                                        ₹{{ number_format($item->product->price, 2) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Quantity Control --}}
                            <div class="qty-control flex-shrink-0">
                                <button type="button" class="qty-btn" data-cart-id="{{ $item->id }}" data-action="decrement" title="Decrease">−</button>
                                <input type="number" id="qty-{{ $item->id }}" class="qty-input" value="{{ $item->quantity }}" min="1" max="{{ $item->product?->stock ?? 99 }}" data-cart-id="{{ $item->id }}" readonly>
                                <button type="button" class="qty-btn" data-cart-id="{{ $item->id }}" data-action="increment" title="Increase">+</button>
                            </div>

                            {{-- Line Total --}}
                            <div class="text-end d-none d-sm-block fw-bold text-dark" id="line-total-{{ $item->id }}" style="min-width: 90px;">
                                ₹{{ number_format($item->line_total, 2) }}
                            </div>

                            {{-- Remove --}}
                            <button type="button" class="btn btn-sm btn-link text-danger p-1 remove-item-btn flex-shrink-0" data-cart-id="{{ $item->id }}" title="Remove from cart">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    @endforeach

                    {{-- Footer row --}}
                    <div class="d-flex align-items-center justify-content-between px-3 py-2 bg-light">
                        <div class="d-flex gap-2 flex-wrap">
                            <div class="trust-pill"><i class="bi bi-patch-check-fill text-success"></i> Vedic Energized Products</div>
                            <div class="trust-pill"><i class="bi bi-truck text-primary"></i> Fast & Insured Delivery</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-4">
                <div class="order-summary-card p-4 shadow-sm">
                    <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom">Order Summary</h5>

                    {{-- Coupon Form --}}
                    @if(!$summary['coupon'])
                        <div class="mb-4">
                            <label class="form-label fw-semibold small">Coupon Code</label>
                            <div class="input-group">
                                <input type="text" id="couponCodeInput" class="form-control border-end-0" placeholder="Enter code (e.g. ASTRO20)">
                                <button type="button" id="applyCouponBtn" class="btn btn-outline-warning fw-bold px-3">Apply</button>
                            </div>
                            <div id="couponMessage" class="small mt-1 fw-semibold"></div>
                        </div>
                    @else
                        <div class="alert alert-success py-2 px-3 d-flex justify-content-between align-items-center mb-4 border-0" style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2) !important;">
                            <span class="small fw-bold text-success">
                                <i class="bi bi-tag-fill me-1"></i>
                                Coupon "{{ $summary['coupon']['code'] }}" applied
                            </span>
                            <button type="button" id="removeCouponBtn" class="btn-close btn-close-sm" title="Remove coupon"></button>
                        </div>
                    @endif

                    {{-- Price Breakdown --}}
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Subtotal</span>
                        <span id="summary-subtotal" class="fw-semibold">₹{{ number_format($summary['subtotal'], 2) }}</span>
                    </div>

                    @if($summary['discount'] > 0)
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted text-success">Coupon Discount</span>
                            <span id="summary-discount" class="fw-bold text-success">− ₹{{ number_format($summary['discount'], 2) }}</span>
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-3 small">
                        <span class="text-muted">Shipping</span>
                        <span id="summary-shipping" class="fw-semibold {{ $summary['shipping'] == 0 ? 'text-success' : '' }}">
                            {{ $summary['shipping'] > 0 ? '₹' . number_format($summary['shipping'], 2) : 'Free' }}
                        </span>
                    </div>

                    @if($summary['subtotal'] < 1000 && $summary['shipping'] > 0)
                        <div class="alert alert-warning border-0 py-2 px-3 small mb-3" style="background: rgba(251, 191, 36, 0.1);">
                            <i class="bi bi-truck me-1"></i> Add ₹{{ number_format(1000 - $summary['subtotal'], 2) }} more for <strong>Free Shipping</strong>!
                        </div>
                    @endif

                    <div class="d-flex justify-content-between border-top pt-3 mb-4">
                        <span class="fw-bold text-dark">Total Payable</span>
                        <span id="summary-grand-total" class="fw-bold fs-5 text-dark">₹{{ number_format($summary['total'], 2) }}</span>
                    </div>

                    @auth('web')
                        <a href="{{ route('checkout.index') }}" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="bi bi-bag-check-fill"></i> Proceed to Checkout
                        </a>
                    @else
                        <a href="{{ route('login') }}?redirect={{ route('checkout.index') }}" class="btn btn-warning btn-lg w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2 mb-3">
                            <i class="bi bi-person-fill-lock"></i> Login to Checkout
                        </a>
                    @endauth

                    <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary w-100 fw-semibold">
                        <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                    </a>

                    {{-- Security badges --}}
                    <div class="text-center mt-3 text-muted small">
                        <i class="bi bi-shield-lock-fill text-success me-1"></i> 100% Secure & Encrypted Checkout
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
const CSRF = '{{ csrf_token() }}';

// ---- Quantity Controls ----
document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const cartId = this.dataset.cartId;
        const input  = document.getElementById('qty-' + cartId);
        let val = parseInt(input.value);
        if (this.dataset.action === 'increment') val++;
        else val = Math.max(1, val - 1);
        input.value = val;
        updateCartItem(cartId, val);
    });
});

function updateCartItem(cartId, qty) {
    fetch(`/cart/update/${cartId}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ quantity: qty }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateSummary(data);
            updateCartCount(data.cart_count);
        }
    });
}

// ---- Remove Items ----
document.querySelectorAll('.remove-item-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const cartId = this.dataset.cartId;
        if (!confirm('Remove this item from your cart?')) return;

        fetch(`/cart/remove/${cartId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById('cart-row-' + cartId);
                if (row) row.remove();
                updateCartCount(data.cart_count);
                if (data.cart_count === 0) location.reload();
            }
        });
    });
});

// ---- Coupon ----
const applyCouponBtn = document.getElementById('applyCouponBtn');
if (applyCouponBtn) {
    applyCouponBtn.addEventListener('click', () => {
        const code = document.getElementById('couponCodeInput').value.trim();
        if (!code) return;

        fetch('/cart/coupon/apply', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ coupon_code: code }),
        })
        .then(r => r.json())
        .then(data => {
            const msgEl = document.getElementById('couponMessage');
            msgEl.textContent = data.message;
            msgEl.className = 'small mt-1 fw-semibold ' + (data.success ? 'text-success' : 'text-danger');
            if (data.success) {
                updateSummary(data);
                setTimeout(() => location.reload(), 800);
            }
        });
    });
}

const removeCouponBtn = document.getElementById('removeCouponBtn');
if (removeCouponBtn) {
    removeCouponBtn.addEventListener('click', () => {
        fetch('/cart/coupon/remove', {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        })
        .then(r => r.json())
        .then(() => location.reload());
    });
}

// ---- Helpers ----
function updateSummary(data) {
    if (data.grand_total) document.getElementById('summary-grand-total').textContent = data.grand_total;
    if (data.shipping) document.getElementById('summary-shipping').textContent = data.shipping;
    if (data.discount) {
        const el = document.getElementById('summary-discount');
        if (el) el.textContent = '− ' + data.discount;
    }
}

function updateCartCount(count) {
    document.querySelectorAll('.cart-count-badge').forEach(el => el.textContent = count);
}
</script>
@endpush

@endsection
