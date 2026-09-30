@extends('layouts.app')

@section('title', "Book Consultation with {$astrologer->display_name} — AstroVani")

@push('styles')
<style>
    .booking-hero {
        background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%);
        border-bottom: 3px solid #F5B041;
        padding: 40px 0;
        color: white;
    }
    .booking-astro-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #F5B041;
    }
    .booking-astro-ph {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2D124D, #6C3483);
        color: #F5B041;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.8rem;
        border: 2px solid #F5B041;
    }
    .consult-type-card {
        border: 2px solid #E5E7EB;
        border-radius: 12px;
        padding: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        background: #FFFFFF;
    }
    .consult-type-card:hover {
        border-color: rgba(245, 176, 65, 0.6);
        background: #FFFDF9;
    }
    .consult-type-card.selected {
        border-color: #F5B041;
        background: rgba(245, 176, 65, 0.08);
        box-shadow: 0 4px 12px rgba(245, 176, 65, 0.15);
    }
    .slot-pill {
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background: #FFFFFF;
    }
    .slot-pill:hover:not(.disabled) {
        border-color: #F5B041;
        background: #FFFDF9;
    }
    .slot-pill.selected {
        background: #1A0B2E;
        color: #F5B041;
        border-color: #1A0B2E;
    }
    .slot-pill.disabled {
        background: #F3F4F6;
        color: #9CA3AF;
        border-color: #E5E7EB;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .duration-btn {
        border: 1px solid #D1D5DB;
        background: #FFFFFF;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .duration-btn.active {
        background: var(--astro-purple);
        color: #FFFFFF;
        border-color: var(--astro-purple);
    }
</style>
@endpush

