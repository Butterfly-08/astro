@extends('layouts.app')

@section('title', isset($currentCategory)
    ? $currentCategory->name . ' — AstroVaani Spiritual Shop'
    : 'AstroVaani Spiritual Shop — Pooja, Rudraksha, Yantra & Spiritual Products')

@section('meta_description', isset($currentCategory)
    ? ($currentCategory->description ?? 'Explore sacred spiritual products at AstroVaani.')
    : 'Explore Pooja Essentials, Rudraksha, Yantra, Gemstones, Homam Kits, Pariharam Kits and devotional products at AstroVaani.')

@push('styles')

<style>
    /* =========================================================
       SHOP PAGE
    ========================================================= */

    .shop-page {
        background: #f6f6f6;
        min-height: 100vh;
    }

    /* =========================================================
       SEARCH HEADER
    ========================================================= */

    .shop-search-header {
        background: #2d124d;
        padding: 18px 0;
    }

    .shop-search-box {
        background: #fff;
        border-radius: 4px;
        min-height: 46px;
        display: flex;
        align-items: center;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
    }

    .shop-search-box input {
        border: 0;
        outline: 0;
        box-shadow: none;
        flex: 1;
        height: 46px;
        padding: 0 14px;
        font-size: .95rem;
    }

    .shop-search-box button {
        height: 46px;
        border: 0;
        background: #f5b041;
        color: #24102f;
        font-weight: 700;
        padding: 0 25px;
    }

    .shop-search-box button:hover {
        background: #e9a22e;
    }

    /* =========================================================
       CATEGORY ROW
    ========================================================= */

    .category-strip {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06);
    }

    .category-scroll {
        display: flex;
        align-items: stretch;
        gap: 0;
        overflow-x: auto;
        overflow-y: hidden;
        scrollbar-width: thin;
        scrollbar-color: #999 #f5f5f5;
        -webkit-overflow-scrolling: touch;
    }

    .category-scroll::-webkit-scrollbar {
        height: 7px;
    }

    .category-scroll::-webkit-scrollbar-track {
        background: #f5f5f5;
    }

    .category-scroll::-webkit-scrollbar-thumb {
        background: #999;
        border-radius: 10px;
    }

    /* IMPORTANT: Prevent category items from shrinking */
    .category-item {
        flex: 0 0 120px;
        width: 120px;
        min-width: 120px;
        max-width: 120px;

        padding: 13px 8px 10px;

        text-align: center;
        text-decoration: none;
        color: #222;

        border-bottom: 3px solid transparent;

        transition: .2s ease;

        white-space: normal;
        overflow: hidden;

        box-sizing: border-box;
    }

    .category-item:hover {
        color: #6c3483;
        background: #faf7ff;
    }

    .category-item.active {
        color: #6c3483;
        border-bottom-color: #6c3483;
        font-weight: 700;
    }

    .category-icon {
        width: 45px;
        height: 45px;
        margin: 0 auto 7px;

        border-radius: 50%;
        background: #f8f3ff;

        display: flex;
        align-items: center;
        justify-content: center;

        font-size: 1.35rem;
        color: #6c3483;

        flex-shrink: 0;
    }

    .category-item.active .category-icon {
        background: #6c3483;
        color: #f5b041;
    }

    /* Category text */
    .category-name {
        display: block;

        width: 100%;

        font-size: .74rem;
        font-weight: 600;
        line-height: 1.2;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

        text-align: center;
    }

    /* =========================================================
       PROMO BANNER
    ========================================================= */

    .promo-banner {
        margin-top: 18px;
        min-height: 220px;

        border-radius: 8px;
        overflow: hidden;

        position: relative;

        background:
            radial-gradient(
                circle at 85% 25%,
                rgba(245, 176, 65, .45),
                transparent 22%
            ),
            radial-gradient(
                circle at 70% 80%,
                rgba(255, 255, 255, .12),
                transparent 30%
            ),
            linear-gradient(
                115deg,
                #220b3d,
                #5c1d78 55%,
                #8e3ca5
            );

        color: #fff;

        display: flex;
        align-items: center;
    }

    .promo-banner::before {
        content: "ॐ";

        position: absolute;
        right: 7%;
        top: 5px;

        font-size: 10rem;
        font-weight: 700;

        color: rgba(255, 255, 255, .08);
    }

    .promo-banner::after {
        content: "✦  ✧  ✦  ✧  ✦";

        position: absolute;
        right: 8%;
        bottom: 20px;

        color: rgba(245, 176, 65, .5);

        font-size: 1.5rem;
        letter-spacing: 8px;
    }

    .promo-content {
        position: relative;
        z-index: 2;

        padding: 30px 40px;

        max-width: 650px;
    }

    .promo-badge {
        display: inline-block;

        background: #f5b041;
        color: #2d124d;

        padding: 5px 12px;

        border-radius: 4px;

        font-size: .75rem;
        font-weight: 800;

        margin-bottom: 10px;
    }

    .promo-title {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.15;

        margin-bottom: 8px;
    }

    .promo-text {
        color: rgba(255, 255, 255, .82);

        margin-bottom: 16px;
    }

    .promo-btn {
        display: inline-block;

        background: #fff;
        color: #4a1764;

        padding: 9px 20px;

        border-radius: 4px;

        text-decoration: none;

        font-weight: 700;
        font-size: .9rem;
    }

    .promo-btn:hover {
        background: #f5b041;
        color: #2d124d;
    }

    /* =========================================================
       SHOP CONTENT
    ========================================================= */

    .shop-container {
        padding-top: 20px;
        padding-bottom: 50px;
    }

    .shop-card {
        background: #fff;

        border-radius: 5px;
        border: 1px solid #e5e7eb;
    }

    .filter-card {
        position: sticky;
        top: 85px;

        padding: 18px;
    }

    .filter-title {
        font-size: .9rem;
        font-weight: 800;

        text-transform: uppercase;

        color: #374151;
    }

    .section-bar {
        background: #fff;

        border-radius: 5px;
        border: 1px solid #e5e7eb;

        padding: 14px 16px;

        margin-bottom: 14px;
    }

    .section-bar h5 {
        margin: 0;

        font-size: 1.05rem;
        font-weight: 800;
    }

    .product-area {
        min-width: 0;
    }

    /* =========================================================
       PRODUCT CARD WRAPPER
    ========================================================= */

    .product-wrapper {
        height: 100%;

        background: #fff;

        border-radius: 5px;
        border: 1px solid #e5e7eb;

        overflow: hidden;

        transition: .2s ease;
    }

    .product-wrapper:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, .10);
        transform: translateY(-2px);
    }

    .product-wrapper > * {
        height: 100%;
    }

    /* =========================================================
       TRUST STRIP
    ========================================================= */

    .trust-strip {
        background: #fff;

        border: 1px solid #e5e7eb;
        border-radius: 5px;

        margin-top: 20px;

        padding: 22px 15px;
    }

    .trust-item {
        text-align: center;

        border-right: 1px solid #e5e7eb;
    }

    .trust-item:last-child {
        border-right: 0;
    }

    .trust-icon {
        font-size: 1.6rem;

        color: #6c3483;

        margin-bottom: 5px;
    }

    .trust-title {
        font-size: .82rem;
        font-weight: 800;

        margin-bottom: 2px;
    }

    .trust-text {
        font-size: .72rem;

        color: #6b7280;

        margin: 0;
    }

    /* =========================================================
       MOBILE
    ========================================================= */

    @media (max-width: 991px) {

        .filter-card {
            position: static;
        }

        .promo-banner {
            min-height: 190px;
        }

        .promo-content {
            padding: 25px;
        }

        .promo-title {
            font-size: 1.55rem;
        }

        .category-item {
            flex-basis: 115px;
            width: 115px;
            min-width: 115px;
            max-width: 115px;
        }
    }

    @media (max-width: 575px) {

        .shop-search-header {
            padding: 12px 0;
        }

        .shop-search-box button {
            padding: 0 13px;
        }

        /*
         * Mobile category width.
         * Still fixed so items never overlap.
         */
        .category-item {
            flex: 0 0 100px;
            width: 100px;
            min-width: 100px;
            max-width: 100px;

            padding-left: 6px;
            padding-right: 6px;
        }

        .category-icon {
            width: 40px;
            height: 40px;

            font-size: 1.15rem;
        }

        .category-name {
            font-size: .68rem;
        }

        .promo-banner {
            min-height: 180px;
        }

        .promo-content {
            padding: 22px;
        }

        .promo-title {
            font-size: 1.35rem;
        }

        .promo-text {
            font-size: .82rem;
        }

        .trust-item {
            border-right: 0;

            border-bottom: 1px solid #eee;

            padding-bottom: 12px;
        }

        .trust-item:last-child {
            border-bottom: 0;
        }
    }
