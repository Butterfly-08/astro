@extends('layouts.admin')

@section('title', 'Astrologer Management')
@section('page_title', 'Astrologer Management')

@push('styles')
<style>
    .status-badge { font-size: 0.75rem; padding: 4px 10px; border-radius: 20px; font-weight: 600; }
    .badge-pending  { background: #FEF3C7; color: #92400E; }
    .badge-active   { background: #D1FAE5; color: #065F46; }
    .badge-inactive { background: #F3F4F6; color: #374151; }
    .badge-rejected { background: #FEE2E2; color: #991B1B; }
    .avatar-sm { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; }
    .avatar-placeholder { width: 42px; height: 42px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.1rem; }
    .rating-stars { color: #F5B041; font-size: 0.82rem; }
    .stat-mini-card { background: #fff; border-radius: 10px; padding: 16px 20px; border: 1px solid #E5E7EB; }
    .stat-mini-card .number { font-size: 1.9rem; font-weight: 700; font-family: 'Outfit', sans-serif; }
    .table-actions .btn { padding: 4px 9px; font-size: 0.78rem; }
    .filter-bar { background: #fff; border-radius: 10px; padding: 16px 20px; border: 1px solid #E5E7EB; margin-bottom: 20px; }
</style>
@endpush

@section('content')

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-mini-card">
            <div class="number text-primary">{{ $stats['total'] }}</div>
            <div class="text-muted small mt-1">Total Astrologers</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini-card">
            <div class="number text-warning">{{ $stats['pending'] }}</div>
            <div class="text-muted small mt-1">Pending Approval</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini-card">
            <div class="number text-success">{{ $stats['active'] }}</div>
            <div class="text-muted small mt-1">Active</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-mini-card">
            <div class="number" style="color: #F5B041;">{{ $stats['featured'] }}</div>
            <div class="text-muted small mt-1">Featured</div>
        </div>
    </div>
</div>

{{-- Header + Add Button --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="mb-0 fw-semibold">All Astrologers</h6>
    <a href="{{ route('admin.astrologers.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Add Astrologer
    </a>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.astrologers.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name, specialization…" value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                @foreach(['pending','active','inactive','rejected'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="service_id" class="form-select form-select-sm">
                <option value="">All Services</option>
                @foreach($services as $svc)
                    <option value="{{ $svc->id }}" @selected(request('service_id') == $svc->id)>{{ $svc->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.astrologers.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Astrologer</th>
                        <th>Services</th>
                        <th>Rates (₹/min)</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Featured</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($astrologers as $astrologer)
                    <tr>
                        <td class="text-muted small">{{ $astrologer->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                @if($astrologer->profile_image)
                                    <img src="{{ asset('storage/' . $astrologer->profile_image) }}" alt="{{ $astrologer->display_name }}" class="avatar-sm">
                                @else
                                    <div class="avatar-placeholder" style="background: linear-gradient(135deg, #6C3483, #1A0B2E); color: #F5B041;">
                                        {{ strtoupper(substr($astrologer->display_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold small">{{ $astrologer->display_name }}</div>
                                    <div class="text-muted" style="font-size: 0.77rem;">{{ $astrologer->email }}</div>
                                    @if($astrologer->experience_years)
                                        <div class="text-muted" style="font-size: 0.75rem;"><i class="bi bi-briefcase me-1"></i>{{ $astrologer->experience_years }} yrs exp.</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($astrologer->services->take(3) as $svc)
                                    <span class="badge bg-light text-dark border" style="font-size: 0.72rem;">{{ $svc->name }}</span>
                                @endforeach
                                @if($astrologer->services->count() > 3)
                                    <span class="badge bg-secondary" style="font-size: 0.72rem;">+{{ $astrologer->services->count() - 3 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.8rem;" class="text-nowrap">
                                <div><i class="bi bi-chat text-primary me-1"></i>₹{{ number_format($astrologer->chat_rate, 0) }}</div>
                                <div><i class="bi bi-telephone text-success me-1"></i>₹{{ number_format($astrologer->call_rate, 0) }}</div>
                                <div><i class="bi bi-camera-video text-warning me-1"></i>₹{{ number_format($astrologer->video_rate, 0) }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= round($astrologer->rating_avg) ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            <div style="font-size: 0.77rem;" class="text-muted">{{ number_format($astrologer->rating_avg, 1) }} ({{ $astrologer->total_reviews }})</div>
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $astrologer->status }}">{{ ucfirst($astrologer->status) }}</span>
                            @if($astrologer->is_available)
                                <div class="mt-1"><span class="badge bg-success bg-opacity-10 text-success" style="font-size:0.7rem;"><i class="bi bi-circle-fill me-1" style="font-size:0.55rem;"></i>Online</span></div>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.astrologers.toggle-featured', $astrologer) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" title="{{ $astrologer->is_featured ? 'Unfeature' : 'Feature' }}">
                                    <i class="bi bi-star{{ $astrologer->is_featured ? '-fill text-warning' : ' text-muted' }} fs-5"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex justify-content-end gap-1 table-actions">
                                <a href="{{ route('admin.astrologers.show', $astrologer) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.astrologers.edit', $astrologer) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>

                                @if($astrologer->status === 'pending')
                                    <form action="{{ route('admin.astrologers.approve', $astrologer) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.astrologers.destroy', $astrologer) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete {{ $astrologer->display_name }}? This action cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-stars display-6 d-block mb-2 opacity-25"></i>
                            No astrologers found. <a href="{{ route('admin.astrologers.create') }}">Add the first one.</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($astrologers->hasPages())
            <div class="px-4 py-3 border-top">
                {{ $astrologers->links() }}
            </div>
        @endif
    </div>
</div>

@endsection
