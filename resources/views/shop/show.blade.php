@extends('layouts.app')

@section('title', $product->name . ' — Vedic Spiritual Product | AstroVaani')

@section('meta_description', Str::limit(
    $product->short_description ?? strip_tags($product->description ?? ''),
    160
))

@push('styles')
<style>
    /* =========================================================
       ASTROVAANI PRODUCT DETAILS PAGE
       Existing project safe - UI only
    ========================================================= */

    .product-page {
        background: #fafafa;
    }

    /* Breadcrumb */
    .product-breadcrumb {
        background: #fff;
        border-bottom: 1px solid #eee;
    }

    /* Main gallery */
    .product-gallery-sticky {
        position: sticky;
        top: 100px;
    }

    .product-main-img-box {
        position: relative;
        min-height: 500px;
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(0,0,0,.04);
    }

    .product-main-img {
        width: 100%;
        height: 480px;
        object-fit: contain;
        padding: 25px;
        transition: transform .3s ease;
    }

    .product-main-img:hover {
        transform: scale(1.03);
    }

    .product-badge {
        position: absolute;
        top: 16px;
        z-index: 2;
        padding: 7px 13px;
        border-radius: 30px;
        font-size: .78rem;
        font-weight: 700;
    }

    .badge-featured {
        left: 16px;
        background: #f6b93b;
        color: #3d2600;
    }

    .badge-sale {
        right: 16px;
        background: #dc3545;
        color: #fff;
    }

    /* Gallery */
    .product-gallery-thumbs {
        display: flex;
        gap: 10px;
        margin-top: 14px;
        overflow-x: auto;
        padding: 3px;
    }

    .gallery-thumb {
        flex: 0 0 76px;
        width: 76px;
        height: 76px;
        padding: 3px;
        border: 1px solid #ddd;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        overflow: hidden;
        transition: .2s ease;
    }

    .gallery-thumb:hover {
        border-color: #8e44ad;
    }

    .gallery-thumb.active {
        border: 2px solid #8e44ad;
        box-shadow: 0 0 0 2px rgba(142,68,173,.08);
    }

    .gallery-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    /* Trust strip */
    .trust-strip {
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 14px;
        padding: 15px;
        margin-top: 14px;
    }

    .trust-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .82rem;
        color: #555;
    }

    /* Product info */
    .product-category-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f5eefa;
        color: #6c3483;
        border: 1px solid #ead8f1;
        border-radius: 30px;
        padding: 7px 14px;
        font-size: .8rem;
        font-weight: 700;
        text-decoration: none;
    }

    .product-title {
        font-size: clamp(1.8rem, 3vw, 2.7rem);
        line-height: 1.2;
        font-weight: 800;
        color: #202124;
        margin-bottom: 15px;
    }

    .rating-box {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }

    .stars {
        color: #f5a623;
        letter-spacing: 1px;
    }

    .review-count {
        color: #666;
        font-size: .9rem;
    }

    /* Price */
    .price-box {
        background: linear-gradient(135deg, #fff9ed, #fff);
        border: 1px solid #f4dfad;
        border-radius: 15px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .current-price {
        font-size: 2.2rem;
        font-weight: 800;
        color: #1f1f1f;
    }

    .old-price {
        font-size: 1.1rem;
        color: #888;
        text-decoration: line-through;
    }

    .discount-badge {
        background: #198754;
        color: #fff;
        border-radius: 6px;
        padding: 5px 9px;
        font-size: .8rem;
        font-weight: 700;
    }

    /* Stock */
    .stock-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 30px;
        padding: 6px 12px;
        font-size: .8rem;
        font-weight: 700;
    }

    .stock-in {
        background: #e9f8ef;
        color: #198754;
    }

    .stock-low {
        background: #fff3cd;
        color: #856404;
    }

    .stock-out {
        background: #fdeaea;
        color: #dc3545;
    }

    /* Purchase box */
    .purchase-box {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 18px;
        padding: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,.04);
    }

    .quantity-control {
        display: flex;
        align-items: center;
        width: 145px;
        border: 1px solid #ddd;
        border-radius: 9px;
        overflow: hidden;
    }

    .quantity-control button {
        width: 42px;
        height: 42px;
        border: 0;
        background: #f7f7f7;
        font-size: 1.1rem;
    }

    .quantity-control input {
        width: 60px;
        height: 42px;
        border: 0;
        text-align: center;
        font-weight: 700;
    }

    .buy-btn {
        background: #f6b93b;
        border: none;
        color: #241700;
        font-weight: 800;
        min-height: 52px;
        border-radius: 10px;
    }

    .cart-btn {
        min-height: 52px;
        border-radius: 10px;
        font-weight: 800;
    }

    /* Astro consultation */
    .astro-box {
        background: linear-gradient(135deg, #1c0d2d, #38205b);
        border-radius: 18px;
        color: #fff;
        padding: 22px;
        margin-top: 20px;
    }

    .astro-icon {
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        border-radius: 50%;
        background: #f6b93b;
        color: #2d1b00;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Service cards */
    .service-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
        margin-top: 20px;
    }

    .service-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 13px;
        font-size: .82rem;
        color: #555;
    }

    .service-card i {
        font-size: 1.25rem;
        margin-right: 7px;
    }

    /* Information tabs */
    .product-info-section {
        margin-top: 55px;
    }

    .product-tabs {
        border-bottom: 1px solid #ddd;
    }

    .product-tabs .nav-link {
        color: #555;
        font-weight: 700;
        border: 0;
        padding: 14px 20px;
    }

    .product-tabs .nav-link.active {
        color: #6c3483;
        background: transparent;
        border-bottom: 3px solid #6c3483;
    }

    .info-card {
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 15px;
        padding: 25px;
    }

    .spec-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 13px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .spec-row:last-child {
        border-bottom: 0;
    }

    .spec-label {
        color: #777;
    }

    .spec-value {
        color: #222;
        font-weight: 700;
        text-align: right;
    }

    /* What's inside */
    .inside-card {
        border: 1px solid #eee;
        border-radius: 14px;
        background: #fff;
        padding: 18px;
        height: 100%;
    }

    .inside-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #f7eefa;
        color: #6c3483;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    /* Reviews */
    .review-summary {
        background: #fffaf0;
        border: 1px solid #f2dfb3;
        border-radius: 15px;
        padding: 25px;
    }

    /* Related */
    .related-section {
        margin-top: 55px;
        padding-top: 40px;
        border-top: 1px solid #eee;
    }

    /* Mobile */
    @media (max-width: 991px) {
        .product-gallery-sticky {
            position: static;
        }

        .product-main-img-box {
            min-height: 400px;
        }

        .product-main-img {
            height: 380px;
        }
    }

    @media (max-width: 576px) {
        .service-grid {
            grid-template-columns: 1fr;
        }

        .product-main-img-box {
            min-height: 320px;
        }

        .product-main-img {
            height: 300px;
        }

        .current-price {
            font-size: 1.8rem;
        }
    }
