@extends('layouts.admin')

@section('title', "Booking {$booking->booking_number}")
@section('page_title', "Manage Consultation {$booking->booking_number}")

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Bookings List
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">
    <!-- Left Column: Details -->
    <div class="col-lg-8">
        {{-- Booking Summary Card --}}
        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 12px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0 text-dark">Appointment Information</h5>
                <div>{!! $booking->status_badge !!}</div>
            </div>

            <div class="row g-3 p-3 bg-light rounded-3 border mb-3">
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Booking Reference</span>
                    <span class="fw-bold fs-6 font-monospace text-dark">{{ $booking->booking_number }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Booked At</span>
                    <span class="fw-semibold text-dark">{{ $booking->created_at->format('d M Y, h:i A') }} ({{ $booking->created_at->diffForHumans() }})</span>
                </div>
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Scheduled Date</span>
                    <span class="fw-bold fs-6 text-primary"><i class="bi bi-calendar3 me-1"></i>{{ $booking->booking_date->format('l, d F Y') }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Time Slot</span>
                    <span class="fw-bold fs-6 text-dark"><i class="bi bi-clock me-1 text-warning"></i>{{ $booking->formatted_time_slot }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Consultation Mode</span>
                    <span class="fw-semibold text-dark text-capitalize">
                        <i class="bi bi-{{ $booking->consultation_type === 'video' ? 'camera-video text-warning' : ($booking->consultation_type === 'call' ? 'telephone text-success' : 'chat-dots text-primary') }} me-1"></i>
                        {{ $booking->consultation_type }} Consultation ({{ $booking->duration_minutes }} Mins)
                    </span>
                </div>
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Discipline Topic</span>
                    <span class="fw-semibold text-dark">{{ $booking->service->name ?? 'General Life Consultation' }}</span>
                </div>
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Applied Rate</span>
                    <span class="fw-semibold text-dark">₹{{ number_format($booking->rate_per_minute, 2) }}/min</span>
                </div>
                <div class="col-sm-6">
                    <span class="small text-muted text-uppercase fw-semibold d-block">Total Consultation Fee</span>
                    <span class="fw-bold text-success fs-5">₹{{ number_format($booking->amount, 2) }}</span>
                </div>
            </div>

            @if($booking->notes)
                <div class="mb-3">
                    <h6 class="fw-bold text-dark mb-1">Customer Birth Details / Inquiry:</h6>
                    <div class="p-3 bg-light rounded-3 text-secondary small" style="white-space: pre-line;">
                        {{ $booking->notes }}
                    </div>
                </div>
            @endif

            @if($booking->cancellation_reason)
                <div class="alert alert-secondary mb-0">
                    <h6 class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i>Cancellation Details:</h6>
                    <div class="small">
                        Reason: {{ $booking->cancellation_reason }}
                        @if($booking->cancelled_at)
                            <div class="text-muted mt-1">Recorded on: {{ $booking->cancelled_at->format('d M Y, h:i A') }}</div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Status Update & Profiles -->
    <div class="col-lg-4">
        {{-- Status Management Form --}}
        <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 12px; border-top: 4px solid #6C3483 !important;">
            <h5 class="fw-bold mb-3 text-dark">Update Booking Status</h5>

            <form action="{{ route('admin.bookings.update-status', $booking) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Appointment Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="pending" @selected($booking->status === 'pending')>Pending (Awaiting Confirmation)</option>
                        <option value="confirmed" @selected($booking->status === 'confirmed')>Confirmed (Scheduled)</option>
                        <option value="completed" @selected($booking->status === 'completed')>Completed (Session Finished)</option>
                        <option value="cancelled" @selected($booking->status === 'cancelled')>Cancelled</option>
                        <option value="rejected" @selected($booking->status === 'rejected')>Rejected</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Payment Status <span class="text-danger">*</span></label>
                    <select name="payment_status" class="form-select" required>
                        <option value="pending" @selected($booking->payment_status === 'pending')>Pending</option>
                        <option value="paid" @selected($booking->payment_status === 'paid')>Paid / Settled</option>
                        <option value="failed" @selected($booking->payment_status === 'failed')>Failed</option>
                        <option value="refunded" @selected($booking->payment_status === 'refunded')>Refunded</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Administrative Notes</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Internal remarks, meeting URLs, or cancellation notes…">{{ old('admin_notes', $booking->admin_notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                    <i class="bi bi-save me-1"></i> Save Status Changes
                </button>
            </form>
        </div>

        {{-- Customer Info --}}
        <div class="card border-0 shadow-sm p-3 mb-4" style="border-radius: 12px;">
            <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-person text-primary me-2"></i>Customer Details</h6>
            <div class="small">
                <div class="fw-semibold text-dark">{{ $booking->user->full_name }}</div>
                <div class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $booking->user->email }}</div>
                <div class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $booking->user->phone ?? 'No phone' }}</div>
                <div class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $booking->user->city ? $booking->user->city . ', ' . $booking->user->state : 'Location not specified' }}</div>
            </div>
        </div>

        {{-- Astrologer Info --}}
        <div class="card border-0 shadow-sm p-3" style="border-radius: 12px;">
            <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-star-fill text-warning me-2"></i>Astrologer Details</h6>
            <div class="d-flex align-items-center gap-2 mb-2">
                @if($booking->astrologer->profile_image)
                    <img src="{{ asset('storage/' . $booking->astrologer->profile_image) }}" alt="{{ $booking->astrologer->display_name }}" style="width:45px;height:45px;border-radius:50%;object-fit:cover;">
                @endif
                <div>
                    <div class="fw-semibold text-dark">{{ $booking->astrologer->display_name }}</div>
                    <div class="small text-muted">{{ $booking->astrologer->email }}</div>
                </div>
            </div>
            <div class="d-grid mt-2">
                <a href="{{ route('admin.astrologers.show', $booking->astrologer) }}" class="btn btn-outline-secondary btn-sm">
                    View Astrologer Profile
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
