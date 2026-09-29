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
                <small class="text-muted"><i class="bi bi-stars me-1 text-warning"></i> Certified Experts</small>
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
                <small class="text-muted"><i class="bi bi-gem me-1"></i> Active Offerings</small>
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

<!-- Architecture & Phase Status Card -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold"><i class="bi bi-shield-check text-primary me-2"></i> Phase 1 System Health & Authentication Status</h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Operational</span>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-shield-lock-fill text-warning fs-5"></i>
                                <span class="fw-bold">Admin Authentication Guard</span>
                            </div>
                            <p class="small text-muted mb-2">
                                Dedicated guard <code>admin</code> powered by <code>admins</code> table and <code>AdminMiddleware</code>.
                            </p>
                            <span class="badge bg-success"><i class="bi bi-check2"></i> Strictly Isolated</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border bg-light">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-person-fill-check text-primary fs-5"></i>
                                <span class="fw-bold">Customer Authentication Guard</span>
                            </div>
                            <p class="small text-muted mb-2">
                                Dedicated guard <code>web</code> powered by <code>users</code> table and standard web authentication.
                            </p>
                            <span class="badge bg-success"><i class="bi bi-check2"></i> Strictly Isolated</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-3 border border-warning bg-warning bg-opacity-10">
                    <div class="d-flex gap-2">
                        <i class="bi bi-info-circle-fill text-warning fs-5"></i>
                        <div>
                            <div class="fw-bold text-dark">Phase 1 Complete</div>
                            <small class="text-muted">
                                Dual-authentication, distinct databases, middleware protection, and responsive Bootstrap 5 styling are fully implemented and verified. Subsequent phases will integrate Astrologer CRUD, Appointment Booking with Double-booking Prevention, E-Commerce Shop, Cart, and REST APIs.
                            </small>
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
@endsection
