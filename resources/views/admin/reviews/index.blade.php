@extends('layouts.admin')

@section('title', 'Customer Reviews')
@section('page_title', 'Customer Reviews')

@section('content')
<div class="mb-4">
    <h2 class="h4 fw-bold mb-1">Customer Reviews</h2>
    <p class="text-muted mb-0">Moderate verified reviews submitted after completed consultations.</p>
</div>

<form method="GET" class="card card-body border-0 shadow-sm mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-6">
            <label for="search" class="form-label small fw-semibold">Search reviews</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Customer, astrologer, or review text">
        </div>
        <div class="col-md-4">
            <label for="visibility" class="form-label small fw-semibold">Visibility</label>
            <select id="visibility" name="visibility" class="form-select">
                <option value="">All reviews</option>
                <option value="visible" @selected(request('visibility') === 'visible')>Visible</option>
                <option value="hidden" @selected(request('visibility') === 'hidden')>Hidden</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </div>
</form>

<div class="row g-3">
    @forelse($reviews as $review)
        <div class="col-12">
            <article class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between gap-3">
                        <div>
                            <div class="fw-bold">{{ $review->booking?->user?->full_name ?: 'Customer' }} <span class="badge {{ $review->is_visible ? 'bg-success' : 'bg-secondary' }} ms-1">{{ $review->is_visible ? 'Visible' : 'Hidden' }}</span></div>
                            <div class="small text-muted">For {{ $review->astrologer?->display_name ?? 'Astrologer' }} · {{ $review->created_at->format('d M Y') }}</div>
                        </div>
                        <div class="text-warning" aria-label="{{ $review->rating }} out of 5 stars">
                            @for($star = 1; $star <= 5; $star++)<i class="bi bi-star{{ $star <= $review->rating ? '-fill' : '' }}"></i>@endfor
                            <span class="text-muted ms-1">{{ $review->rating }}/5</span>
                        </div>
                    </div>
                    <p class="mb-3 mt-3 text-secondary" style="white-space:pre-line;">{{ $review->body }}</p>
                    <form action="{{ route('admin.reviews.visibility', $review) }}" method="POST">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm {{ $review->is_visible ? 'btn-outline-warning' : 'btn-outline-success' }}">
                            <i class="bi {{ $review->is_visible ? 'bi-eye-slash' : 'bi-eye' }} me-1"></i>{{ $review->is_visible ? 'Hide review' : 'Show review' }}
                        </button>
                    </form>
                </div>
            </article>
        </div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body text-center text-muted py-5">No reviews found.</div></div></div>
    @endforelse
</div>
@if($reviews->hasPages())<div class="mt-3">{{ $reviews->links() }}</div>@endif
@endsection
