@extends('layouts.admin')

@section('title', 'Service Management')
@section('page_title', 'Service Management')

@push('styles')
<style>
    .service-card { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 18px 20px; transition: box-shadow 0.2s, transform 0.2s; }
    .service-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); transform: translateY(-2px); }
    .service-icon-box { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; background: linear-gradient(135deg, rgba(108,52,131,0.1), rgba(26,11,46,0.05)); color: #6C3483; }
    .filter-bar { background: #fff; border-radius: 10px; padding: 14px 18px; border: 1px solid #E5E7EB; margin-bottom: 20px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h6 class="mb-0 fw-semibold">All Services / Categories</h6>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>Add Service
    </a>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.services.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search service name…" value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active" @selected(request('status')==='active')>Active</option>
                <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
            </select>
        </div>
        <div class="col-6 col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">Reset</a>
        </div>
    </form>
</div>

{{-- Services Grid --}}
@if($services->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-gem display-5 d-block mb-3 opacity-25"></i>
        <p>No services found. <a href="{{ route('admin.services.create') }}">Create the first one.</a></p>
    </div>
@else
    <div class="row g-3 mb-4">
        @foreach($services as $service)
            <div class="col-md-6 col-lg-4">
                <div class="service-card h-100">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="service-icon-box">
                            @if($service->icon)
                                <i class="{{ $service->icon }}"></i>
                            @else
                                <i class="bi bi-gem"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $service->name }}</div>
                            <div class="d-flex gap-2 mt-1">
                                <span class="badge bg-{{ $service->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $service->status === 'active' ? 'success' : 'secondary' }} border" style="font-size:0.72rem;">{{ ucfirst($service->status) }}</span>
                                <span class="badge bg-light text-dark border" style="font-size:0.72rem;">{{ ucfirst($service->type) }}</span>
                                @if($service->is_featured)
                                    <span class="badge bg-warning bg-opacity-15 text-warning border border-warning" style="font-size:0.72rem;"><i class="bi bi-star-fill me-1"></i>Featured</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($service->short_description)
                        <p class="text-muted small mb-3" style="line-height:1.5;">{{ Str::limit($service->short_description, 90) }}</p>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                        <span><i class="bi bi-people me-1"></i>{{ $service->astrologers_count }} astrologers</span>
                        <span>Sort: {{ $service->sort_order }}</span>
                    </div>

                    <div class="d-flex gap-2 border-top pt-3">
                        <a href="{{ route('admin.services.edit', $service) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form action="{{ route('admin.services.toggle-status', $service) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-outline-{{ $service->status === 'active' ? 'secondary' : 'success' }}" title="{{ $service->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                <i class="bi bi-toggle-{{ $service->status === 'active' ? 'on text-success' : 'off' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                              onsubmit="return confirm('Delete \'{{ $service->name }}\'?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($services->hasPages())
        {{ $services->links() }}
    @endif
@endif

@endsection
