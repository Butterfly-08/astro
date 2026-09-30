@extends('layouts.admin')

@section('title', 'Add New Product — Admin')
@section('page_title', 'Add New Product')

@push('styles')
<style>
    .form-section { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px; margin-bottom: 20px; }
    .form-section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6; }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-12 col-lg-8">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- Basic Information --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-info-circle me-2"></i>Product Information</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Product Category <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Custom SKU <span class="text-muted">(Optional)</span></label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="Auto-generated if left blank">
                        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Natural Certified Blue Sapphire (Neelam) 5.25 Ratti">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Custom Slug <span class="text-muted">(Optional)</span></label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="Auto-generated from name if blank">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Short Summary <span class="text-muted">(shown on store cards)</span></label>
                        <textarea name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="2" placeholder="Brief summary of the spiritual item and who it is recommended for...">{{ old('short_description') }}</textarea>
                        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Full Description & Vedic Benefits <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="6" required placeholder="Detailed information, origin, wearing method, deity mantra, and ritual details...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Pricing & Inventory --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-currency-rupee me-2"></i>Pricing & Inventory</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Regular Price (₹) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required min="0" placeholder="0.00">
                        </div>
                        @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Sale Price (₹) <span class="text-muted">(Optional)</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="0.01" name="sale_price" class="form-control @error('sale_price') is-invalid @enderror" value="{{ old('sale_price') }}" min="0" placeholder="Optional discount">
                        </div>
                        @error('sale_price')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 10) }}" min="0" required>
                        <div class="form-text">Set to 0 for Out of Stock.</div>
                        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Images & Media --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-image me-2"></i>Product Visuals</div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Primary Product Image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Supported: JPG, PNG, WEBP. Max 2MB. Clear high-resolution photo recommended.</div>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Status & Visibility --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-toggle-on me-2"></i>Status & Visibility</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Catalog Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" @selected(old('status', 'active') === 'active')>Active (Visible to Shoppers)</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Inactive (Draft / Hidden)</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured'))>
                            <label class="form-check-label fw-semibold small" for="is_featured">
                                <i class="bi bi-star text-warning me-1"></i> Feature on Homepage & Top of Shop
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Product</button>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary px-3">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-light mb-3">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-stars text-warning me-2"></i>E-Commerce Best Practices</h6>
            <ul class="small text-muted ps-3 mb-0">
                <li class="mb-2"><strong>SKU:</strong> Unique inventory identifier. If left empty, an automated SKU like <code>ASTRO-XXXX</code> is generated.</li>
                <li class="mb-2"><strong>Sale Price:</strong> Must be strictly lower than regular price to display discount badge.</li>
                <li class="mb-2"><strong>Vedic Energization:</strong> Detail the specific planetary deity and chanting rituals in the description.</li>
            </ul>
        </div>
    </div>
</div>

@endsection
