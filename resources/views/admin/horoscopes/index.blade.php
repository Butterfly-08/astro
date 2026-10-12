@extends('layouts.admin')

@section('title', 'Horoscopes')
@section('page_title', 'Daily Horoscopes')

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Daily Horoscopes</h2>
        <p class="text-muted mb-0">Create and publish readings for each zodiac sign.</p>
    </div>
    <a href="{{ route('admin.horoscopes.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>Add Horoscope</a>
</div>

<form method="GET" class="card card-body border-0 shadow-sm mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label small fw-semibold" for="search">Search</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Sign or title">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold" for="date">Date</label>
            <input id="date" name="date" type="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-semibold" for="status">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.horoscopes.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Sign</th><th>Title</th><th>Date</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse($horoscopes as $horoscope)
                    <tr>
                        <td class="fw-semibold">{{ $horoscope->zodiac_sign }}</td>
                        <td>{{ $horoscope->title }}</td>
                        <td>{{ $horoscope->horoscope_date->format('d M Y') }}</td>
                        <td><span class="badge {{ $horoscope->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($horoscope->status) }}</span></td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.horoscopes.edit', $horoscope) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.horoscopes.destroy', $horoscope) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this horoscope?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No horoscope entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($horoscopes->hasPages())<div class="card-footer bg-white">{{ $horoscopes->links() }}</div>@endif
</div>
@endsection
