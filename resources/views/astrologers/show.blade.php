@extends('layouts.app')

@section('title', $astrologer->display_name . ' — Astrologer Profile — AstroVani')
@section('meta_description', $astrologer->short_bio ?? 'Book a consultation with ' . $astrologer->display_name . ' on AstroVani.')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 100%);
        color: #fff;
        padding: 50px 0 40px;
        position: relative;
    }
    .profile-header::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 40px;
        background: var(--astro-bg-cream);
        clip-path: ellipse(55% 100% at 50% 100%);
    }
    .profile-avatar-lg {
        width: 110px; height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(245,176,65,0.6);
    }
    .profile-avatar-lg-placeholder {
        width: 110px; height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg,#6C3483,#1A0B2E);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.8rem; font-weight: 700; color: #F5B041;
        border: 4px solid rgba(245,176,65,0.5);
        flex-shrink: 0;
    }
    .star-rating { color: #F5B041; }
    .spec-pill { padding: 4px 12px; background: rgba(245,176,65,0.15); color: #F5B041; border-radius: 20px; font-size: 0.8rem; font-weight: 500; }
    .lang-pill { padding: 4px 12px; background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.8); border-radius: 20px; font-size: 0.8rem; }
    .profile-tabs .nav-link { color: #6B7280; font-weight: 500; padding: 12px 20px; border: none; border-bottom: 2px solid transparent; border-radius: 0; }
    .profile-tabs .nav-link.active { color: var(--astro-purple); border-bottom-color: var(--astro-purple); background: transparent; }
    .profile-tabs .nav-link:hover { color: var(--astro-purple); background: rgba(108,52,131,0.04); }
    .sticky-booking { position: sticky; top: 80px; }
    .booking-card { background: #fff; border-radius: 16px; border: 1px solid #E5E7EB; overflow: hidden; box-shadow: 0 4px 20px rgba(26,11,46,0.08); }
    .booking-card-header { background: linear-gradient(135deg, #2D124D, #4A1E80); color: #fff; padding: 20px; }
    .rate-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #F3F4F6; }
    .rate-row:last-child { border-bottom: none; }
    .rate-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
    .btn-book-chat { background: var(--astro-purple); color: #fff; border: none; border-radius: 10px; padding: 12px; font-weight: 700; font-size: 1rem; width: 100%; transition: all 0.2s; }
    .btn-book-chat:hover { background: var(--astro-purple-light); }
    .btn-book-call { background: #10B981; color: #fff; border: none; border-radius: 10px; padding: 10px; font-weight: 600; font-size: 0.9rem; width: 100%; transition: all 0.2s; }
    .btn-book-call:hover { background: #059669; }
    .btn-book-video { background: #F5B041; color: #1A0B2E; border: none; border-radius: 10px; padding: 10px; font-weight: 600; font-size: 0.9rem; width: 100%; transition: all 0.2s; }
    .btn-book-video:hover { background: #D4AC0D; }
    .availability-slot { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px 14px; }
    .about-content { font-size: 0.95rem; line-height: 1.8; color: #374151; }
    .related-card { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; padding: 16px; text-decoration: none; color: inherit; display: block; transition: all 0.2s; }
    .related-card:hover { box-shadow: 0 4px 16px rgba(26,11,46,0.08); transform: translateY(-2px); color: inherit; }
    .related-avatar { width: 52px; height: 52px; border-radius: 50%; object-fit: cover; }
    .related-avatar-ph { width: 52px; height: 52px; border-radius: 50%; background: linear-gradient(135deg,#2D124D,#6C3483); display: flex; align-items: center; justify-content: center; color: #F5B041; font-weight: 700; flex-shrink: 0; }
</style>
@endpush

@section('content')

{{-- Profile Header --}}
<div class="profile-header">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-4">
            <div class="flex-shrink-0">
                @if($astrologer->profile_image)
                    <img src="{{ asset('storage/' . $astrologer->profile_image) }}" alt="{{ $astrologer->display_name }}" class="profile-avatar-lg">
                @else
                    <div class="profile-avatar-lg-placeholder">{{ strtoupper(substr($astrologer->display_name, 0, 1)) }}</div>
                @endif
            </div>
            <div>
                <div class="d-flex align-items-center gap-3 flex-wrap mb-1">
                    <h1 style="font-family:'Outfit',sans-serif;font-size:1.9rem;" class="mb-0 fw-bold">{{ $astrologer->display_name }}</h1>
                    @if($astrologer->is_available)
                        <span style="background:rgba(16,185,129,0.25);color:#6EE7B7;padding:4px 12px;border-radius:20px;font-size:0.78rem;font-weight:600;"><i class="bi bi-circle-fill me-1" style="font-size:0.55rem;"></i>Online Now</span>
                    @endif
                    @if($astrologer->is_featured)
                        <span style="background:rgba(245,176,65,0.2);color:#F5B041;padding:4px 10px;border-radius:20px;font-size:0.78rem;font-weight:600;"><i class="bi bi-patch-check-fill me-1"></i>Top Astrologer</span>
                    @endif
                </div>

                {{-- Specializations --}}
                <div class="mb-2 d-flex flex-wrap gap-2">
                    @foreach($astrologer->specializations_array as $spec)
                        <span class="spec-pill">{{ $spec }}</span>
                    @endforeach
                </div>

                {{-- Languages --}}
                @if($astrologer->languages)
                    <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                        <i class="bi bi-translate opacity-75"></i>
                        @foreach($astrologer->languages_array as $lang)
                            <span class="lang-pill">{{ $lang }}</span>
                        @endforeach
                    </div>
                @endif

                {{-- Stats --}}
                <div class="d-flex flex-wrap gap-4 mt-2" style="font-size:0.88rem;opacity:0.85;">
                    <div>
                        <div class="star-rating">
                            @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ $i<=round($astrologer->rating_avg)?'-fill':'' }}"></i>
                            @endfor
                        </div>
                        <div>{{ number_format($astrologer->rating_avg,1) }} ({{ number_format($astrologer->total_reviews) }} reviews)</div>
                    </div>
                    <div><i class="bi bi-briefcase me-1"></i>{{ $astrologer->experience_years }} yrs experience</div>
                    <div><i class="bi bi-people me-1"></i>{{ number_format($astrologer->total_consultations) }}+ consultations</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Page Body --}}
<div class="container" style="margin-top: 40px; padding-bottom: 60px;">
    <div class="row g-4">

        {{-- Left: Tabbed Content --}}
        <div class="col-lg-8">

            {{-- Tab Nav --}}
            <ul class="nav profile-tabs border-bottom mb-4" id="profileTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-about"><i class="bi bi-person me-1"></i>About</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-services"><i class="bi bi-gem me-1"></i>Services</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-availability"><i class="bi bi-calendar3 me-1"></i>Schedule</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-reviews"><i class="bi bi-chat-square-dots me-1"></i>Reviews</button></li>
            </ul>

            <div class="tab-content">

                {{-- About Tab --}}
                <div class="tab-pane fade show active" id="tab-about">
                    @if($astrologer->bio)
                        <div class="about-content mb-4">{{ $astrologer->bio }}</div>
                    @else
                        <p class="text-muted">No biography available yet.</p>
                    @endif

                    <div class="row g-3 mb-4">
                        @if($astrologer->education)
                            <div class="col-md-6">
                                <div class="p-3 border rounded-3">
                                    <div class="text-muted small mb-1"><i class="bi bi-mortarboard me-1"></i>Education</div>
                                    <div class="fw-semibold">{{ $astrologer->education }}</div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3">
                                <div class="text-muted small mb-1"><i class="bi bi-briefcase me-1"></i>Experience</div>
                                <div class="fw-semibold">{{ $astrologer->experience_years }} Years</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Services Tab --}}
                <div class="tab-pane fade" id="tab-services">
                    @if($astrologer->services->isEmpty())
                        <p class="text-muted">No services listed.</p>
                    @else
                        <div class="row g-3">
                            @foreach($astrologer->services as $svc)
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 h-100 d-flex align-items-start gap-3">
                                        <div style="width:44px;height:44px;border-radius:10px;background:linear-gradient(135deg,rgba(108,52,131,0.1),rgba(26,11,46,0.05));display:flex;align-items:center;justify-content:center;font-size:1.3rem;color:#6C3483;flex-shrink:0;">
                                            <i class="{{ $svc->icon ?? 'bi-gem' }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $svc->name }}</div>
                                            @if($svc->short_description)<div class="text-muted small mt-1">{{ $svc->short_description }}</div>@endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Availability Tab --}}
                <div class="tab-pane fade" id="tab-availability">
                    @if($astrologer->availability->isEmpty())
                        <div class="text-muted py-3 text-center">
                            <i class="bi bi-calendar-x display-6 d-block mb-2 opacity-25"></i>
                            No schedule available. Contact for custom booking.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($astrologer->availability as $slot)
                                <div class="col-md-6">
                                    <div class="availability-slot d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">{{ $slot->day_label }}</span>
                                        <span class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Reviews Tab --}}
                <div class="tab-pane fade" id="tab-reviews">
                    <div class="text-center py-4 border-bottom mb-4">
                        <div class="display-5 fw-bold" style="color:var(--astro-purple);">{{ number_format($astrologer->rating_avg, 1) }}</div>
                        <div class="star-rating fs-4 mb-1">
                            @for($i=1;$i<=5;$i++)
                                <i class="bi bi-star{{ $i<=round($astrologer->rating_avg)?'-fill':'' }}"></i>
                            @endfor
                        </div>
                        <div class="text-muted small">Based on {{ number_format($astrologer->total_reviews) }} reviews</div>
                    </div>

                    @forelse($astrologer->reviews as $review)
                        <article class="border rounded-3 p-3 mb-3 bg-white">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">Verified customer</div>
                                    <div class="text-success small"><i class="bi bi-patch-check-fill me-1"></i>Completed consultation</div>
                                </div>
                                <time class="text-muted small" datetime="{{ $review->created_at->toDateString() }}">{{ $review->created_at->format('d M Y') }}</time>
                            </div>
                            <div class="star-rating my-2" aria-label="Rated {{ $review->rating }} out of 5 stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill' : '' }}" aria-hidden="true"></i>
                                @endfor
                            </div>
                            <p class="mb-0 text-secondary" style="white-space: pre-line;">{{ $review->body }}</p>
                        </article>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-chat-square-text fs-3 d-block mb-2"></i>
                            No written reviews yet. Reviews from completed consultations will appear here.
                        </div>
                    @endforelse
                </div>

            </div>

            {{-- Related Astrologers --}}
            @if($related->isNotEmpty())
                <div class="mt-5">
                    <h5 class="fw-bold mb-3" style="font-family:'Outfit',sans-serif;">Similar Astrologers</h5>
                    <div class="row g-3">
                        @foreach($related as $r)
                            <div class="col-md-6">
                                <a href="{{ route('astrologers.show', $r->slug) }}" class="related-card">
                                    <div class="d-flex align-items-center gap-3">
                                        @if($r->profile_image)
                                            <img src="{{ asset('storage/' . $r->profile_image) }}" class="related-avatar" alt="{{ $r->display_name }}">
                                        @else
                                            <div class="related-avatar-ph">{{ strtoupper(substr($r->display_name,0,1)) }}</div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">{{ $r->display_name }}</div>
                                            <div class="text-muted small">{{ Str::limit($r->specializations, 40) }}</div>
                                            <div class="star-rating small">
                                                @for($i=1;$i<=5;$i++)<i class="bi bi-star{{ $i<=round($r->rating_avg)?'-fill':'' }}"></i>@endfor
                                                <span class="text-muted ms-1">{{ number_format($r->rating_avg,1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ms-auto text-end">
                                            <div class="text-muted" style="font-size:0.72rem;">Chat</div>
                                            <div class="fw-bold small">₹{{ number_format($r->chat_rate,0) }}/min</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Right: Booking Card --}}
        <div class="col-lg-4">
            <div class="sticky-booking">
                <div class="booking-card">
                    <div class="booking-card-header">
                        <div class="fw-bold fs-6 mb-1">Book a Consultation</div>
                        <div class="small opacity-75">
                            @if($astrologer->is_available)
                                <i class="bi bi-circle-fill me-1 text-success" style="font-size:0.6rem;"></i>Available now for instant connect
                            @else
                                <i class="bi bi-clock me-1"></i>Schedule a session
                            @endif
                        </div>
                    </div>
                    <div class="p-4">
                        {{-- Rates --}}
                        <div class="mb-3">
                            <div class="rate-row">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rate-icon" style="background:rgba(108,52,131,0.1);color:#6C3483;"><i class="bi bi-chat-dots"></i></div>
                                    <span class="fw-semibold small">Chat Consultation</span>
                                </div>
                                <span class="fw-bold">₹{{ number_format($astrologer->chat_rate, 0) }}<span class="text-muted fw-normal" style="font-size:0.75rem;">/min</span></span>
                            </div>
                            <div class="rate-row">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rate-icon" style="background:rgba(16,185,129,0.1);color:#10B981;"><i class="bi bi-telephone"></i></div>
                                    <span class="fw-semibold small">Voice Call</span>
                                </div>
                                <span class="fw-bold">₹{{ number_format($astrologer->call_rate, 0) }}<span class="text-muted fw-normal" style="font-size:0.75rem;">/min</span></span>
                            </div>
                            <div class="rate-row">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rate-icon" style="background:rgba(245,176,65,0.1);color:#D4AC0D;"><i class="bi bi-camera-video"></i></div>
                                    <span class="fw-semibold small">Video Call</span>
                                </div>
                                <span class="fw-bold">₹{{ number_format($astrologer->video_rate, 0) }}<span class="text-muted fw-normal" style="font-size:0.75rem;">/min</span></span>
                            </div>
                        </div>

                        <a href="{{ route('bookings.create', ['astrologer' => $astrologer->slug, 'type' => 'chat']) }}" class="btn-book-chat mb-2 text-decoration-none d-block text-center">
                            <i class="bi bi-chat-dots me-2"></i>Schedule Live Chat — ₹{{ number_format($astrologer->chat_rate,0) }}/min
                        </a>
                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('bookings.create', ['astrologer' => $astrologer->slug, 'type' => 'call']) }}" class="btn-book-call text-decoration-none d-block text-center">
                                    <i class="bi bi-telephone me-1"></i>Voice Call
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('bookings.create', ['astrologer' => $astrologer->slug, 'type' => 'video']) }}" class="btn-book-video text-decoration-none d-block text-center">
                                    <i class="bi bi-camera-video me-1"></i>Video Call
                                </a>
                            </div>
                        </div>

                        {{-- Trust badges --}}
                        <div class="mt-3 pt-3 border-top d-flex flex-wrap gap-2 justify-content-center">
                            <span class="badge bg-light text-dark border"><i class="bi bi-shield-check text-success me-1"></i>Verified</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-lock text-primary me-1"></i>Secure</span>
                            <span class="badge bg-light text-dark border"><i class="bi bi-award text-warning me-1"></i>Certified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
