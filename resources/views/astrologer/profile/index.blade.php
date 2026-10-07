@extends('astrologer.layouts.app')

@section('title', 'My Partner Profile')
@section('page_title', 'Profile & Account Settings')

@section('content')
<div class="row g-4">
    <!-- Left Column: Partner Status Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm text-center p-4 mb-4" style="border-radius: 14px;">
            <div class="card-body p-0">
                <div class="mx-auto mb-3 position-relative d-inline-block">
                    @if($astrologer->profile_photo_url)
                        <img src="{{ $astrologer->profile_photo_url }}" alt="{{ $astrologer->display_name }}" class="rounded-circle border border-3 border-warning" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center mx-auto fs-1 fw-bold border border-3 border-warning" style="width: 100px; height: 100px;">
                            {{ strtoupper(substr($astrologer->display_name ?? 'A', 0, 1)) }}
                        </div>
                    @endif
                </div>

                <h5 class="fw-bold mb-1 text-dark">{{ $astrologer->display_name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>

                <div class="d-flex justify-content-center gap-2 mb-3">
                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1">
                        <i class="bi bi-patch-check-fill me-1"></i>Approved Partner
                    </span>
                    <span class="badge bg-warning text-dark px-3 py-1 font-monospace">
                        {{ $astrologer->referral_code }}
                    </span>
                </div>

                <hr class="my-3">

                <div class="text-start small text-muted">
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Specialization:</span>
                        <strong class="text-dark">{{ $astrologer->specializations ?? 'Vedic Astrology' }}</strong>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Experience:</span>
                        <strong class="text-dark">{{ $astrologer->experience_years }} Years</strong>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span>Languages:</span>
                        <strong class="text-dark">{{ $astrologer->languages ?? 'Hindi, English' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Phone:</span>
                        <strong class="text-dark">{{ $astrologer->phone ?? $user->phone ?? 'N/A' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Profile & Password -->
    <div class="col-lg-8">
        <!-- Edit Profile Form Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-1"><i class="bi bi-person-gear text-warning me-2"></i>Update Profile Details</h5>
                <p class="text-muted small mb-0">Maintain your professional consultation and referral credentials</p>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('astrologer.profile.update') }}">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $astrologer->phone ?? $user->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Experience (Years)</label>
                            <input type="number" min="0" max="60" name="experience_years" class="form-control" value="{{ old('experience_years', $astrologer->experience_years) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Specializations</label>
                            <input type="text" name="specializations" class="form-control" placeholder="e.g. Kundali, Gemstones, Vastu" value="{{ old('specializations', $astrologer->specializations) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Spoken Languages</label>
                            <input type="text" name="languages" class="form-control" placeholder="e.g. Hindi, English, Sanskrit" value="{{ old('languages', $astrologer->languages) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-muted">Professional Biography / Remedy Philosophy</label>
                        <textarea name="bio" rows="4" class="form-control" placeholder="Tell clients about your lineage and practice..." required>{{ old('bio', $astrologer->bio) }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning fw-bold px-4">
                            <i class="bi bi-save me-1"></i> Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security / Change Password Card -->
        <div class="card border-0 shadow-sm" style="border-radius: 14px;">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-1"><i class="bi bi-shield-lock text-warning me-2"></i>Security & Password</h5>
                <p class="text-muted small mb-0">Ensure your account password is strong and updated regularly</p>
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('astrologer.profile.password') }}">
                    @csrf

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">New Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold text-muted">Confirm New Password</label>
                            <input type="password" name="password_confirmation" class="form-control" minlength="8" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-outline-dark fw-bold px-4">
                            <i class="bi bi-key me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
