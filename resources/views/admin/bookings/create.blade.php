@extends('layouts.admin')

@section('title', 'Book Consultation — Admin Portal')
@section('page_title', 'Book Consultation on Behalf of Customer')

@push('styles')
<style>
    .booking-admin-card {
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        background: #FFFFFF;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .booking-admin-header {
        background: #F8FAFC;
        border-bottom: 1px solid #E2E8F0;
        padding: 14px 20px;
        font-weight: 700;
        font-size: 0.95rem;
        color: #1E293B;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .booking-mode-card {
        border: 2px solid #E2E8F0;
        border-radius: 10px;
        padding: 14px 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        background: #FFFFFF;
    }
    .booking-mode-card:hover {
        border-color: #F5B041;
        background: #FFFDF9;
    }
    .booking-mode-card.selected {
        border-color: #6C3483;
        background: rgba(108, 52, 131, 0.05);
        box-shadow: 0 4px 12px rgba(108, 52, 131, 0.12);
    }
    .slot-pill {
        border: 1px solid #CBD5E1;
        border-radius: 8px;
        padding: 7px 10px;
        font-size: 0.82rem;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: all 0.15s ease;
        background: #FFFFFF;
        color: #334155;
    }
    .slot-pill:hover:not(.disabled) {
        border-color: #6C3483;
        background: #F5F3FF;
        color: #6C3483;
    }
    .slot-pill.selected {
        background: #6C3483;
        color: #FFFFFF;
        border-color: #6C3483;
    }
    .slot-pill.disabled {
        background: #F1F5F9;
        color: #94A3B8;
        border-color: #E2E8F0;
        cursor: not-allowed;
        text-decoration: line-through;
    }
    .duration-pill {
        border: 1px solid #CBD5E1;
        background: #FFFFFF;
        padding: 6px 14px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }
    .duration-pill.active {
        background: #1E293B;
        color: #FFFFFF;
        border-color: #1E293B;
    }
    .sticky-summary {
        position: sticky;
        top: 80px;
    }
</style>
@endpush

