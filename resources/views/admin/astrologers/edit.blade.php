@extends('layouts.admin')

@section('title', 'Edit — ' . $astrologer->display_name)
@section('page_title', 'Edit Astrologer')

@push('styles')
<style>
    .form-section { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; padding: 24px; margin-bottom: 20px; }
    .form-section-title { font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #6B7280; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #F3F4F6; }
    .preview-img { width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 3px solid #E5E7EB; }
    .service-checkbox-item { padding: 8px 12px; border: 1px solid #E5E7EB; border-radius: 8px; cursor: pointer; transition: all 0.15s; }
    .service-checkbox-item:has(input:checked) { border-color: #6C3483; background: #F5F3FF; }
    .status-rejected { color: #991B1B; background: #FEE2E2; border-radius: 6px; padding: 10px 14px; }
    .day-slot { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px 16px; }
</style>
@endpush

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-3">
        @if($astrologer->profile_image)
            <img src="{{ asset('storage/' . $astrologer->profile_image) }}" alt="{{ $astrologer->display_name }}" style="width:50px;height:50px;border-radius:50%;object-fit:cover;">
        @else
            <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg,#6C3483,#1A0B2E);display:flex;align-items:center;justify-content:center;color:#F5B041;font-size:1.4rem;font-weight:700;">
                {{ strtoupper(substr($astrologer->display_name, 0, 1)) }}
            </div>
        @endif
        <div>
            <div class="fw-bold">{{ $astrologer->display_name }}</div>
            <div class="text-muted small">{{ $astrologer->email }}</div>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.astrologers.show', $astrologer) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-eye me-1"></i>View Profile
        </a>
        <a href="{{ route('admin.astrologers.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
    </div>
</div>

@if($astrologer->status === 'rejected' && $astrologer->rejection_reason)
    <div class="status-rejected mb-4">
        <strong><i class="bi bi-x-circle me-2"></i>Rejection Reason:</strong> {{ $astrologer->rejection_reason }}
    </div>
@endif

<div class="row">
    <div class="col-12 col-xl-9">
        <form action="{{ route('admin.astrologers.update', $astrologer) }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf @method('PUT')

            {{-- Basic Info --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-person-badge me-2"></i>Basic Information</div>
                <div class="row g-3">
                    <div class="col-12 text-center mb-2">
                        @if($astrologer->profile_image)
                            <img id="imagePreview" class="preview-img mx-auto d-block mb-2" src="{{ asset('storage/' . $astrologer->profile_image) }}" alt="">
                        @else
                            <div id="imagePreviewPlaceholder" style="width:110px;height:110px;border-radius:50%;background:linear-gradient(135deg,#6C3483,#1A0B2E);display:flex;align-items:center;justify-content:center;color:#F5B041;font-size:2.5rem;margin:0 auto 12px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <img id="imagePreview" class="preview-img mx-auto d-block mb-2" src="#" alt="" style="display:none!important;">
                        @endif
                        <label for="profile_image" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-upload me-1"></i>Change Photo
                        </label>
                        <input type="file" name="profile_image" id="profile_image" class="d-none" accept="image/*">
                        @error('profile_image')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Display Name *</label>
                        <input type="text" name="display_name" class="form-control @error('display_name') is-invalid @enderror" value="{{ old('display_name', $astrologer->display_name) }}" required>
                        @error('display_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Email *</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $astrologer->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Phone</label>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $astrologer->phone) }}">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Experience (Years) *</label>
                        <input type="number" name="experience_years" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years', $astrologer->experience_years) }}" min="0" max="99">
                        @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold small">Status *</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            @foreach(['pending','active','inactive','rejected'] as $s)
                                <option value="{{ $s }}" @selected(old('status', $astrologer->status) === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Rejection Reason <span class="text-muted">(only if status is Rejected)</span></label>
                        <textarea name="rejection_reason" class="form-control @error('rejection_reason') is-invalid @enderror" rows="2">{{ old('rejection_reason', $astrologer->rejection_reason) }}</textarea>
                        @error('rejection_reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Short Bio</label>
                        <textarea name="short_bio" class="form-control @error('short_bio') is-invalid @enderror" rows="2" maxlength="300">{{ old('short_bio', $astrologer->short_bio) }}</textarea>
                        @error('short_bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold small">Full Biography</label>
                        <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="5">{{ old('bio', $astrologer->bio) }}</textarea>
                        @error('bio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Professional --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-mortarboard me-2"></i>Professional Details</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Specializations</label>
                        <input type="text" name="specializations" class="form-control @error('specializations') is-invalid @enderror" value="{{ old('specializations', $astrologer->specializations) }}" placeholder="Vedic, Tarot, Numerology">
                        @error('specializations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Languages</label>
                        <input type="text" name="languages" list="astrologer-language-options" class="form-control @error('languages') is-invalid @enderror" value="{{ old('languages', $astrologer->languages) }}" placeholder="Hindi, English">
                        <datalist id="astrologer-language-options">
                            @foreach(['Hindi', 'English', 'Tamil', 'Telugu', 'Bengali', 'Marathi', 'Gujarati', 'Punjabi'] as $language)
                                <option value="{{ $language }}">
                            @endforeach
                        </datalist>
                        <div class="form-text">Available languages: Hindi, English, Tamil, Telugu, Bengali, Marathi, Gujarati, Punjabi.</div>
                        @error('languages')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold small">Education / Credentials</label>
                        <input type="text" name="education" class="form-control @error('education') is-invalid @enderror" value="{{ old('education', $astrologer->education) }}">
                        @error('education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- Rates --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-currency-rupee me-2"></i>Consultation Rates (₹/min)</div>
                <div class="row g-3">
                    @foreach([['chat_rate','Chat','bi-chat','text-primary'],['call_rate','Call','bi-telephone','text-success'],['video_rate','Video','bi-camera-video','text-warning']] as [$field, $label, $icon, $color])
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small"><i class="bi {{ $icon }} {{ $color }} me-1"></i>{{ $label }} Rate</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" name="{{ $field }}" class="form-control @error($field) is-invalid @enderror" value="{{ old($field, $astrologer->$field) }}" min="0" step="0.01">
                            <span class="input-group-text">/min</span>
                        </div>
                        @error($field)<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Services --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-gem me-2"></i>Services Offered</div>
                <div class="row g-2">
                    @php $assignedIds = old('service_ids', $astrologer->services->pluck('id')->toArray()); @endphp
                    @foreach($services as $svc)
                        <div class="col-6 col-md-4">
                            <label class="service-checkbox-item d-flex align-items-center gap-2 w-100">
                                <input type="checkbox" name="service_ids[]" value="{{ $svc->id }}" @checked(in_array($svc->id, $assignedIds))>
                                @if($svc->icon)<i class="{{ $svc->icon }}"></i>@endif
                                <span class="small">{{ $svc->name }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            {{-- Availability Schedule --}}
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-section-title mb-0"><i class="bi bi-calendar-week me-2"></i>Weekly Consultation Availability</div>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSetStandardHours">
                        <i class="bi bi-clock me-1"></i>Standard (Mon–Sat 09:00–18:00)
                    </button>
                </div>
                <p class="text-muted small mb-3">Specify working hours per day. Turn off toggle for days the astrologer is off duty.</p>

                @php
                    $existingAvailability = $astrologer->availability->keyBy('day_of_week');
                @endphp

                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 140px;">Day of Week</th>
                                <th style="width: 90px;" class="text-center">Active</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($days as $day)
                                @php
                                    $slot = $existingAvailability->get($day);
                                    $slotActive = old("availability.$day.is_active", $slot ? $slot->is_active : false);
                                    $startTime = old("availability.$day.start_time", $slot ? \Carbon\Carbon::parse($slot->start_time)->format('H:i') : '09:00');
                                    $endTime = old("availability.$day.end_time", $slot ? \Carbon\Carbon::parse($slot->end_time)->format('H:i') : '18:00');
                                @endphp
                                <tr>
                                    <td class="fw-semibold">
                                        <input type="hidden" name="availability[{{ $day }}][day_of_week]" value="{{ $day }}">
                                        {{ ucfirst($day) }}
                                    </td>
                                    <td class="text-center">
                                        <div class="form-check form-switch d-inline-block">
                                            <input type="hidden" name="availability[{{ $day }}][is_active]" value="0">
                                            <input class="form-check-input" type="checkbox"
                                                   name="availability[{{ $day }}][is_active]"
                                                   id="avail_active_{{ $day }}"
                                                   value="1"
                                                   @checked($slotActive)>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="time"
                                               class="form-control form-control-sm"
                                               id="start_{{ $day }}"
                                               name="availability[{{ $day }}][start_time]"
                                               value="{{ $startTime }}">
                                    </td>
                                    <td>
                                        <input type="time"
                                               class="form-control form-control-sm"
                                               id="end_{{ $day }}"
                                               name="availability[{{ $day }}][end_time]"
                                               value="{{ $endTime }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Flags --}}
            <div class="form-section">
                <div class="form-section-title"><i class="bi bi-toggle-on me-2"></i>Flags</div>
                <div class="d-flex flex-wrap gap-4">
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" @checked(old('is_featured', $astrologer->is_featured))>
                        <label class="form-check-label fw-semibold small" for="is_featured"><i class="bi bi-star-fill text-warning me-1"></i>Featured Astrologer</label>
                    </div>
                    <div class="form-check form-switch">
                        <input type="hidden" name="is_available" value="0">
                        <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" @checked(old('is_available', $astrologer->is_available))>
                        <label class="form-check-label fw-semibold small" for="is_available"><i class="bi bi-circle-fill text-success me-1" style="font-size:0.7rem;"></i>Currently Available</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 mb-4">
                <button type="submit" class="btn btn-primary px-5"><i class="bi bi-save me-2"></i>Save Changes</button>
                <a href="{{ route('admin.astrologers.show', $astrologer) }}" class="btn btn-outline-secondary px-4">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Quick Actions Sidebar --}}
    <div class="col-12 col-xl-3">
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h6>

                @if($astrologer->status === 'pending')
                    <form action="{{ route('admin.astrologers.approve', $astrologer) }}" method="POST" class="mb-2">
                        @csrf
                        <button class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Approve Astrologer</button>
                    </form>
                @endif

                @if(in_array($astrologer->status, ['pending','active']))
                    <button class="btn btn-outline-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-1"></i>Reject Astrologer
                    </button>
                @endif

                <form action="{{ route('admin.astrologers.toggle-featured', $astrologer) }}" method="POST" class="mb-2">
                    @csrf
                    <button class="btn btn-outline-warning w-100">
                        <i class="bi bi-star{{ $astrologer->is_featured ? '-fill' : '' }} me-1"></i>
                        {{ $astrologer->is_featured ? 'Remove from Featured' : 'Mark as Featured' }}
                    </button>
                </form>

                <form action="{{ route('admin.astrologers.toggle-availability', $astrologer) }}" method="POST">
                    @csrf
                    <button class="btn btn-outline-{{ $astrologer->is_available ? 'secondary' : 'success' }} w-100">
                        <i class="bi bi-circle{{ $astrologer->is_available ? '' : '-fill' }} me-1"></i>
                        {{ $astrologer->is_available ? 'Set Offline' : 'Set Online' }}
                    </button>
                </form>
            </div>
        </div>

        {{-- Meta info --}}
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Meta</h6>
                <div class="small text-muted">
                    <div class="mb-1">Created: {{ $astrologer->created_at->format('d M Y') }}</div>
                    <div class="mb-1">Updated: {{ $astrologer->updated_at->diffForHumans() }}</div>
                    @if($astrologer->approved_at)
                        <div class="mb-1">Approved: {{ $astrologer->approved_at->format('d M Y') }}</div>
                    @endif
                    <div>Consultations: {{ number_format($astrologer->total_consultations) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.astrologers.reject', $astrologer) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Astrologer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="4" required placeholder="Provide a clear reason that will help the astrologer understand what needs improvement…"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.getElementById('profile_image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('imagePreview');
                const placeholder = document.getElementById('imagePreviewPlaceholder');
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (placeholder) placeholder.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    // Helper to set standard working hours across weekdays
    document.getElementById('btnSetStandardHours')?.addEventListener('click', function() {
        const weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        const allDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        allDays.forEach(day => {
            const toggle = document.getElementById('avail_active_' + day);
            const start = document.getElementById('start_' + day);
            const end = document.getElementById('end_' + day);

            if (weekdays.includes(day)) {
                if (toggle) toggle.checked = true;
                if (start) start.value = '09:00';
                if (end) end.value = (day === 'saturday') ? '15:00' : '18:00';
            } else {
                if (toggle) toggle.checked = false;
            }
        });
    });
</script>
@endpush
