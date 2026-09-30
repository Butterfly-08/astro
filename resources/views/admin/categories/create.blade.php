@extends('layouts.admin')

@section('title', 'Add Product Category — Admin')
@section('page_title', 'Add Product Category')

@push('styles')
<style>
    .form-section { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px; margin-bottom: 20px; }
    .form-section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6; }
    .icon-preview { font-size: 1.5rem; color: #6C3483; }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-12 col-lg-8">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-tag me-2"></i>Category Information</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Certified Gemstones">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0">
                        <div class="form-text">Lower numbers appear first.</div>
                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Custom Slug <span class="text-muted">(Optional)</span></label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" placeholder="Auto-generated if left blank">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="iconInput" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', 'bi bi-gem') }}" placeholder="bi bi-gem">
                            <span class="input-group-text"><i id="iconPreview" class="{{ old('icon', 'bi bi-gem') }} icon-preview"></i></span>
                        </div>
                        <div class="form-text">e.g. <code>bi bi-gem</code>, <code>bi bi-shield-check</code></div>
                        @error('icon')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3" placeholder="Brief summary of items in this category...">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Category Banner / Image <span class="text-muted">(Optional)</span></label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Supported: JPG, PNG, WEBP. Max 2MB.</div>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-toggle-on me-2"></i>Status & Visibility</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" @selected(old('status', 'active') === 'active')>Active (Visible in Store)</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Inactive (Hidden)</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured'))>
                            <label class="form-check-label fw-semibold small" for="is_featured">
                                <i class="bi bi-star text-warning me-1"></i> Feature this Category on Home & Shop
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Save Category</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-3">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-light">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Category Guidance</h6>
            <p class="small text-muted mb-3">
                Categories help devotees and customers easily filter and find prescribed astrological remedies like Gemstones, Rudraksha, Yantras, and Puja Essentials.
            </p>
            <ul class="small text-muted ps-3 mb-0">
                <li class="mb-1"><strong>Active:</strong> Publicly browsable in the shop.</li>
                <li class="mb-1"><strong>Featured:</strong> Shown prominently as quick navigation pills.</li>
                <li><strong>Icon:</strong> Use standard Bootstrap 5 icon classes.</li>
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('iconInput').addEventListener('input', function() {
        const preview = document.getElementById('iconPreview');
        preview.className = this.value + ' icon-preview';
    });
</script>
@endpush

@endsection
