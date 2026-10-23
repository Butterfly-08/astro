@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page_title', 'Master Administration Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <!-- Total Users -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Customers</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($totalUsers) }}</h3>
                <small class="text-success"><i class="bi bi-person-check-fill me-1"></i> {{ $activeUsers }} Active Accounts</small>
            </div>
            <div class="stat-icon icon-purple">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Astrologers -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Astrologers</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($totalAstrologers) }}</h3>
                <div class="d-flex align-items-center gap-1">
                    <small class="text-success"><i class="bi bi-patch-check-fill me-1"></i>{{ $activeAstrologers }} Active</small>
                    @if($pendingAstrologers > 0)
                        <a href="{{ route('admin.astrologers.index', ['status' => 'pending']) }}" class="badge bg-danger text-decoration-none ms-1">
                            {{ $pendingAstrologers }} Pending
                        </a>
                    @endif
                </div>
            </div>
            <div class="stat-icon icon-gold">
                <i class="bi bi-star-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Services -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Consultation Services</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($totalServices) }}</h3>
                <small class="text-muted"><i class="bi bi-gem me-1"></i> {{ $activeServices }} Active Disciplines</small>
            </div>
            <div class="stat-icon icon-blue">
                <i class="bi bi-card-checklist"></i>
            </div>
        </div>
    </div>

    <!-- Total Products -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Shop Products</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($totalProducts) }}</h3>
                <small class="text-muted"><i class="bi bi-box-seam me-1"></i> Catalog Items</small>
            </div>
            <div class="stat-icon icon-green">
                <i class="bi bi-bag-check-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Bookings -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Bookings</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($totalBookings) }}</h3>
                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i> Appointments</small>
            </div>
            <div class="stat-icon icon-purple">
                <i class="bi bi-calendar2-week-fill"></i>
            </div>
        </div>
    </div>

    <!-- Total Orders / Revenue -->
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card d-flex align-items-center justify-content-between">
            <div>
                <span class="text-muted small text-uppercase fw-semibold">Total Orders</span>
                <h3 class="fw-bold my-1 text-dark">{{ number_format($totalOrders) }}</h3>
                <small class="text-success"><i class="bi bi-currency-rupee me-1"></i> Revenue: ₹{{ number_format($totalRevenue, 2) }}</small>
            </div>
            <div class="stat-icon icon-gold">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>
</div>

