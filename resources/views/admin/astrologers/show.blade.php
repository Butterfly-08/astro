@extends('layouts.admin')

@section('title', $astrologer->display_name . ' — Profile')
@section('page_title', 'Astrologer Profile')

@push('styles')
<style>
    .profile-hero { background: linear-gradient(135deg, #1A0B2E 0%, #381861 100%); border-radius: 16px; color: #fff; padding: 30px; margin-bottom: 24px; }
    .profile-avatar { width: 90px; height: 90px; border-radius: 50%; object-fit: cover; border: 4px solid rgba(245,176,65,0.5); }
    .profile-avatar-placeholder { width: 90px; height: 90px; border-radius: 50%; background: rgba(245,176,65,0.2); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; color: #F5B041; border: 4px solid rgba(245,176,65,0.3); }
    .info-tab-content { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px; }
    .detail-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px solid #F3F4F6; }
    .detail-item:last-child { border-bottom: none; }
    .detail-label { width: 160px; flex-shrink: 0; color: #6B7280; font-size: 0.85rem; font-weight: 500; }
    .detail-value { font-size: 0.9rem; color: #1F2937; }
    .tag-pill { display: inline-block; padding: 3px 10px; background: #F3F4F6; border-radius: 20px; font-size: 0.78rem; color: #374151; margin: 2px; }
    .rate-block { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 10px; padding: 14px; text-align: center; }
    .rate-amount { font-size: 1.4rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .availability-day { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 10px 14px; }
    .status-active   { color: #065F46; background: #D1FAE5; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .status-pending  { color: #92400E; background: #FEF3C7; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .status-inactive { color: #374151; background: #F3F4F6; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
    .status-rejected { color: #991B1B; background: #FEE2E2; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
</style>
@endpush

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0" style="font-size:0.85rem;">
        <li class="breadcrumb-item"><a href="{{ route('admin.astrologers.index') }}">Astrologers</a></li>
        <li class="breadcrumb-item active">{{ $astrologer->display_name }}</li>
    </ol>
</nav>

{{-- Profile Hero --}}
<div class="profile-hero">
    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-4">
        <div>
            @if($astrologer->profile_image)
                <img src="{{ asset('storage/' . $astrologer->profile_image) }}" class="profile-avatar" alt="{{ $astrologer->display_name }}">
            @else
                <div class="profile-avatar-placeholder">{{ strtoupper(substr($astrologer->display_name, 0, 1)) }}</div>
            @endif
        </div>
        <div class="flex-grow-1">
            <div class="d-flex align-items-center gap-3 flex-wrap mb-1">
                <h4 class="mb-0 fw-bold">{{ $astrologer->display_name }}</h4>
                <span class="status-{{ $astrologer->status }}">{{ ucfirst($astrologer->status) }}</span>
                @if($astrologer->is_featured)
                    <span style="background:rgba(245,176,65,0.2);color:#F5B041;padding:4px 10px;border-radius:20px;font-size:0.78rem;"><i class="bi bi-star-fill me-1"></i>Featured</span>
                @endif
                @if($astrologer->is_available)
                    <span style="background:rgba(16,185,129,0.2);color:#6EE7B7;padding:4px 10px;border-radius:20px;font-size:0.78rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.6rem;"></i>Online</span>
                @endif
            </div>
            <div class="opacity-75 mb-2" style="font-size:0.88rem;">{{ $astrologer->email }} @if($astrologer->phone) · {{ $astrologer->phone }} @endif</div>
            <div class="d-flex flex-wrap gap-3" style="font-size:0.85rem;opacity:0.85;">
                <span><i class="bi bi-briefcase me-1"></i>{{ $astrologer->experience_years }} yrs exp.</span>
                <span><i class="bi bi-star-fill me-1" style="color:#F5B041;"></i>{{ number_format($astrologer->rating_avg, 1) }} ({{ number_format($astrologer->total_reviews) }} reviews)</span>
                <span><i class="bi bi-people me-1"></i>{{ number_format($astrologer->total_consultations) }} consultations</span>
            </div>
            @if($astrologer->short_bio)
                <p class="mt-2 mb-0 opacity-80 small">{{ $astrologer->short_bio }}</p>
            @endif
        </div>
        <div class="d-flex flex-column gap-2 ms-md-auto">
            @if($astrologer->status === 'active')
                <a href="{{ route('admin.bookings.create', ['astrologer_id' => $astrologer->id]) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-calendar-plus me-1"></i>Book Consultation
                </a>
            @endif
            <a href="{{ route('admin.astrologers.edit', $astrologer) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i>Edit
            </a>
            @if($astrologer->status === 'pending')
                <form action="{{ route('admin.astrologers.approve', $astrologer) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm w-100"><i class="bi bi-check-circle me-1"></i>Approve</button>
                </form>
            @endif
            <a href="{{ route('astrologers.show', $astrologer->slug) }}" target="_blank" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i>Public Profile
            </a>
        </div>
    </div>
</div>

{{-- Activity Metric Highlights --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-primary border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Consultation Bookings</span>
                    <h3 class="fw-bold my-1 text-primary">{{ $astrologer->total_bookings_count }}</h3>
                    <small class="text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>{{ $astrologer->completed_bookings_count }} Completed</small>
                </div>
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 fs-3">
                    <i class="bi bi-calendar2-check-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-purple border-4" style="border-left-color: #6f42c1 !important;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Store Orders Taken</span>
                    <h3 class="fw-bold my-1 text-dark" style="color: #6f42c1 !important;">{{ $astrologer->total_orders_count }}</h3>
                    <small class="text-success fw-semibold"><i class="bi bi-truck me-1"></i>{{ $astrologer->delivered_orders_count }} Delivered</small>
                </div>
                <div class="rounded-3 bg-purple bg-opacity-10 p-3 fs-3" style="color: #6f42c1; background-color: rgba(111, 66, 193, 0.1);">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-success border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Calls Attended</span>
                    <h3 class="fw-bold my-1 text-success">{{ $astrologer->attended_calls_count }}</h3>
                    <small class="text-muted">
                        @php
                            $callTotal = $astrologer->attended_calls_count + $astrologer->ignored_calls_count;
                            $attendRate = $callTotal > 0 ? round(($astrologer->attended_calls_count / $callTotal) * 100) : 0;
                        @endphp
                        <i class="bi bi-percent me-1"></i>{{ $attendRate }}% Attendance rate
                    </small>
                </div>
                <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 fs-3">
                    <i class="bi bi-telephone-inbound-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-danger border-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small text-uppercase fw-semibold">Calls Ignored</span>
                    <h3 class="fw-bold my-1 text-danger">{{ $astrologer->ignored_calls_count }}</h3>
                    <small class="text-danger fw-semibold">
                        <i class="bi bi-x-circle-fill me-1"></i>Rejected / Cancelled
                    </small>
                </div>
                <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 fs-3">
                    <i class="bi bi-telephone-x-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabs --}}
<ul class="nav nav-tabs mb-0" id="profileTabs" role="tablist" style="border-bottom: none;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
            <i class="bi bi-info-circle me-1"></i>Overview
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#tab-activity" type="button">
            <i class="bi bi-telephone-inbound me-1"></i>Orders & Call Activity
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#tab-services" type="button">
            <i class="bi bi-gem me-1"></i>Services
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="availability-tab" data-bs-toggle="tab" data-bs-target="#tab-availability" type="button">
            <i class="bi bi-calendar3 me-1"></i>Availability
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="rates-tab" data-bs-toggle="tab" data-bs-target="#tab-rates" type="button">
            <i class="bi bi-currency-rupee me-1"></i>Rates
        </button>
    </li>
</ul>

<div class="tab-content">
    {{-- Overview Tab --}}
    <div class="tab-pane fade show active info-tab-content" id="overview" role="tabpanel" style="border-top: none; border-radius: 0 12px 12px 12px;">
        @if($astrologer->bio)
            <h6 class="fw-bold mb-2">About</h6>
            <p class="text-muted" style="font-size:0.9rem;line-height:1.7;">{{ $astrologer->bio }}</p>
            <hr>
        @endif

        <div class="detail-item">
            <div class="detail-label">Specializations</div>
            <div class="detail-value">
                @foreach($astrologer->specializations_array as $spec)
                    <span class="tag-pill">{{ $spec }}</span>
                @endforeach
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Languages</div>
            <div class="detail-value">
                @foreach($astrologer->languages_array as $lang)
                    <span class="tag-pill">{{ $lang }}</span>
                @endforeach
            </div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Education</div>
            <div class="detail-value">{{ $astrologer->education ?? 'N/A' }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Approved By</div>
            <div class="detail-value">
                @if($astrologer->approvedBy)
                    {{ $astrologer->approvedBy->name }} on {{ $astrologer->approved_at?->format('d M Y, h:i A') }}
                @else
                    Not yet approved
                @endif
            </div>
        </div>
        @if($astrologer->status === 'rejected' && $astrologer->rejection_reason)
            <div class="detail-item">
                <div class="detail-label">Rejection Reason</div>
                <div class="detail-value text-danger">{{ $astrologer->rejection_reason }}</div>
            </div>
        @endif
        <div class="detail-item">
            <div class="detail-label">Created</div>
            <div class="detail-value">{{ $astrologer->created_at->format('d M Y, h:i A') }}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Last Updated</div>
            <div class="detail-value">{{ $astrologer->updated_at->diffForHumans() }}</div>
        </div>
    </div>

    {{-- Orders & Call Activity Tab --}}
    <div class="tab-pane fade info-tab-content" id="tab-activity" role="tabpanel" style="border-top:none;border-radius:0 12px 12px 12px;">
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill text-success"></i> Call Consultations Summary
                    </h6>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">Total Voice Calls Received</span>
                        <span class="fw-bold fs-6">{{ $astrologer->total_calls_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Calls Attended (Completed)</span>
                        <span class="badge bg-success fs-6">{{ $astrologer->attended_calls_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-danger"><i class="bi bi-x-circle-fill me-1"></i> Calls Ignored (Rejected / Cancelled)</span>
                        <span class="badge bg-danger fs-6">{{ $astrologer->ignored_calls_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-muted">Call Response / Attendance Rate</span>
                        <span class="fw-bold text-primary fs-6">{{ $attendRate }}%</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-3 border rounded-3 bg-light">
                    <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-bag-check-fill text-primary"></i> Orders & Consultations Taken
                    </h6>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">Total Consultations Booked</span>
                        <span class="fw-bold fs-6">{{ $astrologer->total_bookings_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">Consultations Completed</span>
                        <span class="badge bg-primary fs-6">{{ $astrologer->completed_bookings_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <span class="text-muted">E-Commerce Store Orders Taken</span>
                        <span class="fw-bold fs-6">{{ $astrologer->total_orders_count }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2">
                        <span class="text-muted">Store Orders Delivered</span>
                        <span class="badge bg-success fs-6">{{ $astrologer->delivered_orders_count }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Calls Table --}}
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-telephone-inbound text-success me-2"></i>Recent Voice Call Consultations</h6>
                <a href="{{ route('admin.bookings.index', ['astrologer_id' => $astrologer->id]) }}" class="btn btn-sm btn-outline-primary">View All Bookings</a>
            </div>
            @if($recentCalls->isEmpty())
                <p class="text-muted small py-3">No voice call consultations recorded for this astrologer yet.</p>
            @else
                <div class="table-responsive border rounded-3">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Booking #</th>
                                <th>Customer</th>
                                <th>Date & Time</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentCalls as $call)
                                <tr>
                                    <td class="font-monospace fw-bold">{{ $call->booking_number }}</td>
                                    <td>{{ $call->user->full_name ?? ($call->user->first_name . ' ' . $call->user->last_name) }}</td>
                                    <td>{{ $call->booking_date->format('d M Y') }} &bull; {{ $call->formatted_time_slot }}</td>
                                    <td>{{ $call->duration_minutes }} mins</td>
                                    <td>
                                        @if($call->status === 'completed')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle-fill me-1"></i>Attended
                                            </span>
                                        @elseif(in_array($call->status, ['rejected', 'cancelled']))
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="bi bi-x-circle-fill me-1"></i>Ignored ({{ ucfirst($call->status) }})
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                <i class="bi bi-clock me-1"></i>{{ ucfirst($call->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>₹{{ number_format($call->amount, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.bookings.show', $call) }}" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">Details</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Recent Referred Orders Table --}}
        <div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0"><i class="bi bi-bag-check text-primary me-2"></i>Recent E-Commerce Orders Taken / Handled</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">View All Orders</a>
            </div>
            @if($recentOrders->isEmpty())
                <p class="text-muted small py-3">No e-commerce store orders referred/taken by this astrologer yet.</p>
            @else
                <div class="table-responsive border rounded-3">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Order Status</th>
                                <th>Payment</th>
                                <th>Total</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $ord)
                                <tr>
                                    <td class="font-monospace fw-bold">{{ $ord->order_number }}</td>
                                    <td>{{ $ord->user->full_name ?? ($ord->shipping_name ?? 'Customer') }}</td>
                                    <td>{{ $ord->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ord->status === 'delivered' ? 'success' : ($ord->status === 'cancelled' ? 'danger' : 'info') }}-subtle text-{{ $ord->status === 'delivered' ? 'success' : ($ord->status === 'cancelled' ? 'danger' : 'info') }} border">
                                            {{ ucfirst($ord->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ord->payment_status === 'paid' ? 'success' : 'warning text-dark' }}">
                                            {{ ucfirst($ord->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="fw-semibold">₹{{ number_format($ord->total_amount, 2) }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.orders.show', $ord) }}" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 0.75rem;">View Order</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Services Tab --}}
    <div class="tab-pane fade info-tab-content" id="tab-services" role="tabpanel" style="border-top:none;border-radius:0 12px 12px 12px;">
        @if($astrologer->services->isEmpty())
            <p class="text-muted text-center py-3">No services assigned. <a href="{{ route('admin.astrologers.edit', $astrologer) }}">Edit to add services.</a></p>
        @else
            <div class="row g-3">
                @foreach($astrologer->services as $svc)
                    <div class="col-md-4">
                        <div class="p-3 border rounded-3 h-100">
                            @if($svc->icon)
                                <div class="mb-2 fs-4 text-primary"><i class="{{ $svc->icon }}"></i></div>
                            @endif
                            <div class="fw-semibold">{{ $svc->name }}</div>
                            @if($svc->short_description)
                                <div class="text-muted small mt-1">{{ $svc->short_description }}</div>
                            @endif
                            <div class="mt-2">
                                <span class="badge bg-{{ $svc->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $svc->status === 'active' ? 'success' : 'secondary' }} border border-{{ $svc->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($svc->status) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Availability Tab --}}
    <div class="tab-pane fade info-tab-content" id="tab-availability" role="tabpanel" style="border-top:none;border-radius:0 12px 12px 12px;">
        @if($astrologer->availability->isEmpty())
            <p class="text-muted text-center py-3">No availability slots defined. <a href="{{ route('admin.astrologers.edit', $astrologer) }}">Configure schedule.</a></p>
        @else
            <div class="row g-3">
                @foreach($astrologer->availability as $slot)
                    <div class="col-md-6 col-lg-4">
                        <div class="availability-day">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">{{ $slot->day_label }}</span>
                                <span class="badge bg-{{ $slot->is_active ? 'success' : 'secondary' }}">{{ $slot->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <div class="text-muted small mt-1">
                                <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Rates Tab --}}
    <div class="tab-pane fade info-tab-content" id="tab-rates" role="tabpanel" style="border-top:none;border-radius:0 12px 12px 12px;">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="rate-block">
                    <div class="text-primary mb-2"><i class="bi bi-chat fs-3"></i></div>
                    <div class="rate-amount text-primary">₹{{ number_format($astrologer->chat_rate, 2) }}</div>
                    <div class="text-muted small">per minute — Chat</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="rate-block">
                    <div class="text-success mb-2"><i class="bi bi-telephone fs-3"></i></div>
                    <div class="rate-amount text-success">₹{{ number_format($astrologer->call_rate, 2) }}</div>
                    <div class="text-muted small">per minute — Voice Call</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="rate-block">
                    <div class="text-warning mb-2"><i class="bi bi-camera-video fs-3"></i></div>
                    <div class="rate-amount text-warning">₹{{ number_format($astrologer->video_rate, 2) }}</div>
                    <div class="text-muted small">per minute — Video Call</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
