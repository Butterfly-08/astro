@extends('layouts.admin')

@section('title', 'Blogs & Articles')
@section('page_title', 'Blogs & Articles')

@section('content')
<div class="d-flex justify-content-between align-items-center gap-3 flex-wrap mb-4">
    <div>
        <h2 class="h4 fw-bold mb-1">Blogs & Articles</h2>
        <p class="text-muted mb-0">Write and publish astrology articles for the public site.</p>
    </div>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>New Article</a>
</div>

<form method="GET" class="card card-body border-0 shadow-sm mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-semibold" for="search">Search</label>
            <input id="search" name="search" class="form-control" value="{{ request('search') }}" placeholder="Article title">
        </div>
        <div class="col-md-4">
            <label class="form-label small fw-semibold" for="status">Status</label>
            <select id="status" name="status" class="form-select">
                <option value="">All statuses</option>
                <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                <option value="published" @selected(request('status') === 'published')>Published</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-outline-primary flex-grow-1">Filter</button>
            <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Article</th><th>Slug</th><th>Published</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
                @forelse($blogs as $blog)
                    <tr>
                        <td class="fw-semibold">{{ $blog->title }}</td>
                        <td><code>{{ $blog->slug }}</code></td>
                        <td>{{ $blog->published_at?->format('d M Y H:i') ?? '—' }}</td>
                        <td><span class="badge {{ $blog->status === 'published' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($blog->status) }}</span></td>
                        <td class="text-end text-nowrap">
                            @if($blog->status === 'published' && $blog->published_at?->isPast())
                                <a href="{{ route('blogs.show', $blog->slug) }}" target="_blank" class="btn btn-sm btn-outline-secondary">View</a>
                            @endif
                            <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this article?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">No articles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($blogs->hasPages())<div class="card-footer bg-white">{{ $blogs->links() }}</div>@endif
</div>
@endsection
