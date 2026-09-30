@extends('layouts.admin')

@section('title', 'Edit Category — ' . $category->name)
@section('page_title', 'Edit Category')

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
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-tag me-2"></i>Category Information</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', $category->sort_order) }}" min="0">
                        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Slug</label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category->slug) }}">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="iconInput" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $category->icon) }}">
                            <span class="input-group-text"><i id="iconPreview" class="{{ old('icon', $category->icon) }} icon-preview"></i></span>
                        </div>
                        @error('icon')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $category->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Category Image</label>
                        @if($category->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="rounded border p-1" style="max-height: 80px;">
                            </div>
                        @endif
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                        <div class="form-text">Leave blank to keep existing image. Supported: JPG, PNG, WEBP. Max 2MB.</div>
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
                            <option value="active" @selected(old('status', $category->status) === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $category->status) === 'inactive')>Inactive</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $category->is_featured))>
                            <label class="form-check-label fw-semibold small" for="is_featured">
                                <i class="bi bi-star text-warning me-1"></i> Feature this Category
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-5">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">Update Category</button>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary px-3">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm p-4 bg-light">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam me-2"></i>Associated Products</h6>
            <p class="small text-muted mb-3">
                This category currently has <strong class="text-dark">{{ $category->products()->count() }}</strong> products attached to it.
            </p>
            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" class="btn btn-outline-primary btn-sm w-100">
                <i class="bi bi-boxes me-1"></i> Manage Category Products
            </a>
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
