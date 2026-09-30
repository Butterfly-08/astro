@extends('layouts.admin')

@section('title', 'Product Categories — Admin')
@section('page_title', 'Product Categories')

@push('styles')
<style>
    .cat-table-card { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; }
    .cat-icon-box { width: 44px; height: 44px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; background: rgba(108, 52, 131, 0.08); color: #6C3483; }
    .filter-bar { background: #fff; border-radius: 10px; padding: 14px 18px; border: 1px solid #E5E7EB; margin-bottom: 20px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="mb-1 fw-bold text-dark">E-Commerce Categories</h6>
        <p class="text-muted small mb-0">Organize and manage catalog categories for gemstones, rudraksha, yantras, and spiritual items.</p>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Add New Category
    </a>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-5">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search category by name or description…" value="{{ request('search') }}">
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
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">Reset</a>
        </div>
    </form>
</div>

{{-- Categories Table --}}
<div class="cat-table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase fw-semibold text-muted">
                <tr>
                    <th style="width: 50px;">Icon</th>
                    <th>Name / Slug</th>
                    <th>Sort Order</th>
                    <th>Products Count</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>
                            <div class="cat-icon-box">
                                @if($category->icon)
                                    <i class="{{ $category->icon }}"></i>
                                @else
                                    <i class="bi bi-tag"></i>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $category->name }}</div>
                            <div class="text-muted small"><code>{{ $category->slug }}</code></div>
                            @if($category->description)
                                <div class="text-muted small text-truncate" style="max-width: 320px;">{{ $category->description }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $category->sort_order }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="badge bg-primary bg-opacity-10 text-primary text-decoration-none border px-2 py-1">
                                <i class="bi bi-box me-1"></i>{{ $category->products_count }} products
                            </a>
                        </td>
                        <td>
                            @if($category->is_featured)
                                <span class="badge bg-warning bg-opacity-15 text-warning border border-warning" style="font-size:0.75rem;">
                                    <i class="bi bi-star-fill me-1"></i>Featured
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('admin.categories.toggle-status', $category) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none" title="Click to toggle status">
                                    <span class="badge bg-{{ $category->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $category->status === 'active' ? 'success' : 'secondary' }} border" style="font-size:0.75rem;">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ ucfirst($category->status) }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? Products linked to it will not be deleted.');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="bi bi-tags display-5 d-block mb-3 opacity-25"></i>
                            <p class="mb-2">No product categories found.</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary">Create First Category</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="p-3 border-top">
            {{ $categories->links() }}
        </div>
    @endif
</div>

@endsection
