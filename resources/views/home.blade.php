@extends('layouts.app')

@section('title', 'AstroVani — Premier Astrology Consultation & Spiritual Store')
@section('meta_description', 'Connect with verified Vedic astrologers, check daily horoscopes, and discover certified gemstones, rudraksha, and sacred puja remedies.')

@section('content')
<!-- Hero Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 55%, #481B7F 100%); border-bottom: 3px solid #F5B041;">
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
                    Connect with astrologers, explore personalized services, and discover astrology-inspired products.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#astrologers" class="btn btn-astro-gold btn-lg px-4 py-2 fw-bold">
                        <i class="bi bi-chat-quote-fill me-2"></i> Talk to an Astrologer
                    </a>
                    <a href="#shop" class="btn btn-outline-light btn-lg px-4 py-2 fw-semibold">
                        <i class="bi bi-bag-heart-fill me-2 text-warning"></i> Explore Shop
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="row mt-5 pt-3 border-top border-white border-opacity-10 g-3">
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">50+</div>
                        <div class="small text-white-50">Verified Astrologers</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">100%</div>
                        <div class="small text-white-50">Private & Confidential</div>
                    </div>
                    <div class="col-4">
                        <div class="fw-bold fs-4 text-warning">4.9 ★</div>
                        <div class="small text-white-50">Customer Rating</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 text-center">
                <div class="p-4 p-md-5 rounded-4 shadow-lg position-relative" style="background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(245, 176, 65, 0.25); backdrop-filter: blur(10px);">
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow" style="width: 110px; height: 110px; background: linear-gradient(135deg, #F5B041 0%, #D4AC0D 100%); color: #1A0B2E;">
                        <i class="bi bi-brightness-high-fill display-4"></i>
                    </div>
                    <h4 class="text-white fw-bold mb-2">Welcome to AstroVani</h4>
                    <p class="text-white-50 small mb-4">
                        Vedic Astrology • Kundli Reading • Tarot Cards • Vastu Shastra • Certified Gemstones & Sacred Rudraksha
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
                            <i class="bi bi-speedometer2 me-1"></i> Access My Customer Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Core Pillars Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center mb-5">
            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning px-3 py-1 mb-2">Authentic Spiritual Services</span>
            <h2 class="fw-bold text-dark">Why Choose AstroVani?</h2>
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
                    <div class="rounded-circle bg-purple text-white d-inline-flex align-items-center justify-content-center mb-3" style="width: 65px; height: 65px; background-color: #2D124D;">
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

<!-- Phase Roadmap Information Banner -->
<section class="py-4 border-top border-bottom bg-light">
    <div class="container text-center">
        <span class="badge bg-dark px-3 py-1 mb-2">Platform Architecture Roadmap</span>
        <h5 class="fw-bold mb-2">Phase 1 Complete: Dual-Authentication Foundation Active</h5>
        <p class="text-muted small mb-0 mx-auto" style="max-width: 750px;">
            The robust authentication core with distinct Admin & Customer guards is active. Next phases will roll out verified Astrologer profiles & appointment booking with double-booking prevention, shop catalog, cart, orders, and REST APIs.
        </p>
    </div>
</section>
@endsection