</style>
@endpush


@section('content')

<div class="product-page">

    {{-- =========================================================
         BREADCRUMB
    ========================================================== --}}
    <div class="product-breadcrumb py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">

                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}"
                           class="text-decoration-none text-muted">
                            Home
                        </a>
                    </li>

                    <li class="breadcrumb-item">
                        <a href="{{ route('shop.index') }}"
                           class="text-decoration-none text-muted">
                            Spiritual Shop
                        </a>
                    </li>

                    @if($product->category)
                        <li class="breadcrumb-item">
                            <a href="{{ route('shop.category', $product->category->slug) }}"
                               class="text-decoration-none text-muted">
                                {{ $product->category->name }}
                            </a>
                        </li>
                    @endif

                    <li class="breadcrumb-item active text-dark fw-semibold">
                        {{ Str::limit($product->name, 40) }}
                    </li>

                </ol>
            </nav>
        </div>
    </div>


    <div class="container py-5">

        {{-- =====================================================
             MAIN PRODUCT AREA
        ====================================================== --}}
        <div class="row g-5">

            {{-- ================= IMAGE / GALLERY ================= --}}
            <div class="col-lg-5">

                <div class="product-gallery-sticky">

                    <div class="product-main-img-box">

                        {{-- Featured --}}
                        @if($product->is_featured)
                            <span class="product-badge badge-featured">
                                <i class="bi bi-star-fill me-1"></i>
                                BESTSELLER
                            </span>
                        @endif

                        {{-- Sale --}}
                        @if($product->is_on_sale)
                            <span class="product-badge badge-sale">
                                SAVE {{ $product->discount_percentage }}%
                            </span>
                        @endif


                        {{-- Main Image --}}
                        @if($product->image)

                            <img
                                id="mainProductImage"
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                class="product-main-img"
                            >

                        @else

                            <div class="text-center text-warning">
                                <i class="bi bi-stars"
                                   style="font-size: 6rem;"></i>

                                <div class="text-muted fw-semibold mt-2">
                                    Sacred Vedic Product
                                </div>
                            </div>

                        @endif

                    </div>


                    {{-- ================= GALLERY ================= --}}
                    @if($product->image || !empty($product->gallery))

                        <div class="product-gallery-thumbs">

                            {{-- Main Image Thumbnail --}}
                            @if($product->image)

                                <button
                                    type="button"
                                    class="gallery-thumb active"
                                    onclick="changeProductImage(
                                        '{{ asset('storage/' . $product->image) }}',
                                        this
                                    )"
                                >
                                    <img
                                        src="{{ asset('storage/' . $product->image) }}"
                                        alt="{{ $product->name }}"
                                    >
                                </button>

                            @endif


                            {{-- Gallery Images --}}
                            @if(!empty($product->gallery))

                                @foreach($product->gallery as $galleryImage)

                                    <button
                                        type="button"
                                        class="gallery-thumb"
                                        onclick="changeProductImage(
                                            '{{ asset('storage/' . $galleryImage) }}',
                                            this
                                        )"
                                    >
                                        <img
                                            src="{{ asset('storage/' . $galleryImage) }}"
                                            alt="{{ $product->name }}"
                                        >
                                    </button>

                                @endforeach

                            @endif

                        </div>

                    @endif


                    {{-- ================= TRUST STRIP ================= --}}
                    <div class="trust-strip">

                        <div class="row g-3">

                            <div class="col-6">
                                <div class="trust-item">
                                    <i class="bi bi-patch-check-fill text-warning fs-5"></i>
                                    <span>Authentic Product</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="trust-item">
                                    <i class="bi bi-shield-check text-success fs-5"></i>
                                    <span>Secure Purchase</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="trust-item">
                                    <i class="bi bi-box-seam text-primary fs-5"></i>
                                    <span>Safe Packaging</span>
                                </div>
                            </div>

                            <div class="col-6">
                                <div class="trust-item">
                                    <i class="bi bi-arrow-repeat text-info fs-5"></i>
                                    <span>Easy Replacement</span>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ================= PRODUCT INFORMATION ================= --}}
            <div class="col-lg-7">

                {{-- Category + SKU --}}
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">

                    @if($product->category)

                        <a
                            href="{{ route('shop.category', $product->category->slug) }}"
                            class="product-category-pill"
                        >
                            <i class="bi bi-tag-fill"></i>
                            {{ $product->category->name }}
                        </a>

                    @endif

                    <span class="small text-muted">
                        SKU:
                        <strong class="text-dark">
                            {{ $product->sku }}
                        </strong>
                    </span>

                </div>


                {{-- Product Name --}}
                <h1 class="product-title">
                    {{ $product->name }}
                </h1>


                {{-- ================= RATING ================= --}}
                <div class="rating-box">

                    <div class="stars">

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

                    <strong>
                        {{ number_format($product->rating_avg, 1) }}
                    </strong>

                    <span class="review-count">
                        {{ $product->total_reviews }} Reviews
                    </span>


                    {{-- Stock status --}}
                    @if($product->stock <= 0 || $product->status === 'out_of_stock')

                        <span class="stock-status stock-out">
                            <i class="bi bi-x-circle-fill"></i>
                            Out of Stock
                        </span>

                    @elseif($product->stock <= 5)

                        <span class="stock-status stock-low">
                            <i class="bi bi-exclamation-circle-fill"></i>
                            Only {{ $product->stock }} left
                        </span>

                    @else

                        <span class="stock-status stock-in">
                            <i class="bi bi-check-circle-fill"></i>
                            In Stock
                        </span>

                    @endif

                </div>


                {{-- ================= PRICE ================= --}}
                <div class="price-box">

                    <div class="d-flex align-items-center flex-wrap gap-3">

                        <span class="current-price">
                            ₹{{ number_format($product->effective_price, 2) }}
                        </span>


                        @if($product->is_on_sale)

                            <span class="old-price">
                                ₹{{ number_format($product->price, 2) }}
                            </span>

                            <span class="discount-badge">
                                {{ $product->discount_percentage }}% OFF
                            </span>

                        @endif

                    </div>


                    @if($product->is_on_sale)

                        <div class="small text-success fw-semibold mt-2">
                            You save
                            ₹{{ number_format(
                                $product->price - $product->effective_price,
                                2
                            ) }}
                        </div>

                    @endif


                    <div class="small text-muted mt-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Inclusive of applicable taxes.
                    </div>

                </div>


                {{-- ================= SHORT DESCRIPTION ================= --}}
                @if($product->short_description)

                    <div class="mb-4">

                        <p class="text-secondary mb-0"
                           style="line-height:1.8;">

                            {{ $product->short_description }}

                        </p>

                    </div>

                @endif


                {{-- ================= PURCHASE BOX ================= --}}
                <div class="purchase-box">

                    @if($product->in_stock)

                        {{-- Quantity --}}
                        <div class="d-flex align-items-center flex-wrap gap-3 mb-4">

                            <div>
                                <div class="small fw-bold text-dark mb-2">
                                    Quantity
                                </div>

                                <div class="quantity-control">

                                    <button
                                        type="button"
                                        onclick="decrementQty()"
                                    >
                                        <i class="bi bi-dash"></i>
                                    </button>


                                    <input
                                        type="number"
                                        id="quantityInput"
                                        value="1"
                                        min="1"
                                        max="{{ $product->stock }}"
                                    >


                                    <button
                                        type="button"
                                        onclick="incrementQty()"
                                    >
                                        <i class="bi bi-plus"></i>
                                    </button>

                                </div>

                            </div>


                            <div class="small text-muted mt-4">
                                {{ $product->stock }} units available
                            </div>

                        </div>


                        {{-- Buttons --}}
                        <div class="row g-3">

                            <div class="col-md-6">

                                <button
                                    type="button"
                                    class="btn buy-btn w-100"
                                    onclick="handleBuyNow()"
                                >
                                    <i class="bi bi-lightning-charge-fill me-2"></i>
                                    Buy Now
                                </button>

                            </div>


                            <div class="col-md-6">

                                <button
                                    type="button"
                                    class="btn btn-dark cart-btn w-100"
                                    onclick="handleAddToCart()"
                                >
                                    <i class="bi bi-cart-plus me-2"></i>
                                    Add to Cart
                                </button>

                            </div>

                        </div>


                    @else

                        <div class="alert alert-danger mb-0">

                            <i class="bi bi-exclamation-triangle-fill me-2"></i>

                            <strong>Currently Out of Stock.</strong>

                            <div class="small mt-1">
                                Please check back later for availability.
                            </div>

                        </div>

                    @endif

                </div>


                {{-- ================= ASTRO RECOMMENDATION ================= --}}
                <div class="astro-box">

                    <div class="d-flex gap-3">

                        <div class="astro-icon">
                            <i class="bi bi-stars fs-5"></i>
                        </div>


                        <div>

                            <h5 class="fw-bold mb-2 text-warning">
                                Recommended according to your Astro result
                            </h5>

                            <p class="small mb-3 text-white-50">
                                Gemstones, Rudraksha and certain spiritual
                                remedies may vary based on individual birth
                                charts. Consider consulting an astrologer
                                before choosing a personal remedy.
                            </p>


                            @if(Route::has('astrologers.index'))

                                <a
                                    href="{{ route('astrologers.index') }}"
                                    class="btn btn-outline-warning btn-sm fw-bold"
                                >
                                    <i class="bi bi-person-check me-1"></i>
                                    Consult an Astrologer
                                </a>

                            @endif

                        </div>

                    </div>

                </div>


                {{-- ================= SERVICE INFORMATION ================= --}}
                <div class="service-grid">

                    <div class="service-card">
                        <i class="bi bi-truck text-primary"></i>
                        Free shipping on eligible orders
                    </div>

                    <div class="service-card">
                        <i class="bi bi-arrow-repeat text-success"></i>
                        7-Day return / replacement
                    </div>

                    <div class="service-card">
                        <i class="bi bi-patch-check text-warning"></i>
                        Authenticity assurance
                    </div>

                    <div class="service-card">
                        <i class="bi bi-shield-lock text-info"></i>
                        Secure payment
                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             PRODUCT INFORMATION TABS
        ====================================================== --}}
        <div class="product-info-section">

            <ul
                class="nav nav-tabs product-tabs"
                id="productTabs"
                role="tablist"
            >

                <li class="nav-item">
                    <button
                        class="nav-link active"
                        data-bs-toggle="tab"
                        data-bs-target="#description"
                        type="button"
                    >
                        <i class="bi bi-file-text me-1"></i>
                        Description
                    </button>
                </li>


                <li class="nav-item">
                    <button
                        class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#spiritual"
                        type="button"
                    >
                        <i class="bi bi-stars me-1"></i>
                        Spiritual Significance
                    </button>
                </li>


                <li class="nav-item">
                    <button
                        class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#inside"
                        type="button"
                    >
                        <i class="bi bi-box-seam me-1"></i>
                        What's Inside
                    </button>
                </li>


                <li class="nav-item">
                    <button
                        class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#specifications"
                        type="button"
                    >
                        <i class="bi bi-card-checklist me-1"></i>
                        Specifications
                    </button>
                </li>


                <li class="nav-item">
                    <button
                        class="nav-link"
                        data-bs-toggle="tab"
                        data-bs-target="#reviews"
                        type="button"
                    >
                        <i class="bi bi-chat-heart me-1"></i>
                        Reviews
                    </button>
                </li>

            </ul>


            <div class="tab-content pt-4">


                {{-- ================= DESCRIPTION ================= --}}
                <div
                    class="tab-pane fade show active"
                    id="description"
                >

                    <div class="info-card">

                        <h4 class="fw-bold mb-3">
                            About {{ $product->name }}
                        </h4>

                        <div
                            class="text-secondary"
                            style="line-height:1.9;"
                        >
                            {!! nl2br(e($product->description ?? '')) !!}
                        </div>

                    </div>

                </div>


                {{-- ================= SPIRITUAL ================= --}}
                <div
                    class="tab-pane fade"
                    id="spiritual"
                >

                    <div class="info-card">

                        <h4 class="fw-bold mb-3">
                            Vedic & Spiritual Significance
                        </h4>

                        <p class="text-secondary"
                           style="line-height:1.9;">

                            This section can contain the traditional
                            spiritual significance, pooja usage and
                            customary practices associated with this
                            product.
                        </p>


                        <div class="row g-4 mt-2">

                            <div class="col-md-6">

                                <div class="inside-card">

                                    <div class="inside-icon">
                                        <i class="bi bi-brightness-high"></i>
                                    </div>

                                    <h6 class="fw-bold">
                                        Traditional Usage
                                    </h6>

                                    <p class="small text-muted mb-0">
                                        Follow the customary pooja or
                                        spiritual usage instructions
                                        appropriate to the product.
                                    </p>

                                </div>

                            </div>


                            <div class="col-md-6">

                                <div class="inside-card">

                                    <div class="inside-icon">
                                        <i class="bi bi-shield-check"></i>
                                    </div>

                                    <h6 class="fw-bold">
                                        Authenticity
                                    </h6>

                                    <p class="small text-muted mb-0">
                                        Product authenticity and
                                        certification information can be
                                        displayed here when available.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================= WHAT'S INSIDE ================= --}}
                <div
                    class="tab-pane fade"
                    id="inside"
                >

                    <div class="info-card">

                        <h4 class="fw-bold mb-2">
                            What's Inside
                        </h4>

                        <p class="text-muted small mb-4">
                            For Homam Kits and Combo Products, the complete
                            contents can be displayed here.
                        </p>


                        <div class="row g-3">

                            <div class="col-md-4">

                                <div class="inside-card">

                                    <div class="inside-icon">
                                        <i class="bi bi-box"></i>
                                    </div>

                                    <h6 class="fw-bold">
                                        Main Product
                                    </h6>

                                    <p class="small text-muted mb-0">
                                        {{ $product->name }}
                                    </p>

                                </div>

                            </div>


                            <div class="col-md-4">

                                <div class="inside-card">

                                    <div class="inside-icon">
                                        <i class="bi bi-stars"></i>
                                    </div>

                                    <h6 class="fw-bold">
                                        Spiritual Items
                                    </h6>

                                    <p class="small text-muted mb-0">
                                        Kit-specific pooja materials can
                                        be listed here.
                                    </p>

                                </div>

                            </div>


                            <div class="col-md-4">

                                <div class="inside-card">

                                    <div class="inside-icon">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </div>

                                    <h6 class="fw-bold">
                                        Information
                                    </h6>

                                    <p class="small text-muted mb-0">
                                        Usage and product information can
                                        be included with the kit.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================= SPECIFICATIONS ================= --}}
                <div
                    class="tab-pane fade"
                    id="specifications"
                >

                    <div class="info-card">

                        <h4 class="fw-bold mb-4">
                            Product Specifications
                        </h4>


                        <div class="spec-row">

                            <span class="spec-label">
                                Product Name
                            </span>

                            <span class="spec-value">
                                {{ $product->name }}
                            </span>

                        </div>


                        <div class="spec-row">

                            <span class="spec-label">
                                SKU
                            </span>

                            <span class="spec-value">
                                {{ $product->sku }}
                            </span>

                        </div>


                        <div class="spec-row">

                            <span class="spec-label">
                                Category
                            </span>

                            <span class="spec-value">
                                {{ $product->category->name ?? 'Spiritual Product' }}
                            </span>

                        </div>


                        <div class="spec-row">

                            <span class="spec-label">
                                Availability
                            </span>

                            <span class="spec-value">

                                @if($product->in_stock)
                                    In Stock
                                @else
                                    Out of Stock
                                @endif

                            </span>

                        </div>


                        <div class="spec-row">

                            <span class="spec-label">
                                Available Quantity
                            </span>

                            <span class="spec-value">
                                {{ $product->stock }}
                            </span>

                        </div>


                        <div class="spec-row">

                            <span class="spec-label">
                                Rating
                            </span>

                            <span class="spec-value">
                                {{ number_format($product->rating_avg, 1) }} / 5
                            </span>

                        </div>

                    </div>

                </div>


                {{-- ================= REVIEWS ================= --}}
                <div
                    class="tab-pane fade"
                    id="reviews"
                >

                    <div class="row g-4">

                        <div class="col-md-4">

                            <div class="review-summary text-center">

                                <div class="display-4 fw-bold">
                                    {{ number_format($product->rating_avg, 1) }}
                                </div>

                                <div class="stars fs-4 mb-2">

                                    @for($i = 1; $i <= 5; $i++)

                                        @if($i <= floor($product->rating_avg))
                                            <i class="bi bi-star-fill"></i>
                                        @else
                                            <i class="bi bi-star"></i>
                                        @endif

                                    @endfor

                                </div>

                                <div class="text-muted">
                                    Based on
                                    {{ $product->total_reviews }}
                                    reviews
                                </div>

                            </div>

                        </div>


                        <div class="col-md-8">

                            <div class="info-card">

                                <h5 class="fw-bold mb-3">
                                    Customer Reviews
                                </h5>

                                @if($product->total_reviews > 0)

                                    <p class="text-muted mb-0">
                                        Customer review details can be
                                        displayed here when the review
                                        records are connected.
                                    </p>

                                @else

                                    <div class="text-center py-4">

                                        <i
                                            class="bi bi-chat-heart text-muted"
                                            style="font-size:3rem;"
                                        ></i>

                                        <h6 class="fw-bold mt-3">
                                            No reviews yet
                                        </h6>

                                        <p class="text-muted small mb-0">
                                            Be the first devotee to review
                                            this product.
                                        </p>

                                    </div>

                                @endif

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             RELATED PRODUCTS
        ====================================================== --}}
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)

            <div class="related-section">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

                    <div>

                        <h3 class="fw-bold mb-1">
                            Related Sacred Products
                        </h3>

                        <p class="text-muted small mb-0">
                            Explore other products from this category.
                        </p>

                    </div>


                    <a
                        href="{{ route('shop.index') }}"
                        class="btn btn-outline-dark btn-sm"
                    >
                        View All
                        <i class="bi bi-arrow-right ms-1"></i>
                    </a>

                </div>


                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4">

                    @foreach($relatedProducts as $relProduct)

                        <div class="col">

                            @include(
                                'components.product-card',
                                ['product' => $relProduct]
                            )

                        </div>

                    @endforeach

                </div>

            </div>

        @endif

    </div>

