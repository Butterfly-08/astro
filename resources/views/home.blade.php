@extends('layouts.app')

@section('title', 'AstroVani — Premier Astrology Consultation & Spiritual Store')
@section('meta_description', 'Connect with verified Vedic astrologers, check daily horoscopes, and discover certified gemstones, rudraksha, and sacred puja remedies.')

@push('styles')
<style>
    .hero-banner {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 55%, #481B7F 100%);
        border-bottom: 3px solid #F5B041;
        position: relative;
    }
    .service-tile {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 20px;
        transition: all 0.25s ease;
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
    }
    .service-tile:hover {
        border-color: rgba(245, 176, 65, 0.5);
        box-shadow: 0 10px 25px rgba(26, 11, 46, 0.08);
        transform: translateY(-4px);
    }
    .service-tile-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(108, 52, 131, 0.1), rgba(245, 176, 65, 0.15));
        color: var(--astro-purple);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 14px;
        transition: all 0.25s ease;
    }
    .service-tile:hover .service-tile-icon {
        background: linear-gradient(135deg, #2D124D, #6C3483);
        color: #F5B041;
        transform: scale(1.06);
    }
    .astro-home-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .astro-home-card:hover {
        border-color: rgba(245, 176, 65, 0.5);
        box-shadow: 0 12px 28px rgba(26, 11, 46, 0.1);
        transform: translateY(-4px);
    }
    .astro-home-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #F5B041;
    }
    .astro-home-avatar-ph {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2D124D, #6C3483);
        color: #F5B041;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: 700;
        border: 2px solid #F5B041;
    }
    /* Product Card Styles */
    .product-card { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; overflow: hidden; transition: transform 0.25s ease, box-shadow 0.25s ease; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 14px 34px rgba(26, 11, 46, 0.08); }
    .product-card-img-wrap { height: 200px; background: #F9FAFB; display: flex; align-items: center; justify-content: center; }
    .product-card-img { max-height: 100%; max-width: 100%; object-fit: cover; }
    .product-img-fallback { height: 200px; width: 100%; background: linear-gradient(135deg, #F9FAFB, #F3F4F6); }
    .product-card-title { font-size: 0.95rem; font-weight: 700; }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="py-5 text-white hero-banner">
    <div class="container py-4">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 text-warning mb-3 small fw-semibold border border-warning border-opacity-25">
                    <i class="bi bi-stars"></i> Ancient Wisdom for Modern Destiny
                </div>
                <h1 class="display-4 fw-bold mb-3" style="font-family: 'Outfit', sans-serif; line-height: 1.2;">
                    Guidance for Your Journey
                </h1>
                <p class="lead text-white-50 mb-4" style="font-size: 1.15rem; max-width: 620px;">
                    Connect with verified Vedic astrologers for confidential guidance on career, marriage, health, and personal growth.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold btn-lg px-4 py-2 fw-bold">
                        <i class="bi bi-chat-quote-fill me-2"></i> Talk to an Astrologer
                    </a>
                    <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg px-4 py-2 fw-semibold">
                        <i class="bi bi-gem me-2 text-warning"></i> Explore Services
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="row mt-5 pt-3 border-top border-white border-opacity-10 g-3">
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">{{ $totalAstrologersCount > 0 ? $totalAstrologersCount . '+' : '10+' }}</div>
                        <div class="small text-white-50">Verified Astrologers</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">
                            {{ $totalConsultationsCount > 0 ? number_format($totalConsultationsCount) . '+' : '25,000+' }}
                        </div>
                        <div class="small text-white-50">Consultations Done</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">4.9 ★</div>
                        <div class="small text-white-50">Customer Rating</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 text-center">
                <div class="p-4 p-md-5 rounded-4 shadow-lg position-relative" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(245, 176, 65, 0.25); backdrop-filter: blur(10px);">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 100px; height: 100px; background: linear-gradient(135deg, #F5B041 0%, #D4AC0D 100%); color: #1A0B2E;">
                        <i class="bi bi-brightness-high-fill display-5"></i>
                    </div>
                    <h4 class="text-white fw-bold mb-2">Welcome to <span class="notranslate" translate="no">AstroVani</span></h4>
                    <p class="text-white-50 small mb-4">
                        Vedic Astrology • Kundli Reading • Tarot Cards • Vastu Shastra • Certified Gemstones & Remedies
                    </p>

                    @guest('web')
                        <div class="d-grid gap-2">
                            <a href="{{ route('register') }}" class="btn btn-astro-gold py-2">
                                <i class="bi bi-person-plus me-1"></i> New User? Create Free Account
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light py-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Already Registered? Sign In
                            </a>
                        </div>
                    @else
                        <a href="{{ route('user.dashboard') }}" class="btn btn-astro-gold w-100 py-2">
                            <i class="bi bi-speedometer2 me-1"></i> Access My Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Consultation Services -->
@if($services->isNotEmpty())
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 mb-2">Popular Disciplines</span>
                <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Astrological Consultation Services</h2>
                <p class="text-muted mb-0 small">Specialized fields analyzed by certified Vedic masters and occult scholars</p>
            </div>
            <a href="{{ route('services.index') }}" class="btn btn-outline-primary btn-sm fw-semibold">
                View All Services <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            @foreach($services as $svc)
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('services.show', $svc->slug) }}" class="service-tile">
                        <div class="service-tile-icon">
                            <i class="{{ $svc->icon ?? 'bi-gem' }}"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">{{ $svc->name }}</h6>
                        <p class="text-muted small mb-3" style="font-size: 0.8rem; line-height: 1.4;">
                            {{ Str::limit($svc->short_description ?? $svc->description, 75) }}
                        </p>
                        <div class="d-flex justify-content-between align-items-center text-muted small pt-2 border-top" style="font-size: 0.75rem;">
                            <span><i class="bi bi-person-check text-success me-1"></i>{{ $svc->astrologers_count }} Experts</span>
                            <span class="text-primary fw-semibold">Consult <i class="bi bi-chevron-right"></i></span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Top Verified Astrologers -->
@if($featuredAstrologers->isNotEmpty())
<section class="py-5" style="background-color: #F8F9FA;">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 mb-2">Verified Experts</span>
                <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Consult India's Top Astrologers</h2>
                <p class="text-muted mb-0 small">Direct one-on-one private chat, audio call, or video consultation</p>
            </div>
            <a href="{{ route('astrologers.index') }}" class="btn btn-outline-primary btn-sm fw-semibold">
                Explore All Astrologers <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            @foreach($featuredAstrologers as $astro)
                <div class="col-sm-6 col-lg-3">
                    <div class="astro-home-card p-3">
                        <div class="d-flex gap-3 align-items-center mb-3">
                            <div class="position-relative">
                                @if($astro->profile_image)
                                    <img src="{{ asset('storage/' . $astro->profile_image) }}" alt="{{ $astro->display_name }}" class="astro-home-avatar">
                                @else
                                    <div class="astro-home-avatar-ph">
                                        {{ strtoupper(substr($astro->display_name, 0, 1)) }}
                                    </div>
                                @endif
                                @if($astro->is_available)
                                    <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" title="Online Now"></span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-grow-1">
                                <h6 class="fw-bold text-dark text-truncate mb-0">
                                    <a href="{{ route('astrologers.show', $astro->slug) }}" class="text-dark text-decoration-none">
                                        {{ $astro->display_name }}
                                    </a>
                                </h6>
                                <div class="text-muted small text-truncate">{{ $astro->specializations }}</div>
                                <div class="d-flex align-items-center gap-1 mt-1">
                                    <span class="badge bg-warning text-dark" style="font-size:0.7rem;">★ {{ number_format($astro->rating_avg, 1) }}</span>
                                    <span class="small text-muted" style="font-size: 0.75rem;">({{ number_format($astro->total_reviews) }})</span>
                                </div>
                            </div>
                        </div>

                        <div class="small text-muted mb-3 flex-grow-1" style="font-size: 0.82rem; line-height: 1.45;">
                            {{ Str::limit($astro->short_bio ?? $astro->bio, 85) }}
                        </div>

                        <div class="pt-2 border-top d-flex justify-content-between align-items-center mt-auto">
                            <div>
                                <div class="text-muted" style="font-size: 0.72rem;">Starting From</div>
                                <div class="fw-bold text-dark fs-6">₹{{ number_format($astro->chat_rate) }}<small class="text-muted fw-normal">/min</small></div>
                            </div>
                            <a href="{{ route('astrologers.show', $astro->slug) }}" class="btn btn-astro-gold btn-sm px-3 py-1 fw-semibold">
                                <i class="bi bi-chat-fill me-1"></i> Consult
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Spiritual E-Store & Sacred Remedies -->
@if(isset($featuredProducts) && $featuredProducts->isNotEmpty())
<section class="py-5" style="background-color: #FFFFFF;">
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-2">
            <div>
                <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 mb-2">
                    <i class="bi bi-shield-check text-warning me-1"></i>100% Vedic Energized
                </span>
                <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Spiritual Store & Astrological Remedies</h2>
                <p class="text-muted mb-0 small">Lab certified gemstones, authentic Himalayan rudraksha, and consecrated copper yantras</p>
            </div>
            <a href="{{ route('shop.index') }}" class="btn btn-outline-primary btn-sm fw-semibold">
                Visit Spiritual Shop <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        {{-- Categories Bar if present --}}
        @if(isset($shopCategories) && $shopCategories->isNotEmpty())
            <div class="d-flex gap-2 overflow-auto pb-3 mb-4" style="scrollbar-width: thin;">
                <a href="{{ route('shop.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 fw-semibold text-nowrap">
                    All Products
                </a>
                @foreach($shopCategories as $scat)
                    <a href="{{ route('shop.category', $scat->slug) }}" class="btn btn-light btn-sm rounded-pill px-3 border fw-semibold text-nowrap">
                        @if($scat->icon)<i class="{{ $scat->icon }} me-1 text-primary"></i>@endif
                        {{ $scat->name }}
                        @if($scat->active_products_count > 0)
                            <span class="badge bg-secondary bg-opacity-10 text-dark ms-1">{{ $scat->active_products_count }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Products Grid --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            @foreach($featuredProducts as $product)
                <div class="col">
                    @include('components.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Core Pillars Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 mb-2">Authentic Spiritual Services</span>
            <h2 class="fw-bold text-dark" style="font-family: 'Outfit', sans-serif;">Why Choose <span class="notranslate" translate="no">AstroVani</span>?</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                Experience genuine Vedic consultations paired with ethically sourced, lab-certified astrology remedies.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 rounded-4 border bg-light h-100 text-center">
                    <div class="rounded-circle bg-warning bg-opacity-25 text-warning d-inline-flex align-items-center justify-content-center mb-3" style="width: 65px; height: 65px;">
                        <i class="bi bi-patch-check-fill fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Verified Astrologers</h5>
                    <p class="text-muted small mb-0">
                        Every astrologer undergoes multi-tiered verification covering qualifications, Vedic expertise, and consultation ethics.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 rounded-4 border bg-light h-100 text-center">
                    <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 65px; height: 65px; background-color: #2D124D;">
                        <i class="bi bi-shield-lock-fill fs-2 text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">100% Confidential</h5>
                    <p class="text-muted small mb-0">
                        Your birth details, questions, and personal consultation history are kept strictly confidential with bank-grade security.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="p-4 rounded-4 border bg-light h-100 text-center">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3" style="width: 65px; height: 65px;">
                        <i class="bi bi-gem fs-2"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">Lab Certified Remedies</h5>
                    <p class="text-muted small mb-0">
                        Authentic natural gemstones, energized rudrakshas, and sanctified yantras delivered safely to your doorstep.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Spiritual Guidance Callout Banner -->
<section class="py-5" style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 100%); color: white;">
    <div class="container text-center py-2">
        <span class="badge bg-warning text-dark px-3 py-1 mb-3 fw-bold">Live Astrological Guidance</span>
        <h2 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">Have Questions About Your Future?</h2>
        <p class="text-white-50 lead mx-auto mb-4" style="max-width: 700px; font-size: 1.05rem;">
            Whether dealing with career uncertainty, marriage delays, or financial challenges, our astrologers provide actionable Vedic remedies and auspicious timing (Muhurat).
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold btn-lg px-4 py-2 fw-bold">
                <i class="bi bi-chat-dots-fill me-2"></i> Connect With Astrologer Now
            </a>
            <a href="{{ route('services.index') }}" class="btn btn-outline-light btn-lg px-4 py-2 fw-semibold">
                <i class="bi bi-compass-fill me-2"></i> Browse All Disciplines
            </a>
        </div>
    </div>
</section>
@endsection
