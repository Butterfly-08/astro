@extends('layouts.app')

@section('title', 'Astrology Consultation Services — AstroVani')
@section('meta_description', 'Explore our comprehensive range of Vedic, Tarot, KP, Numerology, Vastu, and Gemstone consultation services with verified astrology masters.')

@push('styles')
<style>
    .service-hero {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%);
        border-bottom: 3px solid #F5B041;
        padding: 55px 0 45px;
        color: white;
    }
    .service-card {
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        background: #FFFFFF;
        transition: all 0.28s ease;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(26, 11, 46, 0.1);
        border-color: rgba(245, 176, 65, 0.4);
    }
    .service-icon-box {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(108, 52, 131, 0.12), rgba(245, 176, 65, 0.15));
        color: var(--astro-purple);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.85rem;
        transition: transform 0.25s ease;
    }
    .service-card:hover .service-icon-box {
        transform: scale(1.08) rotate(3deg);
        background: linear-gradient(135deg, #2D124D, #6C3483);
        color: #F5B041;
    }
    .badge-service-type {
        background: rgba(245, 176, 65, 0.15);
        color: #935116;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid rgba(245, 176, 65, 0.3);
    }
</style>
@endpush

@section('content')
<!-- Hero Header -->
<div class="service-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: 0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page">Services</li>
            </ol>
        </nav>
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-white bg-opacity-10 text-warning px-3 py-1 mb-2 border border-warning border-opacity-25">
                    <i class="bi bi-gem me-1"></i> Authentic Consultation Disciplines
                </span>
                <h1 class="fw-bold mb-2 display-6" style="font-family: 'Outfit', sans-serif;">Spiritual & Astrological Services</h1>
                <p class="text-white-50 lead mb-0" style="font-size: 1.05rem; max-width: 650px;">
                    Choose from specialized astrological disciplines guided by certified scholars. Get answers to life, love, career, and wealth questions.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold px-4 py-2 fw-semibold">
                    <i class="bi bi-people-fill me-2"></i> View All Astrologers
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Services Grid -->
<div class="container py-5">
    <div class="row g-4">
        @forelse($services as $service)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="service-card p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="service-icon-box">
                            <i class="{{ $service->icon ?? 'bi-gem' }}"></i>
                        </div>
                        @if($service->type)
                            <span class="badge-service-type text-uppercase">{{ str_replace('_', ' ', $service->type) }}</span>
                        @endif
                    </div>

                    <h5 class="fw-bold mb-2 text-dark" style="font-family: 'Outfit', sans-serif;">
                        <a href="{{ route('services.show', $service->slug) }}" class="text-dark text-decoration-none">
                            {{ $service->name }}
                        </a>
                    </h5>

                    <p class="text-muted small flex-grow-1 mb-3" style="line-height: 1.5;">
                        {{ $service->short_description ?? Str::limit($service->description, 100) }}
                    </p>

                    <div class="pt-3 border-top d-flex justify-content-between align-items-center mb-3">
                        <span class="small text-muted">
                            <i class="bi bi-person-check text-success me-1"></i>
                            <strong>{{ $service->astrologers_count }}</strong> {{ Str::plural('Expert', $service->astrologers_count) }}
                        </span>
                        @if($service->is_featured)
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle small">
                                <i class="bi bi-star-fill text-warning"></i> Popular
                            </span>
                        @endif
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('astrologers.index', ['service_id' => $service->id]) }}" class="btn btn-outline-primary btn-sm py-2">
                            <i class="bi bi-chat-dots me-1"></i> Consult {{ $service->name }}
                        </a>
                        <a href="{{ route('services.show', $service->slug) }}" class="btn btn-light btn-sm text-muted">
                            Learn More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-gem display-4 d-block mb-3 opacity-25"></i>
                    <h5>No consultation services currently available</h5>
                    <p class="small">Please check back soon or browse our verified astrologers directly.</p>
                    <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold mt-2">Explore Astrologers</a>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Bottom Banner -->
<section class="py-5 bg-light border-top">
    <div class="container text-center py-2">
        <h3 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif;">Need Guidance Choosing a Consultation?</h3>
        <p class="text-muted mx-auto mb-4" style="max-width: 600px;">
            Our verified astrology masters are ready 24/7 to provide personalized insights into relationships, career, financial growth, and family harmony.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold px-4 py-2 fw-semibold">
                <i class="bi bi-stars me-2"></i> Find Your Astrologer
            </a>
            <a href="{{ route('register') }}" class="btn btn-outline-dark px-4 py-2 fw-semibold">
                <i class="bi bi-person-plus me-2"></i> Create Free Account
            </a>
        </div>
    </div>
</section>
@endsection
