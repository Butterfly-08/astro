@extends('layouts.app')

@section('title', isset($currentCategory) ? $currentCategory->name . ' — AstroVaani Spiritual Shop' : 'Spiritual E-Commerce Shop — Energized Gemstones, Rudraksha, Yantras | AstroVaani')
@section('meta_description', isset($currentCategory) ? ($currentCategory->description ?? 'Explore ' . $currentCategory->name . ' at AstroVaani. 100% authentic, energised and Vedic sanctified.') : 'Discover authentic Vedic gemstones, energized Rudraksha beads, sanctified copper yantras, puja essentials, and personalized astrological reports at AstroVaani.')

@push('styles')
<style>
    /* Hero Banner */
    .shop-hero {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 55%, #561D78 100%);
        color: #fff;
        padding: 55px 0 45px;
        position: relative;
        overflow: hidden;
    }
    .shop-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='1' fill='rgba(245,176,65,0.3)'/%3E%3C/svg%3E") repeat;
        opacity: 0.4;
    }

    /* Category Navigation Badges */
    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: 30px;
        background: #fff;
        border: 1.5px solid #E5E7EB;
        color: #374151;
        font-size: 0.88rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        white-space: nowrap;
    }
    .cat-pill:hover {
        border-color: #6C3483;
        color: #6C3483;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 52, 131, 0.12);
    }
    .cat-pill.active {
        background: linear-gradient(135deg, #2D124D, #6C3483);
        border-color: #2D124D;
        color: #F5B041;
        box-shadow: 0 4px 14px rgba(45, 18, 77, 0.25);
    }

    /* Product Card Component Styling */
    .product-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .product-card:hover {
        transform: translateY(-5px);
        border-color: #D1D5DB;
        box-shadow: 0 14px 34px rgba(26, 11, 46, 0.08);
    }
    .product-card-img-wrap {
        height: 220px;
        background: #F9FAFB;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-card-img {
        max-height: 100%;
        max-width: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .product-card:hover .product-card-img {
        transform: scale(1.04);
    }
    .product-img-fallback {
        height: 220px;
        width: 100%;
        background: linear-gradient(135deg, #F9FAFB, #F3F4F6);
    }
    .product-card-title {
        font-size: 0.96rem;
        line-height: 1.35;
        font-weight: 700;
    }
    .product-card-title a {
        transition: color 0.2s ease;
    }
    .product-card-title a:hover {
        color: #6C3483 !important;
    }

    /* Filter Box */
    .filter-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #E5E7EB;
        padding: 24px;
        position: sticky;
        top: 90px;
    }
    .filter-card-header {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #6B7280;
    }

    /* Trust Feature Strip */
    .trust-strip {
        background: #FAF5FF;
        border: 1px solid #EDE9FE;
        border-radius: 16px;
        padding: 24px 20px;
    }
</style>
@endpush

@section('content')

{{-- Shop Hero Header --}}
<div class="shop-hero">
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 text-warning small fw-bold mb-3">
                    <i class="bi bi-shield-check"></i> 100% VEDIC SANCTIFIED & CERTIFIED
                </div>
                <h1 class="display-5 fw-bold text-white mb-2">
                    {{ isset($currentCategory) ? $currentCategory->name : 'Vedic & Spiritual E-Store' }}
                </h1>
                <p class="lead text-white-50 mb-4" style="max-width: 650px; font-size: 1.05rem;">
                    {{ isset($currentCategory) ? ($currentCategory->description ?? 'Browse hand-selected and authentically energized spiritual items for your astrological remedy.') : 'Discover energised Gemstones, sacred Nepali & Indonesian Rudraksha, hand-crafted Yantras, and Vedic remedy essentials consecrated by Vedic scholars.' }}
                </p>

                {{-- Fast Search Form --}}
                <form action="{{ route('shop.index') }}" method="GET" class="d-flex align-items-center bg-white p-2 rounded-3 shadow-lg" style="max-width: 580px;">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 text-muted"><i class="bi bi-search fs-5"></i></span>
                        <input type="text" name="search" class="form-control border-0 shadow-none ps-0" placeholder="Search gemstones, 5 mukhi rudraksha, shree yantra..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-warning fw-bold px-4 rounded-2">Find Items</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 text-start">
                    <h6 class="text-warning fw-bold mb-2"><i class="bi bi-stars me-1"></i> Astrological Guarantee</h6>
                    <ul class="list-unstyled text-white small mb-0 d-flex flex-column gap-2">
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Consecrated via Vedic Mantras</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Lab Tested & Certified Gemstones</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Personalized Energization on your Gotra</li>
                        <li><i class="bi bi-check-circle-fill text-success me-2"></i>Discreet & Insured Express Delivery</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Category Quick Pills --}}
<div class="bg-white border-bottom py-3">
    <div class="container">
        <div class="d-flex align-items-center gap-2 overflow-auto pb-2" style="scrollbar-width: thin;">
            <a href="{{ route('shop.index') }}" class="cat-pill {{ !request('category') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> All Items
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('shop.category', $cat->slug) }}" class="cat-pill {{ (request('category') == $cat->slug || (isset($currentCategory) && $currentCategory->id == $cat->id)) ? 'active' : '' }}">
                    @if($cat->icon)
                        <i class="{{ $cat->icon }}"></i>
                    @else
                        <i class="bi bi-tag-fill"></i>
                    @endif
                    {{ $cat->name }}
                    <span class="badge bg-light text-dark rounded-pill ms-1" style="font-size: 0.72rem;">{{ $cat->products_count ?? $cat->products()->count() }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Main Shop Body --}}