@section('content')
<div class="mb-3 d-flex justify-content-between align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.bookings.index') }}">Bookings</a></li>
            <li class="breadcrumb-item active">New Consultation Booking</li>
        </ol>
    </nav>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to All Bookings
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please resolve the following errors:</h6>
        <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form action="{{ route('admin.bookings.store') }}" method="POST" id="adminBookingForm">
    @csrf

    <input type="hidden" name="consultation_type" id="inputConsultationType" value="{{ old('consultation_type', $preselectedType) }}">
    <input type="hidden" name="duration_minutes" id="inputDuration" value="{{ old('duration_minutes', 30) }}">
    <input type="hidden" name="start_time" id="inputStartTime" value="{{ old('start_time') }}">

    <div class="row g-4">
        <!-- Main Form Column -->
        <div class="col-lg-8">

            {{-- Section 1: Customer Selection --}}
            <div class="booking-admin-card">
                <div class="booking-admin-header">
                    <span class="badge bg-primary text-white me-1">1</span>
                    Customer Identification
                </div>
                <div class="p-3 p-md-4">
                    <div class="mb-3">
                        <div class="form-check form-check-inline me-4">
                            <input class="form-check-input" type="radio" name="is_new_user" id="userTypeExisting" value="0" @checked(!old('is_new_user')) onchange="toggleUserType(false)">
                            <label class="form-check-label fw-semibold" for="userTypeExisting">
                                <i class="bi bi-person-check me-1 text-primary"></i> Select Registered Customer
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="is_new_user" id="userTypeNew" value="1" @checked(old('is_new_user')) onchange="toggleUserType(true)">
                            <label class="form-check-label fw-semibold" for="userTypeNew">
                                <i class="bi bi-person-plus me-1 text-success"></i> Quick-Register New Customer
                            </label>
                        </div>
                    </div>

                    {{-- Existing User Dropdown --}}
                    <div id="existingUserBlock" class="{{ old('is_new_user') ? 'd-none' : '' }}">
                        <label for="selectUserId" class="form-label small fw-semibold text-secondary">
                            Search & Select Registered Customer <span class="text-danger">*</span>
                        </label>
                        <select name="user_id" id="selectUserId" class="form-select">
                            <option value="">— Choose customer —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}"
                                        data-name="{{ $u->full_name }}"
                                        data-email="{{ $u->email }}"
                                        data-phone="{{ $u->phone }}"
                                        @selected(old('user_id', $preselectedUserId) == $u->id)>
                                    {{ $u->full_name }} ({{ $u->email }}) @if($u->phone) · {{ $u->phone }} @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small">Select the customer account requesting this consultation.</div>
                    </div>

                    {{-- New User Inputs --}}
                    <div id="newUserBlock" class="{{ old('is_new_user') ? '' : 'd-none' }}">
                        <div class="p-3 bg-light rounded-3 border mb-2">
                            <h6 class="fw-bold text-dark mb-2 small"><i class="bi bi-person-badge me-1"></i> New Customer Information</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="new_user_first_name" id="newUserFirstName" class="form-control form-control-sm" value="{{ old('new_user_first_name') }}" placeholder="e.g. Ramesh">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Last Name</label>
                                    <input type="text" name="new_user_last_name" id="newUserLastName" class="form-control form-control-sm" value="{{ old('new_user_last_name') }}" placeholder="e.g. Sharma">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="new_user_email" id="newUserEmail" class="form-control form-control-sm" value="{{ old('new_user_email') }}" placeholder="e.g. ramesh@example.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Phone Number</label>
                                    <input type="text" name="new_user_phone" id="newUserPhone" class="form-control form-control-sm" value="{{ old('new_user_phone') }}" placeholder="e.g. +91 98765 43210">
                                </div>
                            </div>
                            <div class="form-text small mt-1">An account will be automatically provisioned for this customer with active status.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Astrologer & Service Selection --}}
            <div class="booking-admin-card">
                <div class="booking-admin-header">
                    <span class="badge bg-primary text-white me-1">2</span>
                    Astrologer & Consultation Topic
                </div>
                <div class="p-3 p-md-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label for="selectAstrologer" class="form-label small fw-semibold text-secondary">
                                Select Astrologer <span class="text-danger">*</span>
                            </label>
                            <select name="astrologer_id" id="selectAstrologer" class="form-select" required onchange="handleAstrologerChange()">
                                <option value="">— Select an Astrologer —</option>
                                @foreach($astrologers as $astro)
                                    <option value="{{ $astro->id }}"
                                            data-name="{{ $astro->display_name }}"
                                            data-chat="{{ $astro->chat_rate }}"
                                            data-call="{{ $astro->call_rate }}"
                                            data-video="{{ $astro->video_rate }}"
                                            data-exp="{{ $astro->experience_years }}"
                                            data-services="{{ $astro->services->pluck('id')->join(',') }}"
                                            @selected(old('astrologer_id', $preselectedAstrologerId) == $astro->id)>
                                        {{ $astro->display_name }} (₹{{ number_format($astro->chat_rate, 0) }}/m Chat · ₹{{ number_format($astro->call_rate, 0) }}/m Call · ₹{{ number_format($astro->video_rate, 0) }}/m Video)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="selectService" class="form-label small fw-semibold text-secondary">
                                Specialized Discipline / Service
                            </label>
                            <select name="service_id" id="selectService" class="form-select">
                                <option value="">— General Consultation —</option>
                                @foreach($services as $svc)
                                    <option value="{{ $svc->id }}" @selected(old('service_id', $preselectedServiceId) == $svc->id)>
                                        {{ $svc->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Section 3: Consultation Mode & Duration --}}
                    <div class="row g-3 pt-2 border-top">
                        <div class="col-12">
                            <label class="form-label small fw-semibold text-secondary mb-2">Select Consultation Mode</label>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="booking-mode-card {{ old('consultation_type', $preselectedType) === 'chat' ? 'selected' : '' }}" data-type="chat" onclick="selectMode('chat')">
                                        <i class="bi bi-chat-dots fs-3 text-primary d-block mb-1"></i>
                                        <div class="fw-bold small text-dark">Live Chat</div>
                                        <div class="small text-muted" id="chatRateLabel">₹0/min</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="booking-mode-card {{ old('consultation_type', $preselectedType) === 'call' ? 'selected' : '' }}" data-type="call" onclick="selectMode('call')">
                                        <i class="bi bi-telephone fs-3 text-success d-block mb-1"></i>
                                        <div class="fw-bold small text-dark">Voice Call</div>
                                        <div class="small text-muted" id="callRateLabel">₹0/min</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="booking-mode-card {{ old('consultation_type', $preselectedType) === 'video' ? 'selected' : '' }}" data-type="video" onclick="selectMode('video')">
                                        <i class="bi bi-camera-video fs-3 text-warning d-block mb-1"></i>
                                        <div class="fw-bold small text-dark">Video Call</div>
                                        <div class="small text-muted" id="videoRateLabel">₹0/min</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label small fw-semibold text-secondary mb-2">Consultation Duration</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach([15 => '15 Minutes', 30 => '30 Minutes (Standard)', 45 => '45 Minutes', 60 => '60 Minutes (Full Reading)'] as $mins => $label)
                                    <button type="button" class="duration-pill {{ old('duration_minutes', 30) == $mins ? 'active' : '' }}" data-duration="{{ $mins }}" onclick="selectDuration({{ $mins }})">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Date & Slots --}}
            <div class="booking-admin-card">
                <div class="booking-admin-header">
                    <span class="badge bg-primary text-white me-1">3</span>
                    Date & Appointment Time Slot
                </div>
                <div class="p-3 p-md-4">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="bookingDatePicker" class="form-label small fw-semibold text-secondary">
                                Consultation Date <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   id="bookingDatePicker"
                                   name="booking_date"
                                   class="form-control"
                                   value="{{ old('booking_date', date('Y-m-d')) }}"
                                   required
                                   onchange="fetchSlots()">
                        </div>
                        <div class="col-md-6">
                            <label for="manualStartTime" class="form-label small fw-semibold text-secondary">
                                Selected / Custom Start Time (HH:MM) <span class="text-danger">*</span>
                            </label>
                            <input type="time"
                                   id="manualStartTime"
                                   class="form-control"
                                   value="{{ old('start_time') }}"
                                   required
                                   onchange="handleManualTimeChange(this.value)">
                            <div class="form-text small">Select from slots below or type any custom time.</div>
                        </div>
                    </div>

                    {{-- Live Slots Container --}}
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold small text-dark mb-0">
                                <i class="bi bi-clock me-1 text-primary"></i> Astrologer's Working Slots for Selected Date:
                            </label>
                            <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" onclick="fetchSlots()">
                                <i class="bi bi-arrow-repeat me-1"></i> Refresh Slots
                            </button>
                        </div>

                        <div id="slotsSpinner" class="text-center py-3 d-none">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                            <span class="small text-muted ms-2">Checking schedule & active appointments...</span>
                        </div>

                        <div id="slotsAlert" class="alert alert-info py-2 small d-none mb-2"></div>

                        <div id="slotsWrapper">
                            <div id="slotsGrid" class="row g-2">
                                <div class="col-12 text-muted small py-2">
                                    <em>Please select an astrologer to load available schedule slots.</em>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Customer Inquiry / Birth Details & Admin Notes --}}
            <div class="booking-admin-card">
                <div class="booking-admin-header">
                    <span class="badge bg-primary text-white me-1">4</span>
                    Birth Details, Questions & Administrative Notes
                </div>
                <div class="p-3 p-md-4">
                    <div class="mb-3">
                        <label for="bookingNotes" class="form-label small fw-semibold text-secondary">
                            Client Birth Details & Questions (Optional)
                        </label>
                        <textarea name="notes" id="bookingNotes" rows="3" class="form-control" placeholder="e.g. DOB: 22-Aug-1992, Time: 06:15 AM, City: Delhi. Questions: Career change and health remedies.">{{ old('notes') }}</textarea>
                    </div>

                    <div>
                        <label for="adminNotes" class="form-label small fw-semibold text-secondary">
                            Internal Administrative Notes (Optional)
                        </label>
                        <textarea name="admin_notes" id="adminNotes" rows="2" class="form-control" placeholder="e.g. Telephonic booking assisted by Support Team. Payment confirmed via phone.">{{ old('admin_notes') }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sidebar / Sticky Summary Column -->
        <div class="col-lg-4">
            <div class="sticky-summary">
                <div class="booking-admin-card border-top border-4 border-warning">
                    <div class="booking-admin-header bg-white">
                        <i class="bi bi-receipt me-1 text-warning"></i>
                        Booking Summary & Billing
                    </div>
                    <div class="p-3 p-md-4">
                        <ul class="list-group list-group-flush small mb-3">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Customer:</span>
                                <span class="fw-semibold text-dark text-end" id="summaryCustomer">—</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Astrologer:</span>
                                <span class="fw-semibold text-dark text-end" id="summaryAstrologer">—</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Mode:</span>
                                <span class="fw-semibold text-dark text-capitalize" id="summaryMode">Chat</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Duration:</span>
                                <span class="fw-semibold text-dark" id="summaryDuration">30 Minutes</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Date:</span>
                                <span class="fw-semibold text-dark" id="summaryDate">{{ date('d M Y') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Time Slot:</span>
                                <span class="fw-bold text-primary" id="summaryTime">Not Selected</span>
                            </li>
                        </ul>

                        {{-- Status & Payment --}}
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Initial Booking Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="confirmed" @selected(old('status', 'confirmed') === 'confirmed')>Confirmed (Scheduled)</option>
                                <option value="pending" @selected(old('status') === 'pending')>Pending</option>
                                <option value="completed" @selected(old('status') === 'completed')>Completed (Historical / Done)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-secondary">Payment Status</label>
                            <select name="payment_status" class="form-select form-select-sm">
                                <option value="paid" @selected(old('payment_status', 'paid') === 'paid')>Paid (Cash / UPI / Handled)</option>
                                <option value="pending" @selected(old('payment_status') === 'pending')>Pending Payment</option>
                                <option value="refunded" @selected(old('payment_status') === 'refunded')>Refunded / Waived</option>
                            </select>
                        </div>

                        {{-- Pricing Box --}}
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">Calculated Fee:</span>
                                <span class="fw-bold fs-5 text-dark" id="calculatedAmount">₹0.00</span>
                            </div>
                            <div class="pt-2 border-top">
                                <label for="customAmount" class="form-label small text-muted mb-1">
                                    Override Fee (Optional):
                                </label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" step="0.01" min="0" name="custom_amount" id="customAmount" class="form-control" value="{{ old('custom_amount') }}" placeholder="Custom fee..." oninput="updateSummary()">
                                </div>
                                <div class="form-text small" style="font-size: 0.72rem;">Leave empty to charge the standard rate.</div>
                            </div>
                        </div>

                        {{-- Override Conflict Checkbox --}}
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="override_conflict" id="overrideConflict" value="1" @checked(old('override_conflict'))>
                            <label class="form-check-label small text-muted" for="overrideConflict">
                                <strong>Admin Override:</strong> Allow booking even if slot overlaps with an existing appointment.
                            </label>
                        </div>

                        <button type="submit" id="btnSubmitBooking" class="btn btn-primary w-100 py-2 fw-bold">
                            <i class="bi bi-calendar-check me-1"></i> Create Consultation Booking
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    let currentAstro = null;
    let selectedMode = "{{ old('consultation_type', $preselectedType) }}";
    let selectedDuration = {{ old('duration_minutes', 30) }};

    function toggleUserType(isNew) {
        document.getElementById('existingUserBlock').classList.toggle('d-none', isNew);
        document.getElementById('newUserBlock').classList.toggle('d-none', !isNew);
        updateSummary();
    }

    function selectMode(mode) {
        selectedMode = mode;
        document.getElementById('inputConsultationType').value = mode;

        document.querySelectorAll('.booking-mode-card').forEach(el => {
            el.classList.toggle('selected', el.getAttribute('data-type') === mode);
        });

        updateSummary();
    }

    function selectDuration(mins) {
        selectedDuration = mins;
        document.getElementById('inputDuration').value = mins;

        document.querySelectorAll('.duration-pill').forEach(el => {
            el.classList.toggle('active', parseInt(el.getAttribute('data-duration')) === mins);
        });

        updateSummary();
        fetchSlots();
    }

    function handleManualTimeChange(val) {
        document.getElementById('inputStartTime').value = val;

        // Deselect slot pills that do not match
        document.querySelectorAll('.slot-pill').forEach(pill => {
            pill.classList.toggle('selected', pill.getAttribute('data-time') === val);
        });

        updateSummary();
    }

    function handleAstrologerChange() {
        const select = document.getElementById('selectAstrologer');
        const selectedOption = select.options[select.selectedIndex];

        if (selectedOption && selectedOption.value) {
            currentAstro = {
                id: selectedOption.value,
                name: selectedOption.getAttribute('data-name'),
                chatRate: parseFloat(selectedOption.getAttribute('data-chat')) || 0,
                callRate: parseFloat(selectedOption.getAttribute('data-call')) || 0,
                videoRate: parseFloat(selectedOption.getAttribute('data-video')) || 0,
            };

            document.getElementById('chatRateLabel').innerText = `₹${currentAstro.chatRate}/min`;
            document.getElementById('callRateLabel').innerText = `₹${currentAstro.callRate}/min`;
            document.getElementById('videoRateLabel').innerText = `₹${currentAstro.videoRate}/min`;

            fetchSlots();
        } else {
            currentAstro = null;
            document.getElementById('chatRateLabel').innerText = `₹0/min`;
            document.getElementById('callRateLabel').innerText = `₹0/min`;
            document.getElementById('videoRateLabel').innerText = `₹0/min`;
            document.getElementById('slotsGrid').innerHTML = '<div class="col-12 text-muted small py-2"><em>Please select an astrologer to load available schedule slots.</em></div>';
        }

        updateSummary();
    }

    function fetchSlots() {
        const astroId = document.getElementById('selectAstrologer').value;
        const date = document.getElementById('bookingDatePicker').value;
        const duration = selectedDuration;

        if (!astroId || !date) {
            return;
        }

        const spinner = document.getElementById('slotsSpinner');
        const alertBox = document.getElementById('slotsAlert');
        const grid = document.getElementById('slotsGrid');

        spinner.classList.remove('d-none');
        alertBox.classList.add('d-none');
        grid.innerHTML = '';

        const url = `{{ route('admin.bookings.slots') }}?astrologer_id=${encodeURIComponent(astroId)}&date=${encodeURIComponent(date)}&duration=${encodeURIComponent(duration)}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                spinner.classList.add('d-none');

                if (!data.is_working_day) {
                    alertBox.className = 'alert alert-warning py-2 small mb-2';
                    alertBox.innerText = data.message || 'Astrologer is not scheduled to work on this day according to weekly roster.';
                    alertBox.classList.remove('d-none');
                }

                if (!data.slots || data.slots.length === 0) {
                    grid.innerHTML = '<div class="col-12 text-muted small py-2">No generated slots found for this date. You can manually enter any start time above.</div>';
                    return;
                }

                const currentSelectedTime = document.getElementById('inputStartTime').value;

                data.slots.forEach(slot => {
                    const col = document.createElement('div');
                    col.className = 'col-6 col-sm-4 col-md-3';

                    const pill = document.createElement('div');
                    pill.className = `slot-pill ${slot.is_available ? '' : (slot.is_booked ? 'disabled' : 'disabled')} ${slot.start_time === currentSelectedTime ? 'selected' : ''}`;
                    pill.setAttribute('data-time', slot.start_time);

                    let statusText = '';
                    if (slot.is_booked) statusText = ' (Booked)';
                    else if (slot.is_past) statusText = ' (Past)';

                    pill.innerHTML = `<div>${slot.start_time}</div><small style="font-size:0.68rem;opacity:0.8;">${statusText}</small>`;

                    pill.onclick = function() {
                        document.querySelectorAll('.slot-pill').forEach(p => p.classList.remove('selected'));
                        pill.classList.add('selected');
                        document.getElementById('manualStartTime').value = slot.start_time;
                        document.getElementById('inputStartTime').value = slot.start_time;
                        updateSummary();
                    };

                    col.appendChild(pill);
                    grid.appendChild(col);
                });
            })
            .catch(err => {
                spinner.classList.add('d-none');
                alertBox.className = 'alert alert-danger py-2 small mb-2';
                alertBox.innerText = 'Unable to check live slots. You can still enter time manually.';
                alertBox.classList.remove('d-none');
            });
    }

    function updateSummary() {
        // Customer
        const isNew = document.getElementById('userTypeNew').checked;
        let customerName = '—';
        if (isNew) {
            const fName = document.getElementById('newUserFirstName').value || '';
            const lName = document.getElementById('newUserLastName').value || '';
            customerName = (fName + ' ' + lName).trim() || 'New Customer';
        } else {
            const userSelect = document.getElementById('selectUserId');
            if (userSelect.selectedIndex > 0) {
                customerName = userSelect.options[userSelect.selectedIndex].getAttribute('data-name');
            }
        }
        document.getElementById('summaryCustomer').innerText = customerName;

        // Astrologer
        const astroSelect = document.getElementById('selectAstrologer');
        if (astroSelect.selectedIndex > 0) {
            document.getElementById('summaryAstrologer').innerText = astroSelect.options[astroSelect.selectedIndex].getAttribute('data-name');
        } else {
            document.getElementById('summaryAstrologer').innerText = '—';
        }

        // Mode & Duration
        document.getElementById('summaryMode').innerText = selectedMode.toUpperCase();
        document.getElementById('summaryDuration').innerText = `${selectedDuration} Minutes`;

        // Date & Time
        const dateVal = document.getElementById('bookingDatePicker').value;
        if (dateVal) {
            const dateObj = new Date(dateVal + 'T00:00:00');
            document.getElementById('summaryDate').innerText = dateObj.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
        }
        const timeVal = document.getElementById('inputStartTime').value;
        document.getElementById('summaryTime').innerText = timeVal ? `${timeVal} (${selectedDuration}m)` : 'Not Selected';

        // Price calculation
        let rate = 0;
        if (currentAstro) {
            if (selectedMode === 'call') rate = currentAstro.callRate;
            else if (selectedMode === 'video') rate = currentAstro.videoRate;
            else rate = currentAstro.chatRate;
        }

        const calculated = rate * selectedDuration;
        document.getElementById('calculatedAmount').innerText = `₹${calculated.toFixed(2)}`;
    }

    // Auto-init on load
    document.addEventListener('DOMContentLoaded', function() {
        handleAstrologerChange();
        if (document.getElementById('manualStartTime').value) {
            document.getElementById('inputStartTime').value = document.getElementById('manualStartTime').value;
        }
        updateSummary();
    });
</script>
@endpush
@endsection
