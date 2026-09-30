@extends('layouts.admin')

@section('title', 'Edit — ' . $service->name)
@section('page_title', 'Edit Service')

@push('styles')
<style>
    .form-section { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px; margin-bottom: 20px; }
    .form-section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6; }
    .icon-preview { font-size: 2rem; color: #6C3483; min-width: 40px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        <div style="width:46px;height:46px;border-radius:10px;background:linear-gradient(135deg,rgba(108,52,131,0.12),rgba(26,11,46,0.05));display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#6C3483;">
            <i class="{{ $service->icon ?? 'bi-gem' }}"></i>
        </div>
        <div>
            <div class="fw-bold">{{ $service->name }}</div>
            <div class="text-muted small">Slug: <code>{{ $service->slug }}</code></div>
        </div>
    </div>
    <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row">
    <div class="col-12 col-lg-8">
        <form action="{{ route('admin.services.update', $service) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf @method('PUT')

            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-info-circle me-2"></i>Service Details</div>
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Service Name *</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $service->sort_order) }}" min="0">
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-semibold small">Bootstrap Icon Class</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="iconInput" class="form-control" value="{{ old('icon', $service->icon) }}" placeholder="bi-stars">
                            <span class="input-group-text"><i id="iconPreview" class="{{ old('icon', $service->icon) }} icon-preview"></i></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Type *</label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror">
                            @foreach(['consultation','product','both'] as $t)
                                <option value="{{ $t }}" @selected(old('type', $service->type) === $t)>{{ ucfirst($t) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Short Description</label>
                        <input type="text" name="short_description" class="form-control @error('short_description') is-invalid @enderror" value="{{ old('short_description', $service->short_description) }}" maxlength="255">
                        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Full Description</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description', $service->description) }}</textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Cover Image</label>
                        @if($service->cover_image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $service->cover_image) }}" alt="{{ $service->name }}" style="height:80px;border-radius:8px;object-fit:cover;">
                                <div class="text-muted small mt-1">Current cover image — upload a new one to replace.</div>
                            </div>
                        @endif
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
                            <option value="active" @selected(old('status', $service->status)==='active')>Active</option>
                            <option value="inactive" @selected(old('status', $service->status)==='inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-8 d-flex align-items-end pb-1">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $service->is_featured))>
                            <label class="form-check-label fw-semibold small" for="is_featured"><i class="bi bi-star-fill text-warning me-1"></i>Featured on Homepage</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-4">
                <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Save Changes</button>
                <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm" style="border-radius:12px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Service Stats</h6>
                <div class="text-muted small">
                    <div class="mb-1"><i class="bi bi-people me-2"></i><strong>{{ $service->astrologers_count ?? $service->astrologers()->count() }}</strong> astrologers offering this service</div>
                    <div class="mb-1"><i class="bi bi-calendar me-2"></i>Created {{ $service->created_at->format('d M Y') }}</div>
                    <div><i class="bi bi-pencil me-2"></i>Updated {{ $service->updated_at->diffForHumans() }}</div>
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
