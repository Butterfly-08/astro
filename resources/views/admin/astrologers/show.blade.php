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

{{-- Tabs --}}
<ul class="nav nav-tabs mb-0" id="profileTabs" role="tablist" style="border-bottom: none;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
            <i class="bi bi-info-circle me-1"></i>Overview
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
