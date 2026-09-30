@extends('layouts.app')

@section('title', 'My Consultation Appointments — AstroVani')

@push('styles')
<style>
    .booking-row-card {
        border: 1px solid #E5E7EB;
        border-radius: 12px;
        background: #FFFFFF;
        transition: all 0.2s ease;
        padding: 16px 20px;
    }
    .booking-row-card:hover {
        border-color: rgba(245, 176, 65, 0.5);
        box-shadow: 0 4px 16px rgba(26, 11, 46, 0.05);
    }
    .astro-thumb {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #F5B041;
    }
    .astro-thumb-ph {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2D124D, #6C3483);
        color: #F5B041;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        border: 2px solid #F5B041;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2" style="font-size: 0.82rem;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-muted">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">My Consultations</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">My Consultation Bookings</h2>
            <p class="text-muted small mb-0">Track upcoming appointments, past session summaries, and consultation history.</p>
        </div>
        <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold btn-sm px-3 py-2 fw-semibold">
            <i class="bi bi-plus-circle me-1"></i> Book New Consultation
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 border">
                <span class="text-muted small text-uppercase fw-semibold">Total Bookings</span>
                <h4 class="fw-bold my-1 text-dark">{{ $stats['total'] }}</h4>
                <small class="text-muted">Lifetime</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 border">
                <span class="text-muted small text-uppercase fw-semibold">Upcoming</span>
                <h4 class="fw-bold my-1 text-success">{{ $stats['upcoming'] }}</h4>
                <small class="text-success"><i class="bi bi-clock-history me-1"></i>Scheduled</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 border">
                <span class="text-muted small text-uppercase fw-semibold">Completed</span>
                <h4 class="fw-bold my-1 text-primary">{{ $stats['completed'] }}</h4>
                <small class="text-muted">Sessions Done</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-3 bg-white rounded-3 border">
                <span class="text-muted small text-uppercase fw-semibold">Cancelled</span>
                <h4 class="fw-bold my-1 text-secondary">{{ $stats['cancelled'] }}</h4>
                <small class="text-muted">Appointments</small>
            </div>
        </div>
    </div>

    {{-- Filter Nav Pills --}}
    <ul class="nav nav-pills mb-4 border-bottom pb-3">
        <li class="nav-item">
            <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('user.bookings.index') }}">
                All Bookings
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'upcoming' ? 'active' : '' }}" href="{{ route('user.bookings.index', ['status' => 'upcoming']) }}">
                Upcoming ({{ $stats['upcoming'] }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'completed' ? 'active' : '' }}" href="{{ route('user.bookings.index', ['status' => 'completed']) }}">
                Completed
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('status') === 'cancelled' ? 'active' : '' }}" href="{{ route('user.bookings.index', ['status' => 'cancelled']) }}">
                Cancelled
            </a>
        </li>
    </ul>

    {{-- Bookings List --}}
    @if($bookings->isEmpty())
        <div class="card border-0 shadow-sm p-5 text-center" style="border-radius: 14px;">
            <div class="text-muted mb-3">
                <i class="bi bi-calendar-x display-3 opacity-25"></i>
            </div>
            <h5 class="fw-bold">No consultation bookings found</h5>
            <p class="text-muted small mx-auto mb-4" style="max-width: 450px;">
                You don't have any bookings matching this filter. Schedule a personalized consultation with verified Vedic astrologers today.
            </p>
            <div>
                <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold px-4 py-2 fw-semibold">
                    <i class="bi bi-stars me-1"></i> Browse Verified Astrologers
                </a>
            </div>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($bookings as $booking)
                <div class="booking-row-card">
                    <div class="row align-items-center g-3">
                        {{-- Astrologer Info --}}
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-3">
                                @if($booking->astrologer->profile_image)
                                    <img src="{{ asset('storage/' . $booking->astrologer->profile_image) }}" alt="{{ $booking->astrologer->display_name }}" class="astro-thumb flex-shrink-0">
                                @else
                                    <div class="astro-thumb-ph flex-shrink-0">{{ strtoupper(substr($booking->astrologer->display_name, 0, 1)) }}</div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark">{{ $booking->astrologer->display_name }}</div>
                                    <div class="small text-muted text-capitalize">
                                        <i class="bi bi-{{ $booking->consultation_type === 'video' ? 'camera-video text-warning' : ($booking->consultation_type === 'call' ? 'telephone text-success' : 'chat-dots text-primary') }} me-1"></i>
                                        {{ $booking->consultation_type }} Consultation
                                    </div>
                                    <span class="small text-muted font-monospace" style="font-size:0.75rem;">{{ $booking->booking_number }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Schedule Timing --}}
                        <div class="col-sm-6 col-md-3">
                            <div class="small text-muted"><i class="bi bi-calendar3 me-1"></i>Date</div>
                            <div class="fw-semibold text-dark">{{ $booking->booking_date->format('D, d M Y') }}</div>
                            <div class="small text-secondary"><i class="bi bi-clock me-1"></i>{{ $booking->formatted_time_slot }}</div>
                        </div>

                        {{-- Fee & Status --}}
                        <div class="col-sm-6 col-md-2">
                            <div class="small text-muted">Amount</div>
                            <div class="fw-bold text-dark">₹{{ number_format($booking->amount, 2) }}</div>
                            <div class="mt-1">{!! $booking->status_badge !!}</div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-3 text-md-end">
                            <a href="{{ route('user.bookings.show', $booking) }}" class="btn btn-outline-primary btn-sm px-3 me-1">
                                View Details
                            </a>
                            @if($booking->canBeCancelled())
                                <button type="button" class="btn btn-outline-danger btn-sm px-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#cancelModal{{ $booking->id }}">
                                    Cancel
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Cancellation Modal --}}
                @if($booking->canBeCancelled())
                    <div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1" aria-hidden="true">
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
                                            Are you sure you want to cancel your consultation with <strong>{{ $booking->astrologer->display_name }}</strong> scheduled on <strong>{{ $booking->booking_date->format('d M Y') }} at {{ $booking->formatted_time_slot }}</strong>?
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
            @endforeach

            <div class="mt-4">
                {{ $bookings->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
