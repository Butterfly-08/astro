@extends('layouts.app')

@section('title', "Appointment Confirmed — {$booking->booking_number} — AstroVani")

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 p-md-5 text-center" style="border-radius: 16px; border-top: 5px solid #F5B041 !important;">
                
                {{-- Success Icon --}}
                <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px; background: linear-gradient(135deg, #10B981, #059669); color: white; font-size: 2.5rem;">
                    <i class="bi bi-check-lg"></i>
                </div>

                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 mb-2 d-inline-block mx-auto">
                    <i class="bi bi-patch-check-fill me-1"></i> Confirmed Consultation Appointment
                </span>

                <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Your Session Is Confirmed!</h2>
                <p class="text-muted mb-4">
                    Booking Reference: <strong class="text-dark">{{ $booking->booking_number }}</strong>
                </p>

                {{-- Appointment Card --}}
                <div class="p-4 bg-light rounded-3 text-start mb-4 border">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Astrologer</span>
                            <span class="fw-bold fs-6 text-dark">{{ $booking->astrologer->display_name }}</span>
                            <div class="small text-muted">{{ $booking->astrologer->specializations }}</div>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Consultation Mode</span>
                            <span class="fw-bold fs-6 text-dark text-capitalize">
                                <i class="bi bi-{{ $booking->consultation_type === 'video' ? 'camera-video text-warning' : ($booking->consultation_type === 'call' ? 'telephone text-success' : 'chat-dots text-primary') }} me-1"></i>
                                {{ $booking->consultation_type }} Session ({{ $booking->duration_minutes }} Mins)
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Scheduled Date</span>
                            <span class="fw-bold fs-6 text-dark">
                                <i class="bi bi-calendar3 me-1 text-primary"></i>
                                {{ $booking->booking_date->format('l, d F Y') }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <span class="small text-muted text-uppercase fw-semibold d-block">Time Slot</span>
                            <span class="fw-bold fs-6 text-dark">
                                <i class="bi bi-clock me-1 text-warning"></i>
                                {{ $booking->formatted_time_slot }}
                            </span>
                        </div>
                        @if($booking->service)
                            <div class="col-12 pt-2 border-top">
                                <span class="small text-muted text-uppercase fw-semibold d-block">Discipline</span>
                                <span class="fw-semibold text-dark">{{ $booking->service->name }}</span>
                            </div>
                        @endif
                        <div class="col-12 pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="small text-muted text-uppercase fw-semibold">Consultation Fee</span>
                            <span class="fw-bold fs-5 text-dark">₹{{ number_format($booking->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Preparation Instructions --}}
                <div class="text-start p-3 rounded-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 mb-4">
                    <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle-fill text-warning me-2"></i>How to Prepare for Your Consultation:</h6>
                    <ul class="mb-0 small text-secondary ps-3" style="line-height: 1.6;">
                        <li>Keep your birth date, exact birth time, and birth city ready.</li>
                        <li>For voice or video consultations, ensure a stable internet connection in a quiet space.</li>
                        <li>Access your session directly from your customer dashboard at the appointed time.</li>
                    </ul>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-astro-gold px-4 py-2 fw-semibold">
                        <i class="bi bi-calendar-event me-1"></i> View Booking Details
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-dark px-4 py-2">
                        <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
                    </a>
                    <a href="{{ route('astrologers.index') }}" class="btn btn-light px-4 py-2 text-muted">
                        Explore Astrologers
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
