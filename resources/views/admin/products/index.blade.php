@extends('layouts.admin')

@section('title', 'Product Catalog — Admin')
@section('page_title', 'Product Catalog')

@push('styles')
<style>
    .prod-table-card { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; }
    .prod-thumb { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid #E5E7EB; background: #F9FAFB; }
    .prod-thumb-placeholder { width: 48px; height: 48px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #F3F4F6; color: #F5B041; border: 1px solid #E5E7EB; font-size: 1.25rem; }
    .filter-bar { background: #fff; border-radius: 10px; padding: 14px 18px; border: 1px solid #E5E7EB; margin-bottom: 20px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="mb-1 fw-bold text-dark">Spiritual E-Commerce Catalog</h6>
        <p class="text-muted small mb-0">Manage gemstone inventory, rudraksha items, yantras, prices, and stock levels.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-tag me-1"></i> Manage Categories
        </a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i> Add New Product
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('admin.products.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, SKU or description…" value="{{ request('search') }}">
        </div>
        <div class="col-6 col-md-3">
            <select name="category_id" class="form-select form-select-sm">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-secondary flex-fill">Reset</a>
        </div>
    </form>
</div>

{{-- Products Table --}}
<div class="prod-table-card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase fw-semibold text-muted">
                <tr>
                    <th style="width: 55px;">Item</th>
                    <th>Product / SKU</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th class="text-end" style="width: 140px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="prod-thumb">
                            @else
                                <div class="prod-thumb-placeholder">
                                    <i class="bi bi-gem"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">
                                <a href="{{ route('shop.product.show', $product->slug) }}" target="_blank" class="text-dark text-decoration-none" title="Preview on live store">
                                    {{ Str::limit($product->name, 45) }} <i class="bi bi-box-arrow-up-right text-muted small" style="font-size: 0.72rem;"></i>
                                </a>
                            </div>
                            <div class="text-muted small">SKU: <code>{{ $product->sku }}</code></div>
                        </td>
                        <td>
                            @if($product->category)
                                <span class="badge bg-light text-dark border">
                                    <i class="{{ $product->category->icon ?? 'bi-tag' }} me-1"></i>{{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">₹{{ number_format($product->effective_price, 2) }}</div>
                            @if($product->is_on_sale)
                                <div class="text-muted small text-decoration-line-through">
                                    ₹{{ number_format($product->price, 2) }}
                                </div>
                                <span class="badge bg-danger text-white" style="font-size: 0.65rem;">
                                    {{ $product->discount_percentage }}% OFF
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-{{ $product->stock_badge['class'] }}">
                                {{ $product->stock }} units
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.products.toggle-featured', $product) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none" title="Click to toggle featured state">
                                    @if($product->is_featured)
                                        <span class="badge bg-warning bg-opacity-15 text-warning border border-warning" style="font-size:0.75rem;">
                                            <i class="bi bi-star-fill me-1"></i>Featured
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border" style="font-size:0.75rem;">
                                            <i class="bi bi-star me-1"></i>Standard
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td>
                            <form action="{{ route('admin.products.toggle-status', $product) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-link p-0 text-decoration-none" title="Click to toggle status">
                                    <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }} bg-opacity-10 text-{{ $product->status === 'active' ? 'success' : 'secondary' }} border" style="font-size:0.75rem;">
                                        <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ ucfirst($product->status) }}
                                    </span>
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this sacred product?');" class="d-inline">
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
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-gem display-5 d-block mb-3 opacity-25"></i>
                            <p class="mb-2">No products found in the catalog.</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary">Add First Product</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="p-3 border-top">
            {{ $products->links() }}
        </div>
    @endif
</div>

@endsection
