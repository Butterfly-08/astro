@extends('layouts.app')

@section('title', 'Secure Checkout — AstroVaani')
@section('meta_description', 'Complete your sacred order by entering your shipping address and choosing your payment method.')

@push('styles')
<style>
    .checkout-hero {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%);
        padding: 40px 0 30px;
        color: #fff;
        border-bottom: 3px solid #F5B041;
    }
    .form-section { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; padding: 24px; margin-bottom: 20px; }
    .form-section-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6; }
    .order-summary-card { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; position: sticky; top: 90px; }
    .payment-method-card { border: 2px solid #E5E7EB; border-radius: 12px; padding: 14px 16px; cursor: pointer; transition: all 0.2s; }
    .payment-method-card:has(input:checked) { border-color: #F5B041; background: rgba(245, 176, 65, 0.05); }
    .summary-item-thumb { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #F9FAFB; border: 1px solid #E5E7EB; }
    .summary-item-thumb-ph { width: 48px; height: 48px; border-radius: 8px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #F5B041; border: 1px solid #E5E7EB; }
</style>
@endpush

@section('content')

<div class="checkout-hero">
    <div class="container">
        <h1 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Secure Checkout</h1>
        <p class="text-white-50 mb-0">Complete your sacred order in a few simple steps.</p>
        <div class="d-flex align-items-center gap-2 mt-3 small text-white-50">
            <i class="bi bi-shield-lock-fill text-success"></i> 256-bit SSL Encrypted &bull; Your information is safe.
        </div>
    </div>
</div>

<div class="container py-5">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('checkout.place-order') }}" method="POST" novalidate>
        @csrf
        <div class="row g-4">
            {{-- Left: Shipping + Payment --}}
            <div class="col-lg-7">

                {{-- Shipping Address --}}
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-house-fill me-2"></i>Shipping Address</div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_name" class="form-control @error('shipping_name') is-invalid @enderror"
                                value="{{ old('shipping_name', $user->first_name . ' ' . $user->last_name) }}" required>
                            @error('shipping_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Mobile Number <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_phone" class="form-control @error('shipping_phone') is-invalid @enderror"
                                value="{{ old('shipping_phone', $user->phone) }}" required placeholder="+91 9XXXXXXXXX">
                            @error('shipping_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Address Line 1 <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_address_line1" class="form-control @error('shipping_address_line1') is-invalid @enderror"
                                value="{{ old('shipping_address_line1') }}" required placeholder="Flat/House No., Building, Street name">
                            @error('shipping_address_line1')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold small">Address Line 2 <span class="text-muted">(Landmark, Area)</span></label>
                            <input type="text" name="shipping_address_line2" class="form-control"
                                value="{{ old('shipping_address_line2') }}" placeholder="Apartment, floor, area, landmark">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">City <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_city" class="form-control @error('shipping_city') is-invalid @enderror"
                                value="{{ old('shipping_city', $user->city) }}" required>
                            @error('shipping_city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">State <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_state" class="form-control @error('shipping_state') is-invalid @enderror"
                                value="{{ old('shipping_state', $user->state) }}" required>
                            @error('shipping_state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold small">PIN Code <span class="text-danger">*</span></label>
                            <input type="text" name="shipping_pincode" class="form-control @error('shipping_pincode') is-invalid @enderror"
                                value="{{ old('shipping_pincode') }}" required maxlength="6" placeholder="6-digit PIN">
                            @error('shipping_pincode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="form-section">
                    <div class="form-section-title"><i class="bi bi-credit-card-fill me-2"></i>Payment Method</div>
                    <div class="d-flex flex-column gap-3">
                        <label class="payment-method-card d-flex align-items-center gap-3">
                            <input type="radio" name="payment_method" value="cod" class="form-check-input" @checked(old('payment_method','cod')==='cod')>
                            <div>
                                <div class="fw-bold text-dark"><i class="bi bi-cash-stack text-success me-2"></i>Cash on Delivery (COD)</div>
                                <div class="text-muted small">Pay in cash when your sacred item arrives at your doorstep. Extra ₹20 COD fee may apply.</div>
                            </div>
                        </label>

                        <label class="payment-method-card d-flex align-items-center gap-3">
                            <input type="radio" name="payment_method" value="upi" class="form-check-input" @checked(old('payment_method')==='upi')>
                            <div>
                                <div class="fw-bold text-dark"><i class="bi bi-phone-fill text-purple me-2" style="color:#6C3483;"></i>UPI / PhonePe / GPay / Paytm</div>
                                <div class="text-muted small">Instant payment via any UPI app. Most popular in India. Zero fees.</div>
                            </div>
                        </label>

                        <label class="payment-method-card d-flex align-items-center gap-3">
                            <input type="radio" name="payment_method" value="online" class="form-check-input" @checked(old('payment_method')==='online')>
                            <div>
                                <div class="fw-bold text-dark"><i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Credit / Debit Card / Net Banking</div>
                                <div class="text-muted small">Secure online payment via Razorpay. Accepted: Visa, Mastercard, RuPay, all major banks.</div>
                            </div>
                        </label>
                    </div>
                    @error('payment_method')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Right: Order Summary --}}
            <div class="col-lg-5">
                <div class="order-summary-card p-4 shadow-sm">
                    <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom">
                        <i class="bi bi-bag-check me-2 text-warning"></i>Order Summary
                    </h5>

                    {{-- Items list --}}
                    <div class="d-flex flex-column gap-3 mb-4">
                        @foreach($items as $item)
                            <div class="d-flex align-items-center gap-3">
                                @if($item->product?->image)
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product?->name }}" class="summary-item-thumb flex-shrink-0">
                                @else
                                    <div class="summary-item-thumb-ph flex-shrink-0"><i class="bi bi-gem"></i></div>
                                @endif
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold text-dark" style="font-size: 0.85rem; line-height: 1.3;">{{ Str::limit($item->product?->name ?? 'Product', 40) }}</div>
                                    <div class="text-muted" style="font-size: 0.78rem;">Qty: {{ $item->quantity }}</div>
                                </div>
                                <div class="fw-bold text-dark text-end" style="font-size: 0.9rem; white-space: nowrap;">
                                    ₹{{ number_format($item->line_total, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">₹{{ number_format($summary['subtotal'], 2) }}</span>
                        </div>
                        @if($summary['discount'] > 0)
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-success">Coupon Discount ({{ $summary['coupon']['code'] }})</span>
                                <span class="fw-bold text-success">− ₹{{ number_format($summary['discount'], 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between mb-3 small">
                            <span class="text-muted">Shipping</span>
                            <span class="fw-semibold {{ $summary['shipping'] == 0 ? 'text-success' : '' }}">
                                {{ $summary['shipping'] > 0 ? '₹' . number_format($summary['shipping'], 2) : 'Free' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-3 mb-4">
                            <span class="fw-bold text-dark fs-5">Total Payable</span>
                            <span class="fw-bold text-dark fs-5">₹{{ number_format($summary['total'], 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold shadow d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-bag-check-fill"></i> Place Order Now
                    </button>

                    <div class="text-center mt-3 text-muted small d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-shield-lock-fill text-success"></i>
                        SSL encrypted &bull; 100% Safe & Secure
                    </div>

                    <a href="{{ route('cart.index') }}" class="btn btn-link w-100 text-muted text-decoration-none mt-2 small">
                        <i class="bi bi-arrow-left me-1"></i> Back to Cart
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection
