@extends('layouts.admin')

@section('title', 'Manage Consultations & Appointments')
@section('page_title', 'Consultation Bookings Management')

@section('content')
{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl">
        <div class="stat-card">
            <span class="text-muted small text-uppercase fw-semibold">Total Bookings</span>
            <h3 class="fw-bold my-1 text-dark">{{ number_format($stats['total']) }}</h3>
            <small class="text-muted">All-time appointments</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card">
            <span class="text-muted small text-uppercase fw-semibold">Pending</span>
            <h3 class="fw-bold my-1 text-warning">{{ number_format($stats['pending']) }}</h3>
            <small class="text-warning">Awaiting confirmation</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card">
            <span class="text-muted small text-uppercase fw-semibold">Confirmed</span>
            <h3 class="fw-bold my-1 text-success">{{ number_format($stats['confirmed']) }}</h3>
            <small class="text-success">Active scheduled</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card">
            <span class="text-muted small text-uppercase fw-semibold">Completed</span>
            <h3 class="fw-bold my-1 text-primary">{{ number_format($stats['completed']) }}</h3>
            <small class="text-primary">Finished sessions</small>
        </div>
    </div>
    <div class="col-sm-6 col-xl">
        <div class="stat-card">
            <span class="text-muted small text-uppercase fw-semibold">Cancelled</span>
            <h3 class="fw-bold my-1 text-secondary">{{ number_format($stats['cancelled']) }}</h3>
            <small class="text-muted">Cancelled/Rejected</small>
        </div>
    </div>
</div>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.bookings.index') }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Booking #, customer, astro..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'rejected' => 'Rejected'] as $val => $label)
                            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="astrologer_id" class="form-select">
                        <option value="">All Astrologers</option>
                        @foreach($astrologers as $astro)
                            <option value="{{ $astro->id }}" @selected(request('astrologer_id') == $astro->id)>{{ $astro->display_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i>Filter</button>
                    @if(request()->hasAny(['search','status','astrologer_id','date']))
                        <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary" title="Clear Filters"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Bookings Table --}}
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-week text-primary me-2"></i>Consultation Records</h6>
        <span class="badge bg-light text-dark border">{{ $bookings->total() }} Total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Booking #</th>
                        <th>Customer</th>
                        <th>Astrologer</th>
                        <th>Date & Time</th>
                        <th>Mode</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold font-monospace">{{ $booking->booking_number }}</span>
                                <div class="small text-muted">{{ $booking->created_at->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $booking->user->full_name }}</div>
                                <div class="small text-muted">{{ $booking->user->email }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $booking->astrologer->display_name }}</div>
                                @if($booking->service)
                                    <div class="small text-muted">{{ $booking->service->name }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $booking->booking_date->format('d M Y') }}</div>
                                <div class="small text-muted">{{ $booking->formatted_time_slot }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-capitalize">
                                    <i class="bi bi-{{ $booking->consultation_type === 'video' ? 'camera-video text-warning' : ($booking->consultation_type === 'call' ? 'telephone text-success' : 'chat-dots text-primary') }} me-1"></i>
                                    {{ $booking->consultation_type }} ({{ $booking->duration_minutes }}m)
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">₹{{ number_format($booking->amount, 2) }}</div>
                                <div class="small">{!! $booking->payment_status_badge !!}</div>
                            </td>
                            <td>
                                {!! $booking->status_badge !!}
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil-square me-1"></i>Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x display-6 d-block mb-2 opacity-25"></i>
                                No consultation bookings match the selected criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($bookings->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