<div class="container py-5">
    <div class="row g-4">
        {{-- Sidebar Filters --}}
        <div class="col-lg-3">
            <div class="filter-card">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                    <span class="filter-card-header"><i class="bi bi-sliders me-1"></i> Filter Items</span>
                    @if(request()->hasAny(['search', 'category', 'min_price', 'max_price', 'in_stock', 'featured']))
                        <a href="{{ route('shop.index') }}" class="text-danger small fw-semibold text-decoration-none">
                            <i class="bi bi-x-circle me-1"></i>Clear All
                        </a>
                    @endif
                </div>

                <form action="{{ route('shop.index') }}" method="GET" id="shopFilterForm">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif

                    {{-- Category Filter --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase mb-2">Category</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="category" id="cat_all" value="" {{ !request('category') ? 'checked' : '' }} onchange="this.form.submit()">
                                <label class="form-check-label small" for="cat_all">All Categories</label>
                            </div>
                            @foreach($categories as $cat)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category" id="cat_{{ $cat->id }}" value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label small d-flex justify-content-between align-items-center" for="cat_{{ $cat->id }}">
                                        <span>{{ $cat->name }}</span>
                                        <span class="text-muted" style="font-size: 0.75rem;">({{ $cat->products_count ?? $cat->products()->count() }})</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr class="text-muted opacity-25">

                    {{-- Price Range --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase mb-2">Price Range (₹)</label>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="{{ request('min_price') }}">
                            </div>
                            <div class="col-6">
                                <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="{{ request('max_price') }}">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-dark btn-sm w-100 fw-semibold">Apply Price</button>
                    </div>

                    <hr class="text-muted opacity-25">

                    {{-- Availability & Special Flags --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small text-uppercase mb-2">Preferences</label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="in_stock" id="in_stock" value="1" {{ request('in_stock') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label small" for="in_stock">In Stock Only</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="featured" id="featured" value="1" {{ request('featured') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                            <label class="form-check-label small" for="featured">Featured / Best Sellers</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Products Grid & Controls --}}
        <div class="col-lg-9">
            {{-- Top Bar: Count & Sort --}}
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 bg-white rounded-3 border mb-4">
                <div>
                    <span class="fw-bold text-dark">{{ $products->total() }}</span>
                    <span class="text-muted small">sacred items found</span>
                    @if(request('search'))
                        <span class="badge bg-secondary ms-2">Keyword: "{{ request('search') }}"</span>
                    @endif
                </div>

                {{-- Sort Dropdown --}}
                <div class="d-flex align-items-center gap-2">
                    <label for="sortSelector" class="small text-muted fw-semibold mb-0 text-nowrap">Sort By:</label>
                    <select id="sortSelector" class="form-select form-select-sm" style="min-width: 175px;" onchange="window.location.href = this.value">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'featured']) }}" {{ request('sort', 'featured') == 'featured' ? 'selected' : '' }}>Featured / Recommended</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Additions</option>
                    </select>
                </div>
            </div>

            {{-- Products Grid --}}
            @if($products->count() > 0)
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-5">
                    @foreach($products as $product)
                        <div class="col">
                            @include('components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5 bg-white rounded-4 border p-4">
                    <div class="mb-3 text-warning">
                        <i class="bi bi-gem" style="font-size: 3.5rem;"></i>
                    </div>
                    <h4 class="fw-bold text-dark mb-2">No Sacred Items Found</h4>
                    <p class="text-muted mb-4" style="max-width: 480px; margin: 0 auto;">
                        We couldn't find any products matching your specific filters. Try expanding your price range or clearing search keywords.
                    </p>
                    <a href="{{ route('shop.index') }}" class="btn btn-warning fw-bold px-4 py-2">
                        View All Spiritual Items
                    </a>
                </div>
            @endif

            {{-- Trust Badges Banner --}}
            <div class="trust-strip mt-5">
                <div class="row g-4 text-center">
                    <div class="col-md-3 col-6">
                        <i class="bi bi-patch-check-fill text-warning fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">100% Authentic</h6>
                        <p class="text-muted small mb-0">Certified gemstones & genuine Himalayan beads</p>
                    </div>
                    <div class="col-md-3 col-6">
                        <i class="bi bi-fire text-danger fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Prana Pratishtha</h6>
                        <p class="text-muted small mb-0">Energized by trained Vedic Pandits</p>
                    </div>
                    <div class="col-md-3 col-6">
                        <i class="bi bi-truck text-primary fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Safe Delivery</h6>
                        <p class="text-muted small mb-0">Insured express door-step transit</p>
                    </div>
                    <div class="col-md-3 col-6">
                        <i class="bi bi-shield-lock-fill text-success fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Astrologer Recommended</h6>
                        <p class="text-muted small mb-0">Prescribed by verified Vedic experts</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
