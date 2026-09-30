@extends('layouts.app')

@section('title', "{$service->name} Consultation — AstroVani")
@section('meta_description', Str::limit($service->description ?? $service->short_description, 160))

@push('styles')
<style>
    .service-detail-hero {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%);
        border-bottom: 3px solid #F5B041;
        padding: 50px 0 40px;
        color: white;
    }
    .service-hero-icon {
        width: 80px;
        height: 80px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(245, 176, 65, 0.25), rgba(255, 255, 255, 0.1));
        border: 1px solid rgba(245, 176, 65, 0.4);
        color: #F5B041;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.4rem;
    }
    .astrologer-card {
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        background: #FFFFFF;
        transition: all 0.25s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .astrologer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(26, 11, 46, 0.08);
        border-color: rgba(245, 176, 65, 0.4);
    }
    .astro-avatar {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #F5B041;
    }
    .astro-avatar-ph {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2D124D, #6C3483);
        color: #F5B041;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.5rem;
        border: 2px solid #F5B041;
    }
    .sidebar-svc-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 10px;
        text-decoration: none;
        color: #374151;
        transition: background 0.2s ease;
    }
    .sidebar-svc-link:hover {
        background: #F3E8FF;
        color: var(--astro-purple);
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<div class="service-detail-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: 0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-white-50 text-decoration-none">Services</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page">{{ $service->name }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-4 mt-3 flex-wrap">
            <div class="service-hero-icon flex-shrink-0">
                <i class="{{ $service->icon ?? 'bi-gem' }}"></i>
            </div>
            <div>
                <span class="badge bg-white bg-opacity-10 text-warning px-3 py-1 mb-2 border border-warning border-opacity-25 text-uppercase">
                    {{ str_replace('_', ' ', $service->type ?? 'Consultation') }}
                </span>
                <h1 class="fw-bold mb-1 display-6" style="font-family: 'Outfit', sans-serif;">{{ $service->name }} Consultation</h1>
                <p class="text-white-50 mb-0" style="max-width: 650px;">
                    {{ $service->short_description ?? 'Connect with verified masters specializing in ' . $service->name . ' for accurate guidance.' }}
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row g-4">
        <!-- Main Content: Description & Astrologers -->
        <div class="col-lg-8">
            {{-- About Service --}}
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                <h4 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif; color: var(--astro-purple);">
                    About {{ $service->name }}
                </h4>
                <div class="text-secondary" style="line-height: 1.7;">
                    {!! nl2br(e($service->description ?? $service->short_description)) !!}
                </div>
            </div>

            {{-- Astrologers offering this service --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0" style="font-family: 'Outfit', sans-serif;">
                    <i class="bi bi-star-fill text-warning me-2"></i>Verified {{ $service->name }} Astrologers
                    <span class="text-muted fw-normal fs-6">({{ $astrologers->total() }})</span>
                </h5>
            </div>

            @if($astrologers->isEmpty())
                <div class="card border-0 shadow-sm p-4 text-center text-muted" style="border-radius: 14px;">
                    <i class="bi bi-person-x display-5 opacity-25 d-block mb-2"></i>
                    <h6>No astrologers are currently listed for this specific service.</h6>
                    <p class="small mb-3">Browse our full astrologer directory to find verified experts available today.</p>
                    <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold btn-sm d-inline-block">View All Astrologers</a>
                </div>
            @else
                <div class="row g-3">
                    @foreach($astrologers as $astro)
                        <div class="col-md-6">
                            <div class="astrologer-card p-3">
                                <div class="d-flex gap-3 align-items-start mb-2">
                                    @if($astro->profile_image)
                                        <img src="{{ asset('storage/' . $astro->profile_image) }}" alt="{{ $astro->display_name }}" class="astro-avatar flex-shrink-0">
                                    @else
                                        <div class="astro-avatar-ph flex-shrink-0">
                                            {{ strtoupper(substr($astro->display_name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="d-flex align-items-center gap-1">
                                            <h6 class="fw-bold mb-0 text-truncate text-dark">
                                                <a href="{{ route('astrologers.show', $astro->slug) }}" class="text-dark text-decoration-none">
                                                    {{ $astro->display_name }}
                                                </a>
                                            </h6>
                                            @if($astro->is_verified)
                                                <i class="bi bi-patch-check-fill text-primary" title="Verified Astrologer"></i>
                                            @endif
                                        </div>
                                        <div class="small text-muted text-truncate mt-1">{{ $astro->specializations }}</div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-warning text-dark" style="font-size:0.7rem;">
                                                ★ {{ number_format($astro->rating_avg, 1) }}
                                            </span>
                                            <span class="small text-muted">{{ $astro->experience_years }}+ yrs exp</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 border-top mt-auto d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-muted small">Chat/Call:</span>
                                        <span class="fw-bold text-dark">₹{{ number_format($astro->chat_rate) }}/m</span>
                                    </div>
                                    <a href="{{ route('astrologers.show', $astro->slug) }}" class="btn btn-astro-gold btn-sm py-1 px-3">
                                        Consult <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $astrologers->links() }}
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            {{-- Quick action card --}}
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px; background: linear-gradient(135deg, rgba(26,11,46,0.03), rgba(245,176,65,0.08)); border: 1px solid rgba(245,176,65,0.2) !important;">
                <h5 class="fw-bold mb-2 text-dark" style="font-family: 'Outfit', sans-serif;">Need Instant Advice?</h5>
                <p class="text-muted small mb-3">Filter astrologers by language, consultation fee, or immediate online availability.</p>
                <a href="{{ route('astrologers.index', ['service_id' => $service->id]) }}" class="btn btn-astro-gold w-100 py-2 fw-semibold">
                    <i class="bi bi-filter me-1"></i> View Available Experts
                </a>
            </div>

            {{-- Other Services --}}
            <div class="card border-0 shadow-sm p-3" style="border-radius: 14px;">
                <h6 class="fw-bold p-2 mb-2 text-dark border-bottom">Explore Other Services</h6>
                <div class="d-flex flex-column gap-1">
                    @foreach($otherServices as $other)
                        <a href="{{ route('services.show', $other->slug) }}" class="sidebar-svc-link">
                            <i class="{{ $other->icon ?? 'bi-gem' }} text-purple fs-5"></i>
                            <span class="small fw-semibold flex-grow-1">{{ $other->name }}</span>
                            <i class="bi bi-chevron-right text-muted small"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
