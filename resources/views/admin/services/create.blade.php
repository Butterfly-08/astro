@extends('layouts.admin')

@section('title', 'Add New Service')
@section('page_title', 'Add New Service')

@push('styles')
<style>
    .form-section { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px; margin-bottom: 20px; }
    .form-section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6; }
    .icon-preview { font-size: 2rem; color: #6C3483; min-width: 40px; }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-12 col-lg-8">
        <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-info-circle me-2"></i>Service Details</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Service Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required placeholder="e.g. Vedic Astrology">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control @error('sort_order') is-invalid @enderror" value="{{ old('sort_order', 0) }}" min="0">
                        <div class="form-text">Lower = appears first.</div>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="iconInput" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', 'bi-stars') }}" placeholder="bi-stars">
                            <span class="input-group-text"><i id="iconPreview" class="{{ old('icon', 'bi-stars') }} icon-preview"></i></span>
                        </div>
                        <div class="form-text">e.g. <code>bi-stars</code>, <code>bi-moon-stars</code>. Browse at <a href="https://icons.getbootstrap.com" target="_blank">icons.getbootstrap.com</a></div>
                        @error('icon')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            <option value="consultation" @selected(old('type','consultation')==='consultation')>Consultation</option>
                            <option value="product" @selected(old('type')==='product')>Product</option>
                            <option value="both" @selected(old('type')==='both')>Both</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Short Description <span class="text-muted">(shown on cards, max 255)</span></label>
                        <input type="text" name="short_description" class="form-control @error('short_description') is-invalid @enderror" value="{{ old('short_description') }}" maxlength="255">
                        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Full Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Cover Image</label>
                        <input type="file" name="cover_image" class="form-control @error('cover_image') is-invalid @enderror" accept="image/*">
                        @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-toggle-on me-2"></i>Status & Visibility</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Status *</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="active" @selected(old('status','active')==='active')>Active</option>
                            <option value="inactive" @selected(old('status')==='inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end pb-1">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured'))>
                            <label class="form-check-label fw-semibold small" for="is_featured"><i class="bi bi-star-fill text-warning me-1"></i>Featured on Homepage</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-4">
                <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Create Service</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Quick Icon Guide</h6>
                <div class="row g-2 small">
                    @foreach(['bi-stars' => 'Vedic', 'bi-moon-stars' => 'Horoscope', 'bi-puzzle' => 'Numerology', 'bi-eye' => 'Clairvoyance', 'bi-heart-fill' => 'Love & Relationships', 'bi-cash-coin' => 'Finance', 'bi-gem' => 'Gemstones', 'bi-compass' => 'Vastu Shastra'] as $icon => $label)
                        <div class="col-6">
                            <div class="p-2 border rounded d-flex align-items-center gap-2" style="cursor:pointer;" onclick="document.getElementById('iconInput').value='{{ $icon }}'; document.getElementById('iconPreview').className='{{ $icon }} icon-preview';">
                                <i class="{{ $icon }} text-primary"></i>
                                <span>{{ $label }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('iconInput').addEventListener('input', function() {
        document.getElementById('iconPreview').className = this.value + ' icon-preview';
    });
</script>
@endpush