</style>

@endpush


@section('content')

<div class="shop-page">

    {{-- =========================================================
         SEARCH HEADER
    ========================================================== --}}

    <div class="shop-search-header">

        <div class="container">

            <form
                action="{{ route('shop.index') }}"
                method="GET"
                class="shop-search-box"
            >

                @if(request('category'))

                    <input
                        type="hidden"
                        name="category"
                        value="{{ request('category') }}"
                    >

                @endif

                <span class="px-3 text-muted">
                    <i class="bi bi-search"></i>
                </span>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search Rudraksha, Yantra, Gemstones, Pooja items..."
                >

                <button type="submit">
                    SEARCH
                </button>

            </form>

        </div>

    </div>


    {{-- =========================================================
         CATEGORY ICON STRIP
    ========================================================== --}}

    <div class="category-strip">

        <div class="container">

            <div class="category-scroll">

                {{-- ALL --}}

                <a
                    href="{{ route('shop.index') }}"
                    class="category-item {{ !request('category') && !isset($currentCategory) ? 'active' : '' }}"
                >

                    <div class="category-icon">
                        <i class="bi bi-grid-fill"></i>
                    </div>

                    <div class="category-name">
                        All
                    </div>

                </a>


                @php

                    $categoryIcons = [

                        'pooja-essentials' => 'bi-fire',

                        'incense-dhoop' => 'bi-cloud-haze2',

                        'deepam-pooja-oil' => 'bi-lightbulb',

                        'kumkum-vibhuthi-sandhanam' => 'bi-stars',

                        'rudraksha' => 'bi-circle',

                        'yantra' => 'bi-hexagon',

                        'gemstones' => 'bi-gem',

                        'crystals-spiritual-stones' => 'bi-diamond',

                        'homam-kits' => 'bi-flame',

                        'pariharam-kits' => 'bi-shield-check',

                        'god-idols' => 'bi-person-standing',

                        'devotional-items' => 'bi-heart',

                        'spiritual-accessories' => 'bi-hand-index',

                        'pooja-combo-kits' => 'bi-box-seam'

                    ];

                @endphp


                @foreach($categories as $cat)

                    <a
                        href="{{ route('shop.category', $cat->slug) }}"
                        class="category-item {{
                            (
                                request('category') == $cat->slug ||
                                (
                                    isset($currentCategory) &&
                                    $currentCategory->id == $cat->id
                                )
                            )
                            ? 'active'
                            : ''
                        }}"
                    >

                        <div class="category-icon">

                            <i class="{{ $categoryIcons[$cat->slug] ?? 'bi-stars' }}"></i>

                        </div>

                        <div class="category-name">

                            {{ $cat->name }}

                        </div>

                    </a>

                @endforeach

            </div>

        </div>

    </div>


    {{-- =========================================================
         PROMOTIONAL BANNER
    ========================================================== --}}

    <div class="container">

        <div class="promo-banner">

            <div class="promo-content">

                <span class="promo-badge">
                    ✦ ASTROVAANI SPIRITUAL STORE
                </span>

                <div class="promo-title">
                    Sacred Products for Your Spiritual Journey
                </div>

                <div class="promo-text">
                    Explore authentic Rudraksha, Yantra, Gemstones,
                    Pooja Essentials, Homam Kits & Pariharam products.
                </div>

                <a
                    href="#products"
                    class="promo-btn"
                >
                    Shop Now
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>

            </div>

        </div>

    </div>


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}

    <div class="container shop-container">

        <div class="row g-3">

            {{-- =====================================================
                 FILTER SIDEBAR
            ====================================================== --}}

            <div class="col-lg-3">

                <div class="shop-card filter-card">

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">

                        <span class="filter-title">

                            <i class="bi bi-sliders me-1"></i>

                            Filters

                        </span>


                        @if(request()->hasAny([
                            'search',
                            'category',
                            'min_price',
                            'max_price',
                            'in_stock',
                            'featured'
                        ]))

                            <a
                                href="{{ route('shop.index') }}"
                                class="text-danger small fw-semibold text-decoration-none"
                            >
                                Clear
                            </a>

                        @endif

                    </div>


                    <form
                        action="{{ route('shop.index') }}"
                        method="GET"
                    >

                        {{-- Search --}}

                        @if(request('search'))

                            <input
                                type="hidden"
                                name="search"
                                value="{{ request('search') }}"
                            >

                        @endif


                        {{-- Sort --}}

                        @if(request('sort'))

                            <input
                                type="hidden"
                                name="sort"
                                value="{{ request('sort') }}"
                            >

                        @endif


                        {{-- Category --}}

                        <div class="mb-4">

                            <label class="form-label fw-bold small">
                                Category
                            </label>


                            <div class="form-check mb-2">

                                <input
                                    class="form-check-input"
                                    type="radio"
                                    name="category"
                                    id="cat_all"
                                    value=""
                                    {{ !request('category') ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                >

                                <label
                                    class="form-check-label small"
                                    for="cat_all"
                                >
                                    All Categories
                                </label>

                            </div>


                            @foreach($categories as $cat)

                                <div class="form-check mb-2">

                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="category"
                                        id="cat_{{ $cat->id }}"
                                        value="{{ $cat->slug }}"
                                        {{ request('category') == $cat->slug ? 'checked' : '' }}
                                        onchange="this.form.submit()"
                                    >

                                    <label
                                        class="form-check-label small d-flex justify-content-between"
                                        for="cat_{{ $cat->id }}"
                                    >

                                        <span>
                                            {{ $cat->name }}
                                        </span>

                                        <span class="text-muted">
                                            {{ $cat->products_count ?? $cat->products()->count() }}
                                        </span>

                                    </label>

                                </div>

                            @endforeach

                        </div>


                        <hr>


                        {{-- Price --}}

                        <div class="mb-4">

                            <label class="form-label fw-bold small">
                                Price Range
                            </label>

                            <div class="row g-2">

                                <div class="col-6">

                                    <input
                                        type="number"
                                        name="min_price"
                                        class="form-control form-control-sm"
                                        placeholder="Min"
                                        value="{{ request('min_price') }}"
                                    >

                                </div>

                                <div class="col-6">

                                    <input
                                        type="number"
                                        name="max_price"
                                        class="form-control form-control-sm"
                                        placeholder="Max"
                                        value="{{ request('max_price') }}"
                                    >

                                </div>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-dark btn-sm w-100 mt-2"
                            >
                                Apply
                            </button>

                        </div>


                        <hr>


                        {{-- Preferences --}}

                        <div>

                            <label class="form-label fw-bold small">
                                Availability
                            </label>


                            <div class="form-check mb-2">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="in_stock"
                                    value="1"
                                    id="in_stock"
                                    {{ request('in_stock') == '1' ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                >

                                <label
                                    class="form-check-label small"
                                    for="in_stock"
                                >
                                    In Stock Only
                                </label>

                            </div>


                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="featured"
                                    value="1"
                                    id="featured"
                                    {{ request('featured') == '1' ? 'checked' : '' }}
                                    onchange="this.form.submit()"
                                >

                                <label
                                    class="form-check-label small"
                                    for="featured"
                                >
                                    Featured Products
                                </label>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            {{-- =====================================================
                 PRODUCTS
            ====================================================== --}}

            <div
                class="col-lg-9 product-area"
                id="products"
            >

                {{-- Product Heading --}}

                <div class="section-bar">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                        <div>

                            <h5>

                                @if(isset($currentCategory))

                                    {{ $currentCategory->name }}

                                @else

                                    Spiritual Products

                                @endif

                            </h5>

                            <small class="text-muted">

                                {{ $products->total() }}

                                sacred items available

                            </small>

                        </div>


                        {{-- Sort --}}

                        <div class="d-flex align-items-center gap-2">

                            <span class="small text-muted d-none d-sm-block">
                                Sort:
                            </span>

                            <select
                                class="form-select form-select-sm"
                                style="min-width: 170px;"
                                onchange="window.location.href=this.value"
                            >

                                <option
                                    value="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}"
                                    {{ request('sort', 'featured') == 'featured' ? 'selected' : '' }}
                                >
                                    Featured
                                </option>

                                <option
                                    value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}"
                                    {{ request('sort') == 'price_low' ? 'selected' : '' }}
                                >
                                    Price: Low to High
                                </option>

                                <option
                                    value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}"
                                    {{ request('sort') == 'price_high' ? 'selected' : '' }}
                                >
                                    Price: High to Low
                                </option>

                                <option
                                    value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}"
                                    {{ request('sort') == 'newest' ? 'selected' : '' }}
                                >
                                    Newest
                                </option>

                            </select>

                        </div>

                    </div>


                    @if(request('search'))

                        <div class="mt-2">

                            <span class="badge bg-light text-dark border">

                                Search:
                                "{{ request('search') }}"

                            </span>

                        </div>

                    @endif

                </div>


                {{-- Products Grid --}}

                @if($products->count() > 0)

                    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-3 g-3">

                        @foreach($products as $product)

                            <div class="col">

                                <div class="product-wrapper">

                                    @include(
                                        'components.product-card',
                                        ['product' => $product]
                                    )

                                </div>

                            </div>

                        @endforeach

                    </div>


                    {{-- Pagination --}}

                    <div class="d-flex justify-content-center mt-4">

                        {{ $products->links() }}

                    </div>

                @else

                    <div class="shop-card text-center py-5 px-4">

                        <i
                            class="bi bi-search"
                            style="font-size: 3rem; color: #6c3483;"
                        ></i>

                        <h4 class="fw-bold mt-3">
                            No Sacred Items Found
                        </h4>

                        <p class="text-muted">
                            Try another search or clear your filters.
                        </p>

                        <a
                            href="{{ route('shop.index') }}"
                            class="btn btn-warning fw-bold px-4"
                        >
                            View All Products
                        </a>

                    </div>

                @endif


                {{-- =================================================
                     TRUST STRIP
                ================================================== --}}

                <div class="trust-strip">

                    <div class="row g-3">

                        <div class="col-6 col-md-3">

                            <div class="trust-item">

                                <div class="trust-icon">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>

                                <div class="trust-title">
                                    Authentic
                                </div>

                                <p class="trust-text">
                                    Genuine spiritual products
                                </p>

                            </div>

                        </div>


                        <div class="col-6 col-md-3">

                            <div class="trust-item">

                                <div class="trust-icon">
                                    <i class="bi bi-stars"></i>
                                </div>

                                <div class="trust-title">
                                    Energized
                                </div>

                                <p class="trust-text">
                                    Traditional Vedic practices
                                </p>

                            </div>

                        </div>


                        <div class="col-6 col-md-3">

                            <div class="trust-item">

                                <div class="trust-icon">
                                    <i class="bi bi-truck"></i>
                                </div>

                                <div class="trust-title">
                                    Safe Delivery
                                </div>

                                <p class="trust-text">
                                    Secure doorstep delivery
                                </p>

                            </div>

                        </div>


                        <div class="col-6 col-md-3">

                            <div class="trust-item">

                                <div class="trust-icon">
                                    <i class="bi bi-shield-check"></i>
                                </div>

                                <div class="trust-title">
                                    Secure Checkout
                                </div>

                                <p class="trust-text">
                                    Safe & protected payment
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection