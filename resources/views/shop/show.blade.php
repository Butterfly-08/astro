@extends('layouts.app')

@section('title', $product->name . ' — Vedic Energized Spiritual Product | AstroVaani')
@section('meta_description', Str::limit($product->short_description ?? strip_tags($product->description), 160))

@push('styles')
<style>
    .product-detail-hero {
        background: #F9FAFB;
        border-bottom: 1px solid #E5E7EB;
        padding: 30px 0;
    }
    .product-main-img-box {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        height: 440px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    .product-main-img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    .spiritual-badge {
        background: linear-gradient(135deg, rgba(245, 176, 65, 0.15), rgba(108, 52, 131, 0.12));
        border: 1px solid rgba(245, 176, 65, 0.4);
        color: #78350F;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 6px 14px;
        border-radius: 30px;
    }
    .nav-tabs .nav-link {
        color: #4B5563;
        font-weight: 600;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 12px 20px;
    }
    .nav-tabs .nav-link.active {
        color: #6C3483;
        border-bottom: 3px solid #6C3483;
        background: transparent;
    }
    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #F3F4F6;
        font-size: 0.92rem;
    }
    .consult-nudge-box {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 100%);
        border-radius: 16px;
        color: #fff;
        padding: 24px;
    }
</style>
@endpush

@section('content')

