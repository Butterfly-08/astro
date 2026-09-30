  @extends('layouts.admin')

@section('title', 'Add New Astrologer')
@section('page_title', 'Add New Astrologer')

@push('styles')
<style>
    .form-section { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px; margin-bottom: 20px; }
    .form-section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6; }
    .preview-img { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #E5E7EB; display: none; }
    .preview-placeholder { width: 110px; height: 110px; border-radius: 50%; background: linear-gradient(135deg,#6C3483,#1A0B2E); display: flex; align-items: center; justify-content: center; color: #F5B041; font-size: 2.5rem; }
    .rate-input-group .input-group-text { font-size: 0.85rem; }
    .service-checkbox-item { padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 8px; cursor: pointer; transition: all 0.15s; }
    .service-checkbox-item:has(input:checked) { border-color: #6C3483; background: #F5F3FF; }
</style>
@endpush

@section('content')

<div class="row">
    <div class="col-12 col-xl-9">
        <form action="{{ route('admin.astrologers.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf

            {{-- Basic Info --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-person-badge me-2"></i>Basic Information</div>
                <div class="row g-3">
                    {{-- Profile Image --}}
                    <div class="col-12 text-center mb-2">
                        <div id="imagePreviewContainer" class="preview-placeholder mx-auto mb-2">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <img id="imagePreview" class="preview-img mx-auto d-block mb-2" src="#" alt="">
                        <label for="profile_image" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-upload me-1"></i>Upload Photo
                        </label>
                        <input type="file" name="profile_image" id="profile_image" class="d-none @error('profile_image') is-invalid @enderror" accept="image/*">
                        @error('profile_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Display Name <span class="text-danger">*</span></label>
                        <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" value="{{ old('display_name') }}" placeholder="e.g. Pandit Rajesh Sharma" required>
                        @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="+91 9999999999">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Experience (Years) <span class="text-danger">*</span></label>
                        <input type="number" name="experience_years" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years', 0) }}" min="0" max="99">
                        @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['pending','active','inactive'] as $s)
                                <option value="{{ $s }}" @selected(old('status', 'pending') === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Short Bio (max 300 chars)</label>
                        <textarea name="short_bio" class="form-control @error('short_bio') is-invalid @enderror" rows="2" maxlength="300" placeholder="Brief tagline shown on listing cards…">{{ old('short_bio') }}</textarea>
                        @error('short_bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Full Biography</label>
                        <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="4" placeholder="Detailed about section for the profile page…">{{ old('bio') }}</textarea>
                        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Professional Details --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-mortarboard me-2"></i>Professional Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Specializations</label>
                        <input type="text" name="specializations" class="form-control @error('specializations') is-invalid @enderror" value="{{ old('specializations') }}" placeholder="Vedic, Tarot, Numerology (comma-separated)">
                        <div class="form-text">Separate multiple entries with commas.</div>
                        @error('specializations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Languages</label>
                        <input type="text" name="languages" class="form-control @error('languages') is-invalid @enderror" value="{{ old('languages') }}" placeholder="Hindi, English, Telugu (comma-separated)">
                        @error('languages')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Education / Credentials</label>
                        <input type="text" name="education" class="form-control @error('education') is-invalid @enderror" value="{{ old('education') }}" placeholder="e.g. Jyotish Acharya, BHU Varanasi">
                        @error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Rates --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-currency-rupee me-2"></i>Consultation Rates (₹ per minute)</div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small"><i class="bi bi-chat text-primary me-1"></i>Chat Rate</label>
                        <div class="input-group rate-input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="chat_rate" class="form-control @error('chat_rate') is-invalid @enderror" value="{{ old('chat_rate', 0) }}" min="0" step="0.01">
                            <span class="input-group-text">/min</span>
                        </div>
                        @error('chat_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small"><i class="bi bi-telephone text-success me-1"></i>Call Rate</label>
                        <div class="input-group rate-input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="call_rate" class="form-control @error('call_rate') is-invalid @enderror" value="{{ old('call_rate', 0) }}" min="0" step="0.01">
                            <span class="input-group-text">/min</span>
                        </div>
                        @error('call_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small"><i class="bi bi-camera-video text-warning me-1"></i>Video Rate</label>
                        <div class="input-group rate-input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="video_rate" class="form-control @error('video_rate') is-invalid @enderror" value="{{ old('video_rate', 0) }}" min="0" step="0.01">
                            <span class="input-group-text">/min</span>
                        </div>
                        @error('video_rate')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Services --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-gem me-2"></i>Services Offered</div>
                @if($services->isEmpty())
                    <p class="text-muted small"><a href="{{ route('admin.services.create') }}">Create services first</a> to assign them here.</p>
                @else
                    <div class="row g-2">
                        @foreach($services as $svc)
                            <div class="col-6 col-md-4">
                                <label class="service-checkbox-item d-flex align-items-center gap-2 w-100">
                                    <input type="checkbox" name="service_ids[]" value="{{ $svc->id }}"
                                           @checked(in_array($svc->id, old('service_ids', [])))>
                                    @if($svc->icon)<i class="{{ $svc->icon }}"></i>@endif
                                    <span class="small">{{ $svc->name }}</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Flags --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-toggle-on me-2"></i>Flags & Visibility</div>
                <div class="d-flex flex-wrap gap-4">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured'))>
                        <label class="form-check-label fw-semibold small" for="is_featured"><i class="bi bi-star-fill text-warning me-1"></i>Featured Astrologer</label>
                    </div>
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_available" value="0">
                        <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" @checked(old('is_available'))>
                        <label class="form-check-label fw-semibold small" for="is_available"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.7rem;"></i>Currently Available</label>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex gap-3 mb-4">
                <button type="submit" class="btn btn-primary px-5">
                    <i class="bi bi-save me-2"></i>Create Astrologer
                </button>
                <a href="{{ route('admin.astrologers.index') }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Tips Sidebar --}}
    <div class="col-12 col-xl-3">
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary me-2"></i>Tips</h6>
                <ul class="small text-muted ps-3 mb-0">
                    <li class="mb-2">Set status to <strong>Pending</strong> for new applications awaiting review.</li>
                    <li class="mb-2">Use <strong>Featured</strong> to highlight top astrologers on the homepage.</li>
                    <li class="mb-2">Rates are in <strong>₹ per minute</strong> and visible to customers.</li>
                    <li>Specializations and languages use <strong>comma-separated</strong> values.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Image preview
    document.getElementById('profile_image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('imagePreviewContainer');
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