<!-- Platform Health & Quick Actions -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check text-primary me-2"></i> Platform Health & Security Status</h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> All Systems Operational</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                                <span class="fw-bold">Admin Portal Security</span>
                            </div>
                            <p class="small text-muted mb-2">
                                Dedicated administrator session guard with role verification and active credential protection.
                            </p>
                            <span class="badge bg-success"><i class="bi bi-check2 me-1"></i> Protected</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light h-100">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person-fill-check text-primary fs-5"></i>
                                <span class="fw-bold">Customer Portal Security</span>
                            </div>
                            <p class="small text-muted mb-2">
                                Customer authentication guard with encrypted sessions, cart persistence, and order tracking.
                            </p>
                            <span class="badge bg-success"><i class="bi bi-check2 me-1"></i> Protected</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="fw-semibold text-dark small"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Quick Administrative Shortcuts:</span>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('admin.astrologers.create') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-person-plus-fill me-1"></i> Add Astrologer
                            </a>
                            <a href="{{ route('admin.bookings.create') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-calendar-plus-fill me-1"></i> New Booking
                            </a>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-box-seam me-1"></i> Add Product
                            </a>
                            <a href="{{ route('admin.settings.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-gear-fill me-1"></i> Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge text-primary me-2"></i> Current Admin Session</h6>
            </div>
            <div class="card-body p-4">
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-dark text-warning d-inline-flex align-items-center justify-content-center" style="width: 65px; height: 65px; font-size: 1.8rem;">
                        <i class="bi bi-shield-fill-check"></i>
                    </div>
                    <h5 class="fw-bold mt-2 mb-0">{{ $admin->name ?? 'Administrator' }}</h5>
                    <small class="text-muted">{{ $admin->email ?? 'admin@astrovani.test' }}</small>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Status:</span>
                        <span class="badge bg-success">Active</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Guard:</span>
                        <span class="badge bg-dark">admin</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between px-0">
                        <span class="text-muted">Last Login:</span>
                        <span>{{ $admin->last_login_at ? $admin->last_login_at->diffForHumans() : 'Just now' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@if($pendingAstrologers > 0)
<!-- Pending Astrologer Approvals Alert Section -->
<div class="card border-0 shadow-sm rounded-3 mb-4 border-start border-warning border-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill me-1"></i> Action Required</span>
            <h6 class="mb-0 fw-bold">Astrologer Applications Pending Approval ({{ $pendingAstrologers }})</h6>
        </div>
        <a href="{{ route('admin.astrologers.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning text-dark fw-semibold">
            View All Pending <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Applicant</th>
                        <th>Specializations</th>
                        <th>Experience</th>
                        <th>Rates (C/A/V)</th>
                        <th>Applied On</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingAstrologersList as $pendingAstro)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-semibold text-dark">{{ $pendingAstro->display_name }}</div>
                                <div class="small text-muted">{{ $pendingAstro->email }} &bull; {{ $pendingAstro->phone ?? 'No phone' }}</div>
                            </td>
                            <td>
                                <span class="small">{{ Str::limit($pendingAstro->specializations, 35) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $pendingAstro->experience_years }} yrs</span>
                            </td>
                            <td>
                                <span class="small text-muted">₹{{ number_format($pendingAstro->chat_rate) }} / ₹{{ number_format($pendingAstro->call_rate) }} / ₹{{ number_format($pendingAstro->video_rate) }}</span>
                            </td>
                            <td class="small text-muted">{{ $pendingAstro->created_at->diffForHumans() }}</td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('admin.astrologers.show', $pendingAstro) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-search me-1"></i> Review & Approve
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Recent Users Table -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold"><i class="bi bi-people text-primary me-2"></i> Recent Registered Customers</h6>
        <span class="badge bg-primary">{{ count($recentUsers) }} Shown</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th class="pe-4">Registered</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentUsers as $user)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $user->id }}</td>
                            <td class="fw-semibold">{{ $user->full_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? '—' }}</td>
                            <td>{{ $user->city ? $user->city . ', ' . $user->state : ($user->country ?? '—') }}</td>
                            <td>
                                @if($user->status === 'active')
                                    <span class="badge bg-success-subtle text-success">Active</span>
                                @elseif($user->status === 'blocked')
                                    <span class="badge bg-danger-subtle text-danger">Blocked</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="pe-4 text-muted small">{{ $user->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                No registered users found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ======================================================================= -->
<!-- Astrologers Performance & Activity Reports (Bookings & Orders)          -->
<!-- ======================================================================= -->
<div class="mt-5 mb-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark px-2 py-1"><i class="bi bi-bar-chart-fill me-1"></i> Reports</span>
                <h4 class="fw-bold mb-0 text-dark">Astrologers Booking & Orders Reports</h4>
            </div>
            <p class="text-muted small mb-0 mt-1">
                Comprehensive activity report showing consultation bookings taken and e-commerce store orders referred/handled by each astrologer
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-calendar2-check me-1"></i> View All Bookings
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-success">
                <i class="bi bi-bag-check me-1"></i> View All Orders
            </a>
        </div>
    </div>

    <!-- Report Metric Highlights -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-primary border-4">
                <span class="text-muted small text-uppercase fw-semibold">Bookings Taken</span>
                <h4 class="fw-bold my-1 text-primary">{{ number_format($astrologerBookingsSummary['total_bookings']) }}</h4>
                <small class="text-success fw-semibold">
                    <i class="bi bi-check-circle-fill me-1"></i>{{ number_format($astrologerBookingsSummary['completed_bookings']) }} Done
                </small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-purple border-4" style="border-left-color: #6f42c1 !important;">
                <span class="text-muted small text-uppercase fw-semibold">Store Orders Taken</span>
                <h4 class="fw-bold my-1 text-dark" style="color: #6f42c1 !important;">{{ number_format($astrologerOrdersSummary['total_astrologer_orders']) }}</h4>
                <small class="text-success fw-semibold">
                    <i class="bi bi-truck me-1"></i>{{ number_format($astrologerOrdersSummary['completed_astrologer_orders']) }} Delivered
                </small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-success border-4">
                <span class="text-muted small text-uppercase fw-semibold">Calls Attended</span>
                <h4 class="fw-bold my-1 text-success">{{ number_format($astrologerCallsSummary['attended_calls']) }}</h4>
                <small class="text-muted">
                    <i class="bi bi-telephone-inbound-fill text-success me-1"></i>Completed calls
                </small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-danger border-4">
                <span class="text-muted small text-uppercase fw-semibold">Calls Ignored</span>
                <h4 class="fw-bold my-1 text-danger">{{ number_format($astrologerCallsSummary['ignored_calls']) }}</h4>
                <small class="text-danger fw-semibold">
                    <i class="bi bi-telephone-x-fill me-1"></i>Rejected / Missed
                </small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-warning border-4">
                <span class="text-muted small text-uppercase fw-semibold">Bookings Revenue</span>
                <h4 class="fw-bold my-1 text-warning">₹{{ number_format($astrologerBookingsSummary['total_booking_revenue'], 0) }}</h4>
                <small class="text-muted">Gross fees</small>
            </div>
        </div>

        <div class="col-sm-6 col-xl-2">
            <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100 border-start border-info border-4" style="border-left-color: #0dcaf0 !important;">
                <span class="text-muted small text-uppercase fw-semibold">Store Orders Value</span>
                <h4 class="fw-bold my-1 text-dark">₹{{ number_format($astrologerOrdersSummary['total_orders_value'], 0) }}</h4>
                <small class="text-success fw-semibold">₹{{ number_format($astrologerOrdersSummary['total_commissions_generated'], 0) }} Comm.</small>
            </div>
        </div>
    </div>

    <!-- Main Astrologers Performance Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="mb-0 fw-bold"><i class="bi bi-table text-warning me-2"></i> Astrologer Activity Breakdown (Bookings, Orders & Calls)</h6>
                <small class="text-muted">Direct metrics per astrologer for consultation sessions, store orders taken, and call attendance</small>
            </div>
            <span class="badge bg-light text-dark border">{{ count($astrologersPerformance) }} Astrologers</span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th class="ps-4">Astrologer</th>
                            <th class="text-center">Bookings Taken</th>
                            <th class="text-center">Calls Attended</th>
                            <th class="text-center">Calls Ignored</th>
                            <th class="text-end">Bookings Revenue</th>
                            <th class="text-center">Orders Taken</th>
                            <th class="text-center">Orders Delivered</th>
                            <th class="text-end">Orders Value</th>
                            <th class="text-end">Commission</th>
                            <th class="text-end">Wallet Balance</th>
                            <th class="pe-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($astrologersPerformance as $perf)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-warning bg-opacity-25 text-dark fw-bold d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.85rem;">
                                            {{ substr($perf->display_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.astrologers.show', $perf) }}" class="fw-bold text-dark text-decoration-none">
                                                {{ $perf->display_name }}
                                            </a>
                                            <div class="small text-muted font-monospace" style="font-size: 0.75rem;">
                                                Astro ID: <strong class="text-dark">AST-{{ $perf->id }}</strong>
                                                @if($perf->referral_code)
                                                    &bull; Code: <span class="text-warning fw-semibold">{{ $perf->referral_code }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1">
                                        {{ $perf->total_bookings_count }}
                                    </span>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ $perf->completed_bookings_count }} done</div>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">
                                        <i class="bi bi-telephone-inbound-fill me-1"></i>{{ $perf->attended_calls_count }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge {{ $perf->ignored_calls_count > 0 ? 'bg-danger-subtle text-danger' : 'bg-light text-muted border' }} fw-bold px-2 py-1">
                                        <i class="bi bi-telephone-x-fill me-1"></i>{{ $perf->ignored_calls_count }}
                                    </span>
                                </td>

                                <td class="text-end fw-bold text-dark">
                                    ₹{{ number_format($perf->total_booking_revenue ?? 0, 2) }}
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-dark fw-bold px-2 py-1">
                                        {{ $perf->total_orders_count }}
                                    </span>
                                </td>

                                <td class="text-center">
                                    <span class="badge bg-success-subtle text-success fw-semibold">
                                        {{ $perf->completed_orders_count }}
                                    </span>
                                    @if($perf->pending_orders_count > 0)
                                        <span class="badge bg-secondary-subtle text-secondary fw-semibold ms-1">
                                            {{ $perf->pending_orders_count }} open
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end fw-bold text-dark">
                                    ₹{{ number_format($perf->total_orders_revenue, 2) }}
                                </td>

                                <td class="text-end fw-bold text-success">
                                    ₹{{ number_format($perf->total_commissions_earned, 2) }}
                                </td>

                                <td class="text-end font-monospace text-primary fw-semibold">
                                    ₹{{ number_format($perf->wallet?->available_balance ?? 0, 2) }}
                                </td>

                                <td class="pe-4 text-center">
                                    <a href="{{ route('admin.bookings.index', ['astrologer_id' => $perf->id]) }}" class="btn btn-sm btn-light border py-1 px-2" title="View Astrologer Bookings">
                                        <i class="bi bi-calendar-event text-primary"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.index', ['astrologer_id' => $perf->id]) }}" class="btn btn-sm btn-light border py-1 px-2 ms-1" title="View Astrologer Orders">
                                        <i class="bi bi-bag text-success"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4 text-muted">
                                    No astrologers found in database.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Activity Grids (Side by Side) -->
    <div class="row g-4">
        <!-- Recent Consultation Bookings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar2-event text-primary me-2"></i> Recent Consultation Bookings</h6>
                        <small class="text-muted">Latest appointments booked with astrologers</small>
                    </div>
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Booking #</th>
                                    <th>Astrologer</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th class="pe-3 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAstrologerBookings as $bk)
                                    <tr>
                                        <td class="ps-3 fw-semibold">
                                            <a href="{{ route('admin.bookings.show', $bk) }}" class="text-decoration-none">
                                                {{ $bk->booking_number ?? '#' . $bk->id }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $bk->booking_date?->format('M d, Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $bk->astrologer?->display_name ?? '—' }}</div>
                                            <small class="text-muted font-monospace">AST-{{ $bk->astrologer_id }}</small>
                                        </td>
                                        <td>{{ $bk->user?->full_name ?? 'Customer' }}</td>
                                        <td class="fw-bold">₹{{ number_format($bk->amount, 2) }}</td>
                                        <td class="pe-3 text-end">
                                            @if($bk->status === 'completed')
                                                <span class="badge bg-success-subtle text-success">Completed</span>
                                            @elseif($bk->status === 'confirmed')
                                                <span class="badge bg-primary-subtle text-primary">Confirmed</span>
                                            @elseif($bk->status === 'cancelled')
                                                <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No recent bookings found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Astrologer Store Orders -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="bi bi-bag-check text-success me-2"></i> Recent Astrologer Orders Taken</h6>
                        <small class="text-muted">Latest e-commerce sales attributed to astrologers</small>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-success">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Order #</th>
                                    <th>Referred By</th>
                                    <th>Customer</th>
                                    <th>Order Value</th>
                                    <th class="pe-3 text-end">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAstrologerOrders as $ord)
                                    <tr>
                                        <td class="ps-3 fw-semibold">
                                            <a href="{{ route('admin.orders.show', $ord) }}" class="text-decoration-none">
                                                {{ $ord->order_number }}
                                            </a>
                                            <div class="text-muted" style="font-size: 0.72rem;">{{ $ord->created_at->format('M d, Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $ord->referrerAstrologer?->display_name ?? 'Astrologer' }}</div>
                                            <small class="text-muted font-monospace">AST-{{ $ord->referrer_astrologer_id }}</small>
                                        </td>
                                        <td>{{ $ord->user?->full_name ?? ($ord->shipping_name ?? 'Customer') }}</td>
                                        <td class="fw-bold">₹{{ number_format($ord->total_amount, 2) }}</td>
                                        <td class="pe-3 text-end">
                                            @if($ord->status === 'delivered')
                                                <span class="badge bg-success-subtle text-success">Delivered</span>
                                            @elseif($ord->status === 'shipped')
                                                <span class="badge bg-info-subtle text-info">Shipped</span>
                                            @elseif($ord->status === 'cancelled')
                                                <span class="badge bg-danger-subtle text-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning">{{ ucfirst($ord->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            No astrologer-attributed orders found yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