{{-- Breadcrumb Navigation --}}
<div class="product-detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.index') }}" class="text-decoration-none text-muted">Spiritual Shop</a></li>
                @if($product->category)
                    <li class="breadcrumb-item">
                        <a href="{{ route('shop.category', $product->category->slug) }}" class="text-decoration-none text-muted">
                            {{ $product->category->name }}
                        </a>
                    </li>
                @endif
                <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ Str::limit($product->name, 35) }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5">
        {{-- Product Image & Gallery --}}
        <div class="col-lg-5">
            <div class="position-sticky" style="top: 100px;">
                <div class="product-main-img-box shadow-sm mb-3 position-relative">
                    @if($product->is_featured)
                        <span class="badge bg-warning text-dark fw-bold position-absolute top-0 start-0 m-3 px-3 py-2 shadow-sm">
                            <i class="bi bi-star-fill me-1"></i>BESTSELLER
                        </span>
                    @endif
                    @if($product->is_on_sale)
                        <span class="badge bg-danger text-white fw-bold position-absolute top-0 end-0 m-3 px-3 py-2 shadow-sm">
                            SAVE {{ $product->discount_percentage }}%
                        </span>
                    @endif

                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-main-img img-fluid p-4">
                    @else
                        <div class="text-center p-5 text-warning">
                            <i class="bi bi-gem" style="font-size: 6rem;"></i>
                            <div class="text-muted small mt-2 fw-semibold">Sacred Vedic Item</div>
                        </div>
                    @endif
                </div>

                {{-- Guarantee badges --}}
                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border small">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-patch-check-fill text-warning fs-5"></i>
                        <span>Lab Certified & 100% Genuine</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill text-success fs-5"></i>
                        <span>Gotra Energized</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Product Info & Purchase Options --}}
        <div class="col-lg-7">
            {{-- Category & SKU --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                @if($product->category)
                    <a href="{{ route('shop.category', $product->category->slug) }}" class="spiritual-badge text-decoration-none">
                        <i class="bi bi-tag-fill me-1"></i> {{ $product->category->name }}
                    </a>
                @endif
                <span class="text-muted small">SKU: <strong class="text-dark">{{ $product->sku }}</strong></span>
            </div>

            {{-- Title --}}
            <h1 class="display-6 fw-bold text-dark mb-3" style="font-family: 'Outfit', sans-serif;">
                {{ $product->name }}
            </h1>

            {{-- Rating & Reviews --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="d-flex align-items-center text-warning" style="font-size: 1.1rem;">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($product->rating_avg))
                            <i class="bi bi-star-fill"></i>
                        @elseif($i - 0.5 <= $product->rating_avg)
                            <i class="bi bi-star-half"></i>
                        @else
                            <i class="bi bi-star"></i>
                        @endif
                    @endfor
                </div>
                <span class="fw-bold text-dark">{{ number_format($product->rating_avg, 1) }}</span>
                <span class="text-muted">|</span>
                <span class="text-muted small"><i class="bi bi-chat-heart me-1 text-danger"></i>{{ $product->total_reviews }} devotee reviews</span>
                <span class="text-muted">|</span>
                <span class="badge bg-{{ $product->stock_badge['class'] }}">{{ $product->stock_badge['label'] }}</span>
            </div>

            {{-- Pricing --}}
            <div class="p-3 bg-light rounded-3 border mb-4">
                <div class="d-flex align-items-baseline gap-3">
                    <span class="display-5 fw-bold text-dark" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                        ₹{{ number_format($product->effective_price, 2) }}
                    </span>
                    @if($product->is_on_sale)
                        <span class="fs-4 text-decoration-line-through text-muted">
                            ₹{{ number_format($product->price, 2) }}
                        </span>
                        <span class="badge bg-success py-2 px-3 fw-bold">
                            Save ₹{{ number_format($product->price - $product->effective_price, 2) }} ({{ $product->discount_percentage }}% OFF)
                        </span>
                    @endif
                </div>
                <div class="text-muted small mt-1">
                    <i class="bi bi-info-circle me-1"></i>Inclusive of all taxes & Vedic Prana Pratishtha consecration ceremonies.
                </div>
            </div>

            {{-- Short description --}}
            @if($product->short_description)
                <p class="lead text-secondary mb-4" style="font-size: 1rem; line-height: 1.6;">
                    {{ $product->short_description }}
                </p>
            @endif

            {{-- Purchase Action Box --}}
            <div class="card border-2 border-warning bg-white shadow-sm p-4 mb-4">
                @if($product->in_stock)
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-auto">
                            <label for="quantityInput" class="fw-bold text-dark small">Quantity:</label>
                        </div>
                        <div class="col-auto">
                            <div class="input-group" style="width: 130px;">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="decrementQty()"><i class="bi bi-dash"></i></button>
                                <input type="number" id="quantityInput" class="form-control form-control-sm text-center fw-bold" value="1" min="1" max="{{ $product->stock }}">
                                <button class="btn btn-outline-secondary btn-sm" type="button" onclick="incrementQty()"><i class="bi bi-plus"></i></button>
                            </div>
                        </div>
                        <div class="col">
                            <span class="text-muted small">Max {{ $product->stock }} units available in sanctified vault</span>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <button type="button" class="btn btn-warning btn-lg fw-bold flex-grow-1 shadow-sm d-flex align-items-center justify-content-center gap-2" onclick="alert('Order and cart integration is being processed! Product: {{ addslashes($product->name) }}')">
                            <i class="bi bi-lightning-charge-fill"></i> Buy Now
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-lg fw-bold flex-grow-1 d-flex align-items-center justify-content-center gap-2" onclick="alert('Item added to your spiritual cart!')">
                            <i class="bi bi-bag-plus"></i> Add to Cart
                        </button>
                    </div>
                @else
                    <div class="alert alert-danger mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div>
                            <strong>Currently Out of Stock.</strong>
                            <div class="small">Our Vedic scholars are consecrating the next batch. Please check back shortly or consult an astrologer for alternative remedies.</div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Astrologer Cross-Sell Nudge --}}
            <div class="consult-nudge-box mb-4">
                <div class="d-flex align-items-start gap-3">
                    <div class="bg-warning text-dark p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                        <i class="bi bi-chat-quote-fill fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-warning mb-1">Unsure if this remedy aligns with your Kundli?</h6>
                        <p class="small text-white-50 mb-3">
                            Wearing the wrong gemstone or rudraksha can produce adverse effects. Consult our certified Vedic Astrologers to verify suitability based on your birth chart.
                        </p>
                        <a href="{{ route('astrologers.index') }}" class="btn btn-sm btn-outline-warning fw-bold">
                            <i class="bi bi-person-check-fill me-1"></i> Consult an Astrologer
                        </a>
                    </div>
                </div>
            </div>

            {{-- Delivery & Service Points --}}
            <div class="row g-3 small text-muted">
                <div class="col-sm-6 d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam text-primary fs-5"></i>
                    <span>Free shipping on all prepaid sacred orders</span>
                </div>
                <div class="col-sm-6 d-flex align-items-center gap-2">
                    <i class="bi bi-arrow-repeat text-success fs-5"></i>
                    <span>7-Day Return & Replacement guarantee</span>
                </div>
                <div class="col-sm-6 d-flex align-items-center gap-2">
                    <i class="bi bi-award text-warning fs-5"></i>
                    <span>Includes Certificate of Authenticity</span>
                </div>
                <div class="col-sm-6 d-flex align-items-center gap-2">
                    <i class="bi bi-shield-check text-info fs-5"></i>
                    <span>Secure 256-Bit Encrypted Payments</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs: Detailed Description, Vedic Significance, Specifications --}}
    <div class="mt-5 pt-4 border-top">
        <ul class="nav nav-tabs mb-4" id="productTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc-pane" type="button" role="tab">
                    <i class="bi bi-file-text me-1"></i> Detailed Description
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="spiritual-tab" data-bs-toggle="tab" data-bs-target="#spiritual-pane" type="button" role="tab">
                    <i class="bi bi-stars me-1"></i> Vedic & Spiritual Significance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="specs-tab" data-bs-toggle="tab" data-bs-target="#specs-pane" type="button" role="tab">
                    <i class="bi bi-card-checklist me-1"></i> Item Specifications
                </button>
            </li>
        </ul>

        <div class="tab-content bg-white p-4 rounded-3 border" id="productTabContent">
            {{-- Description --}}
            <div class="tab-pane fade show active" id="desc-pane" role="tabpanel">
                <h5 class="fw-bold text-dark mb-3">About this Sacred Product</h5>
                <div class="text-secondary" style="line-height: 1.8;">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

            {{-- Spiritual Significance --}}
            <div class="tab-pane fade" id="spiritual-pane" role="tabpanel">
                <h5 class="fw-bold text-dark mb-3">Astrological Benefits & Consecration Protocol</h5>
                <p class="text-secondary mb-4">
                    Every spiritual item on AstroVaani undergoes rigorous Vedic purification rituals known as <em>Prana Pratishtha</em>. Our senior temple priests consecrate the sacred item using ancient mantras before dispatch.
                </p>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-brightness-high text-warning me-2"></i>How to Wear / Install</h6>
                            <ul class="text-secondary small mb-0 ps-3">
                                <li>Bathe early morning on the designated auspicious day.</li>
                                <li>Purify the item with Gangajal and raw unpasteurized milk.</li>
                                <li>Light incense (Dhoop) and a pure ghee Diya.</li>
                                <li>Chant the prescribed Vedic beeja mantra 108 times using a Japa Mala.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-gem text-primary me-2"></i>Consecration Certificate</h6>
                            <p class="text-secondary small mb-0">
                                This product comes along with an authenticated certificate of energization containing the date of ritual, priest name, and testing lab certification for mineral authenticity.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Specifications --}}
            <div class="tab-pane fade" id="specs-pane" role="tabpanel">
                <h5 class="fw-bold text-dark mb-3">Product Specifications</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="spec-item">
                            <span class="text-muted">Product SKU</span>
                            <span class="fw-semibold text-dark">{{ $product->sku }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="text-muted">Category</span>
                            <span class="fw-semibold text-dark">{{ $product->category->name ?? 'Sacred Item' }}</span>
                        </div>
                        <div class="spec-item">
                            <span class="text-muted">Availability</span>
                            <span class="fw-semibold text-dark">{{ $product->in_stock ? 'In Stock (' . $product->stock . ' units)' : 'Out of Stock' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="spec-item">
                            <span class="text-muted">Energization Status</span>
                            <span class="fw-semibold text-success"><i class="bi bi-check-circle-fill me-1"></i>100% Vedic Consecrated</span>
                        </div>
                        <div class="spec-item">
                            <span class="text-muted">Authenticity Guarantee</span>
                            <span class="fw-semibold text-dark">Certified Original</span>
                        </div>
                        <div class="spec-item">
                            <span class="text-muted">Origin / Craftsmanship</span>
                            <span class="fw-semibold text-dark">Himalayas / Traditional Indian Artisans</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Related Products --}}
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-5 pt-5 border-top">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">Related Sacred Offerings</h3>
                    <p class="text-muted small mb-0">Complements your astrological remedy</p>
                </div>
                <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm fw-semibold">
                    View All in Shop <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">
                @foreach($relatedProducts as $relProduct)
                    <div class="col">
                        @include('components.product-card', ['product' => $relProduct])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function incrementQty() {
        const input = document.getElementById('quantityInput');
        const max = parseInt(input.getAttribute('max')) || 99;
        let val = parseInt(input.value) || 1;
        if (val < max) {
            input.value = val + 1;
        }
    }

    function decrementQty() {
        const input = document.getElementById('quantityInput');
        let val = parseInt(input.value) || 1;
        if (val > 1) {
            input.value = val - 1;
        }
    }
</script>
@endpush

@endsection