</div>


{{-- =========================================================
     JAVASCRIPT
========================================================== --}}
@push('scripts')

<script>

    /* =====================================================
       IMAGE GALLERY
    ====================================================== */

    function changeProductImage(imageUrl, button) {

        const mainImage =
            document.getElementById('mainProductImage');

        if (mainImage) {

            mainImage.src = imageUrl;

        }


        document
            .querySelectorAll('.gallery-thumb')
            .forEach(function (thumb) {

                thumb.classList.remove('active');

            });


        if (button) {

            button.classList.add('active');

        }

    }


    /* =====================================================
       QUANTITY
    ====================================================== */

    function incrementQty() {

        const input =
            document.getElementById('quantityInput');

        if (!input) return;

        const max =
            parseInt(input.getAttribute('max')) || 99;

        let value =
            parseInt(input.value) || 1;


        if (value < max) {

            input.value = value + 1;

        }

    }


    function decrementQty() {

        const input =
            document.getElementById('quantityInput');

        if (!input) return;

        let value =
            parseInt(input.value) || 1;


        if (value > 1) {

            input.value = value - 1;

        }

    }


    /* =====================================================
       CART / BUY NOW ACTION
       Existing backend cart system
    ====================================================== */

    function handleAddToCart() {

        const input = document.getElementById('quantityInput');

        const quantity =
            input ? parseInt(input.value) || 1 : 1;

        const form = document.createElement('form');

        form.method = 'POST';
        form.action = "{{ route('cart.add', $product->id) }}";

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = "{{ csrf_token() }}";

        const qty = document.createElement('input');
        qty.type = 'hidden';
        qty.name = 'quantity';
        qty.value = quantity;

        form.appendChild(csrf);
        form.appendChild(qty);

        document.body.appendChild(form);

        form.submit();
    }


    function handleBuyNow() {

        const input = document.getElementById('quantityInput');

        const quantity =
            input ? parseInt(input.value) || 1 : 1;

        const formData = new FormData();

        formData.append('_token', "{{ csrf_token() }}");
        formData.append('quantity', quantity);

        fetch("{{ route('cart.add', $product->id) }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {

            if (!response.ok) {
                throw new Error('Unable to add product to cart.');
            }

            return response.json();
        })
        .then(data => {

            window.location.href = "{{ url('/cart') }}";

        })
        .catch(error => {

            console.error(error);

            alert('Unable to add this product to cart. Please try again.');

        });
    }

</script>

@endpush

@endsection