@section('content')
<!-- Header Hero -->
<div class="booking-hero">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="font-size: 0.82rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('astrologers.index') }}" class="text-white-50 text-decoration-none">Astrologers</a></li>
                <li class="breadcrumb-item"><a href="{{ route('astrologers.show', $astrologer->slug) }}" class="text-white-50 text-decoration-none">{{ $astrologer->display_name }}</a></li>
                <li class="breadcrumb-item active text-warning" aria-current="page">Book Consultation</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3 mt-2 flex-wrap">
            @if($astrologer->profile_image)
                <img src="{{ asset('storage/' . $astrologer->profile_image) }}" alt="{{ $astrologer->display_name }}" class="booking-astro-avatar">
            @else
                <div class="booking-astro-ph">{{ strtoupper(substr($astrologer->display_name, 0, 1)) }}</div>
            @endif
            <div>
                <span class="badge bg-white bg-opacity-10 text-warning px-3 py-1 mb-1 border border-warning border-opacity-25">
                    <i class="bi bi-calendar-check me-1"></i> Instant Appointment Scheduling
                </span>
                <h2 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Book Session with {{ $astrologer->display_name }}</h2>
                <div class="text-white-50 small">
                    {{ $astrologer->specializations }} &bull; {{ $astrologer->experience_years }}+ yrs exp &bull; ★ {{ number_format($astrologer->rating_avg, 1) }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-x-circle me-1"></i> Please correct the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
        @csrf
        <input type="hidden" name="astrologer_id" value="{{ $astrologer->id }}">
        <input type="hidden" name="consultation_type" id="inputConsultationType" value="{{ old('consultation_type', $preselectedType) }}">
        <input type="hidden" name="duration_minutes" id="inputDuration" value="{{ old('duration_minutes', 30) }}">
        <input type="hidden" name="start_time" id="inputStartTime" value="{{ old('start_time') }}">

        <div class="row g-4">
            <!-- Left Column: Booking Options -->
            <div class="col-lg-8">
                {{-- 1. Consultation Mode --}}
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">
                        <span class="badge bg-warning text-dark me-2">1</span>Select Consultation Mode
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="consult-type-card {{ old('consultation_type', $preselectedType) === 'chat' ? 'selected' : '' }}" data-type="chat" data-rate="{{ $astrologer->chat_rate }}">
                                <i class="bi bi-chat-dots fs-2 text-primary d-block mb-1"></i>
                                <div class="fw-bold text-dark">Live Chat</div>
                                <div class="text-muted small">Real-time messaging</div>
                                <div class="mt-2 fw-bold text-primary">₹{{ number_format($astrologer->chat_rate, 0) }}<small class="text-muted">/min</small></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="consult-type-card {{ old('consultation_type', $preselectedType) === 'call' ? 'selected' : '' }}" data-type="call" data-rate="{{ $astrologer->call_rate }}">
                                <i class="bi bi-telephone fs-2 text-success d-block mb-1"></i>
                                <div class="fw-bold text-dark">Voice Call</div>
                                <div class="text-muted small">Private audio call</div>
                                <div class="mt-2 fw-bold text-success">₹{{ number_format($astrologer->call_rate, 0) }}<small class="text-muted">/min</small></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="consult-type-card {{ old('consultation_type', $preselectedType) === 'video' ? 'selected' : '' }}" data-type="video" data-rate="{{ $astrologer->video_rate }}">
                                <i class="bi bi-camera-video fs-2 text-warning d-block mb-1"></i>
                                <div class="fw-bold text-dark">Video Session</div>
                                <div class="text-muted small">Face-to-face Kundli reading</div>
                                <div class="mt-2 fw-bold text-warning">₹{{ number_format($astrologer->video_rate, 0) }}<small class="text-muted">/min</small></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Consultation Duration --}}
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">
                        <span class="badge bg-warning text-dark me-2">2</span>Select Consultation Duration
                    </h5>
                    <div class="d-flex flex-wrap gap-3">
                        @foreach([15 => '15 Minutes', 30 => '30 Minutes (Recommended)', 45 => '45 Minutes', 60 => '60 Minutes (Full Reading)'] as $mins => $label)
                            <button type="button" class="duration-btn {{ old('duration_minutes', 30) == $mins ? 'active' : '' }}" data-duration="{{ $mins }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- 3. Service Discipline --}}
                @if($astrologer->services->isNotEmpty())
                    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                        <h5 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif;">
                            <span class="badge bg-warning text-dark me-2">3</span>Select Astrological Topic / Discipline (Optional)
                        </h5>
                        <p class="text-muted small mb-3">Choose the primary area you want the astrologer to analyze during your session.</p>
                        <select name="service_id" class="form-select">
                            <option value="">— General Life Reading & Guidance —</option>
                            @foreach($astrologer->services as $svc)
                                <option value="{{ $svc->id }}" @selected(old('service_id', $preselectedServiceId) == $svc->id)>
                                    {{ $svc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- 4. Date & Live Slot Picker --}}
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">
                        <span class="badge bg-warning text-dark me-2">4</span>Select Consultation Date & Time Slot
                    </h5>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Consultation Date <span class="text-danger">*</span></label>
                            <input type="date"
                                   id="bookingDatePicker"
                                   name="booking_date"
                                   class="form-control"
                                   min="{{ date('Y-m-d') }}"
                                   value="{{ old('booking_date', date('Y-m-d')) }}"
                                   required>
                            <div class="form-text small">
                                Available days: <strong>{{ implode(', ', array_map('ucfirst', $activeDays)) }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Slot Loading Spinner --}}
                    <div id="slotsLoading" class="text-center py-4 d-none">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading available slots...</span>
                        </div>
                        <div class="small text-muted mt-2">Checking astrologer's live schedule...</div>
                    </div>

                    {{-- Slot Error Message --}}
                    <div id="slotsAlert" class="alert alert-warning d-none"></div>

                    {{-- Slots Container --}}
                    <div id="slotsContainer">
                        <label class="form-label fw-semibold small mb-2">Available 30-Min Time Slots <span class="text-danger">*</span></label>
                        <div id="slotsGrid" class="row g-2">
                            <!-- Populated dynamically via AJAX -->
                        </div>
                    </div>
                </div>

                {{-- 5. Birth Details & Notes --}}
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 14px;">
                    <h5 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif;">
                        <span class="badge bg-warning text-dark me-2">5</span>Consultation Details & Questions (Optional)
                    </h5>
                    <p class="text-muted small mb-3">Provide birth details (Date, Time, Place of Birth) or specific questions for accurate chart preparation.</p>
                    <textarea name="notes" class="form-control" rows="4" placeholder="e.g. Birth: 15-Aug-1995, 08:30 AM, Bengaluru. Looking for guidance on career change and marriage timeline.">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Right Column: Sticky Summary & Confirmation -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 90px; border-radius: 14px; border-top: 4px solid #F5B041 !important;">
                    <h5 class="fw-bold mb-3" style="font-family: 'Outfit', sans-serif;">Appointment Summary</h5>

                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Astrologer</span>
                            <span class="fw-semibold text-dark">{{ $astrologer->display_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Mode</span>
                            <span class="fw-semibold text-dark text-capitalize" id="summaryMode">Chat</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Duration</span>
                            <span class="fw-semibold text-dark" id="summaryDuration">30 Minutes</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Date</span>
                            <span class="fw-semibold text-dark" id="summaryDate">{{ date('d M Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span class="text-muted">Time Slot</span>
                            <span class="fw-semibold text-primary" id="summaryTime">Not Selected</span>
                        </li>
                    </ul>

                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-muted">Consultation Fee:</span>
                            <span class="fw-bold fs-5 text-dark" id="summaryTotal">₹0.00</span>
                        </div>
                        <div class="small text-muted" style="font-size: 0.72rem;">* Pay securely during session or at checkout</div>
                    </div>

                    @auth('web')
                        <button type="submit" id="btnSubmitBooking" class="btn btn-astro-gold w-100 py-3 fw-bold" disabled>
                            <i class="bi bi-calendar-check-fill me-2"></i> Confirm & Schedule Session
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-astro-gold w-100 py-3 fw-bold text-decoration-none text-center">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Login to Confirm Appointment
                        </a>
                        <div class="text-center mt-2 small text-muted">
                            New user? <a href="{{ route('register') }}" class="text-purple-600 fw-semibold">Sign up free</a>
                        </div>
                    @endauth

                    <div class="mt-4 pt-3 border-top small text-muted">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-lock-fill text-success"></i> 100% Private & Confidential
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-shield-check text-primary"></i> Certified Vedic Master
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-warning"></i> Free Cancellation up to 2 hours prior
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slotsUrl = "{{ route('bookings.slots', $astrologer->slug) }}";
    const datePicker = document.getElementById('bookingDatePicker');
    const slotsGrid = document.getElementById('slotsGrid');
    const slotsLoading = document.getElementById('slotsLoading');
    const slotsAlert = document.getElementById('slotsAlert');
    const slotsContainer = document.getElementById('slotsContainer');
    const inputStartTime = document.getElementById('inputStartTime');
    const inputConsultationType = document.getElementById('inputConsultationType');
    const inputDuration = document.getElementById('inputDuration');
    const btnSubmit = document.getElementById('btnSubmitBooking');

    // Summary Elements
    const summaryMode = document.getElementById('summaryMode');
    const summaryDuration = document.getElementById('summaryDuration');
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');
    const summaryTotal = document.getElementById('summaryTotal');

    let currentRate = {{ (float) ($astrologer->chat_rate ?? 25) }};
    let currentDuration = parseInt(inputDuration.value) || 30;

    // Rate dictionary
    const rates = {
        'chat': {{ (float) ($astrologer->chat_rate ?? 25) }},
        'call': {{ (float) ($astrologer->call_rate ?? 30) }},
        'video': {{ (float) ($astrologer->video_rate ?? 40) }}
    };

    function recalculateTotal() {
        const mode = inputConsultationType.value || 'chat';
        currentRate = rates[mode] || rates['chat'];
        const total = currentRate * currentDuration;

        summaryMode.textContent = mode.charAt(0).toUpperCase() + mode.slice(1);
        summaryDuration.textContent = currentDuration + ' Minutes';
        summaryTotal.textContent = '₹' + total.toFixed(2);
    }

    // Consultation Mode selection
    document.querySelectorAll('.consult-type-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.consult-type-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const type = this.getAttribute('data-type');
            inputConsultationType.value = type;
            recalculateTotal();
        });
    });

    // Duration selection
    document.querySelectorAll('.duration-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.duration-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentDuration = parseInt(this.getAttribute('data-duration'));
            inputDuration.value = currentDuration;
            recalculateTotal();
        });
    });

    // Fetch slots via AJAX
    function fetchSlots(selectedDate) {
        if (!selectedDate) return;

        summaryDate.textContent = new Date(selectedDate).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        slotsLoading.classList.remove('d-none');
        slotsAlert.classList.add('d-none');
        slotsGrid.innerHTML = '';
        inputStartTime.value = '';
        summaryTime.textContent = 'Not Selected';
        if (btnSubmit) btnSubmit.disabled = true;

        fetch(`${slotsUrl}?date=${selectedDate}`)
            .then(res => res.json())
            .then(data => {
                slotsLoading.classList.add('d-none');

                if (!data.is_working_day) {
                    slotsAlert.textContent = data.message || 'Astrologer is off-duty on this date. Please choose another day.';
                    slotsAlert.classList.remove('d-none');
                    return;
                }

                if (!data.slots || data.slots.length === 0) {
                    slotsAlert.textContent = 'No consultation slots available on this date.';
                    slotsAlert.classList.remove('d-none');
                    return;
                }

                // Render slots
                data.slots.forEach(slot => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3';

                    const pill = document.createElement('div');
                    pill.className = `slot-pill ${slot.is_available ? '' : 'disabled'}`;
                    pill.textContent = slot.label;
                    pill.setAttribute('data-start', slot.start_time);

                    if (slot.is_available) {
                        pill.addEventListener('click', function() {
                            document.querySelectorAll('.slot-pill').forEach(p => p.classList.remove('selected'));
                            this.classList.add('selected');
                            inputStartTime.value = this.getAttribute('data-start');
                            summaryTime.textContent = this.textContent;
                            if (btnSubmit) btnSubmit.disabled = false;
                        });
                    } else {
                        pill.title = slot.is_booked ? 'Already booked by another user' : 'Time has passed';
                    }

                    col.appendChild(pill);
                    slotsGrid.appendChild(col);
                });
            })
            .catch(err => {
                slotsLoading.classList.add('d-none');
                slotsAlert.textContent = 'Failed to load schedule. Please verify your connection and try again.';
                slotsAlert.classList.remove('d-none');
            });
    }

    // Trigger on date change
    datePicker.addEventListener('change', function() {
        fetchSlots(this.value);
    });

    // Initial load
    recalculateTotal();
    fetchSlots(datePicker.value);
});
</script>
@endpush
