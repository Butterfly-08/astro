@extends('layouts.app')

@section('title', "Consultation {$booking->booking_number} — AstroVani")

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2" style="font-size: 0.82rem;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.bookings.index') }}" class="text-decoration-none text-muted">My Consultations</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $booking->booking_number }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Booking Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
                    <div>
                        <span class="text-muted small">Booking Reference</span>
                        <h4 class="fw-bold mb-0 text-dark font-monospace">{{ $booking->booking_number }}</h4>
                    </div>
                    <div>
                        {!! $booking->status_badge !!}
                    </div>
                </div>

                {{-- Cancellation Alert if cancelled --}}
                @if($booking->status === 'cancelled')
                    <div class="alert alert-secondary mb-4">
                        <div class="fw-bold mb-1"><i class="bi bi-info-circle me-1"></i>Appointment Cancelled</div>
                        <div class="small">
                            Reason: {{ $booking->cancellation_reason ?? 'No reason provided.' }}
                            @if($booking->cancelled_at)
                                &bull; Cancelled on {{ $booking->cancelled_at->format('d M Y, h:i A') }}
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Appointment Details Grid --}}
                <div class="p-3 bg-light rounded-3 mb-4 border">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Consultation Date</span>
                            <span class="fw-bold fs-6 text-dark"><i class="bi bi-calendar-event text-primary me-1"></i>{{ $booking->booking_date->format('l, d F Y') }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Time Slot</span>
                            <span class="fw-bold fs-6 text-dark"><i class="bi bi-clock text-warning me-1"></i>{{ $booking->formatted_time_slot }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Consultation Mode</span>
                            <span class="fw-semibold text-dark text-capitalize">
                                <i class="bi bi-{{ $booking->consultation_type === 'video' ? 'camera-video text-warning' : ($booking->consultation_type === 'call' ? 'telephone text-success' : 'chat-dots text-primary') }} me-1"></i>
                                {{ $booking->consultation_type }} Consultation ({{ $booking->duration_minutes }} Mins)
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Discipline / Topic</span>
                            <span class="fw-semibold text-dark">{{ $booking->service->name ?? 'General Life Guidance' }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Per-Minute Rate</span>
                            <span class="fw-semibold text-dark">₹{{ number_format($booking->rate_per_minute, 2) }}/min</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Total Consultation Fee</span>
                            <span class="fw-bold text-success fs-5">₹{{ number_format($booking->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Customer Submitted Notes / Birth details --}}
                @if($booking->notes)
                    <div class="mb-4">
                        <h6 class="fw-bold text-dark mb-2">Submitted Birth Details & Questions</h6>
                        <div class="p-3 bg-light rounded-3 text-secondary small" style="white-space: pre-line;">
                            {{ $booking->notes }}
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="d-flex justify-content-between align-items-center pt-3 border-top flex-wrap gap-2">
                    <a href="{{ route('user.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to My Bookings
                    </a>

                    @if($booking->canBeCancelled())
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle me-1"></i> Cancel Appointment
                        </button>
                    @endif
                </div>
            </div>

            @if($booking->status === 'completed')
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h5 class="fw-bold mb-1">Consultation review</h5>
                    <p class="text-muted small mb-3">Only the customer who completed this booking can leave a review.</p>

                    @if($booking->review)
                        <div class="d-flex align-items-center gap-1 text-warning mb-2" aria-label="Rated {{ $booking->review->rating }} out of 5 stars">
                            @for($star = 1; $star <= 5; $star++)
                                <i class="bi bi-star{{ $star <= $booking->review->rating ? '-fill' : '' }}" aria-hidden="true"></i>
                            @endfor
                            <span class="text-muted small ms-2">Submitted {{ $booking->review->created_at->format('d M Y') }}</span>
                        </div>
                        <p class="mb-0">{{ $booking->review->body }}</p>
                    @else
                        @error('review')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror
                        <form action="{{ route('user.bookings.review', $booking) }}" method="POST">
                            @csrf
                            <fieldset class="mb-3">
                                <legend class="form-label fw-semibold small">Your rating</legend>
                                <div class="d-flex gap-2" role="radiogroup" aria-label="Rate your consultation">
                                    @for($rating = 1; $rating <= 5; $rating++)
                                        <label class="btn btn-outline-warning">
                                            <input class="visually-hidden" type="radio" name="rating" value="{{ $rating }}" @checked(old('rating', 5) == $rating) required>
                                            <span aria-hidden="true">{{ $rating }} <i class="bi bi-star-fill"></i></span>
                                            <span class="visually-hidden">{{ $rating }} stars</span>
                                        </label>
                                    @endfor
                                </div>
                                @error('rating')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </fieldset>
                            <div class="mb-3">
                                <label for="review-body" class="form-label fw-semibold small">Your review</label>
                                <textarea id="review-body" name="body" class="form-control" rows="4" minlength="10" maxlength="2000" required>{{ old('body') }}</textarea>
                                @error('body')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-astro-primary">
                                <i class="bi bi-send me-1"></i>Submit verified review
                            </button>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        <!-- Right Column: Astrologer Summary -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                <h6 class="fw-bold mb-3 text-muted text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Astrologer Information</h6>

                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($booking->astrologer->profile_image)
                        <img src="{{ asset('storage/' . $booking->astrologer->profile_image) }}" alt="{{ $booking->astrologer->display_name }}" style="width:65px;height:65px;border-radius:50%;object-fit:cover;border:2px solid #F5B041;">
                    @else
                        <div style="width:65px;height:65px;border-radius:50%;background:linear-gradient(135deg,#2D124D,#6C3483);color:#F5B041;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.5rem;border:2px solid #F5B041;">
                            {{ strtoupper(substr($booking->astrologer->display_name,0,1)) }}
                        </div>
                    @endif
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $booking->astrologer->display_name }}</h5>
                        <div class="small text-muted">{{ $booking->astrologer->specializations }}</div>
                        <div class="small text-warning">★ {{ number_format($booking->astrologer->rating_avg, 1) }} ({{ $booking->astrologer->experience_years }}+ yrs exp)</div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('astrologers.show', $booking->astrologer->slug) }}" class="btn btn-outline-primary btn-sm">
                        View Full Profile
                    </a>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4" style="border-radius: 14px; background: rgba(245, 176, 65, 0.08); border: 1px solid rgba(245, 176, 65, 0.25) !important;">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check text-success me-1"></i> Consultation Guarantee</h6>
                <p class="small text-muted mb-0">
                    All AstroVani consultations are covered by our customer privacy guarantee. Your charts and personal discussions remain strictly confidential.
                </p>
            </div>
        </div>
    </div>
</div>

{{-- Cancellation Modal --}}
@if($booking->canBeCancelled())
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px;">
                <form action="{{ route('user.bookings.cancel', $booking) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Cancel Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-start">
                        <p class="text-secondary small mb-3">
                            Are you sure you want to cancel your consultation scheduled for <strong>{{ $booking->booking_date->format('d M Y') }} at {{ $booking->formatted_time_slot }}</strong>?
                        </p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Reason for Cancellation <span class="text-danger">*</span></label>
                            <textarea name="cancellation_reason" class="form-control" rows="3" required placeholder="Please let us know why you need to cancel…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Keep Appointment</button>
                        <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
