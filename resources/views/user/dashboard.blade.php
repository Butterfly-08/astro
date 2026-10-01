@extends('layouts.app')

@section('title', 'My Dashboard — AstroVani')

@section('content')
<div class="container py-4 my-3">
    <!-- Welcome Header Banner -->
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white shadow-sm" style="background: linear-gradient(135deg, #1A0B2E 0%, #2D124D 60%, #481B7F 100%); border-bottom: 3px solid #F5B041;">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark mb-2 px-3 py-1 fw-bold"><i class="bi bi-stars"></i> Customer Portal</span>
                <h2 class="display-6 fw-bold mb-1">Namaste, {{ $user->first_name }} {{ $user->last_name }}!</h2>
                <p class="lead text-white-50 mb-0" style="font-size: 1.05rem;">
                    Welcome to your personal AstroVani spiritual sanctuary. Manage your upcoming consultations, track purchases, and stay aligned with the cosmos.
                </p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="{{ route('astrologers.index') }}" class="btn btn-astro-gold me-2">
                    <i class="bi bi-calendar-plus me-1"></i> Book Consultation
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Customer Dashboard Sidebar -->
        <div class="col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="p-4 text-center bg-light border-bottom">
                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 70px; height: 70px; font-size: 2rem;">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $user->full_name }}</h6>
                    <small class="text-muted">{{ $user->email }}</small>
                    <div class="mt-2">
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active Customer</span>
                    </div>
                </div>

                <div class="list-group list-group-flush small">
                    <a href="{{ route('user.dashboard') }}" class="list-group-item list-group-item-action active fw-semibold d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-speedometer2 text-warning fs-5"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="#profile" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary">
                        <i class="bi bi-person-vcard fs-5 text-muted"></i>
                        <span>My Profile</span>
                    </a>
                    <a href="{{ route('user.bookings.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 text-secondary">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-calendar2-check fs-5 text-muted"></i>
                            <span>My Bookings</span>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $totalBookings }}</span>
                    </a>
                    <a href="{{ route('user.orders.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 text-secondary">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-bag-check fs-5 text-muted"></i>
                            <span>My Orders</span>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $totalOrders }}</span>
                    </a>
                    <a href="{{ route('user.wishlist.index') }}" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 text-secondary">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-heart fs-5 text-muted"></i>
                            <span>Wishlist</span>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $wishlistCount }}</span>
                    </a>
                    <a href="#reviews" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary">
                        <i class="bi bi-star fs-5 text-muted"></i>
                        <span>My Reviews</span>
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 text-secondary">
                        <i class="bi bi-bell fs-5 text-muted"></i>
                        <span>Notifications</span>
                    </a>
                    <div class="p-3">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main User Dashboard Metrics -->
        <div class="col-lg-9">
            <div class="row g-3 mb-4">
                <!-- Total Orders -->
                <div class="col-sm-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Orders</span>
                                <h3 class="fw-bold my-1 text-dark">{{ $totalOrders }}</h3>
                                <small class="text-muted">Total Placed</small>
                            </div>
                            <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3">
                                <i class="bi bi-bag-check fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Bookings -->
                <div class="col-sm-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Consultations</span>
                                <h3 class="fw-bold my-1 text-dark">{{ $totalBookings }}</h3>
                                <small class="text-muted">All Time</small>
                            </div>
                            <div class="rounded-3 bg-purple bg-opacity-10 text-purple p-3" style="background-color: rgba(45, 18, 77, 0.1); color: #2D124D;">
                                <i class="bi bi-chat-heart fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <div class="col-sm-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Upcoming</span>
                                <h3 class="fw-bold my-1 text-warning">{{ $upcomingBookings }}</h3>
                                <small class="text-muted">Active Sessions</small>
                            </div>
                            <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3">
                                <i class="bi bi-alarm fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Items -->
                <div class="col-sm-6 col-md-3">
                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white h-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted small text-uppercase fw-semibold">Wishlist</span>
                                <h3 class="fw-bold my-1 text-danger">{{ $wishlistCount }}</h3>
                                <small class="text-muted">Saved Items</small>
                            </div>
                            <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3">
                                <i class="bi bi-heart-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Consultation Bookings -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-check text-primary me-2"></i> Recent Consultation Appointments</h6>
                    <a href="{{ route('user.bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @if($recentBookings->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2 opacity-50"></i>
                            <div class="small mb-2">You have no consultation bookings yet.</div>
                            <a href="{{ route('astrologers.index') }}" class="btn btn-sm btn-astro-gold">Consult an Astrologer</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Astrologer</th>
                                        <th>Date & Time</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th class="pe-3 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $rb)
                                        <tr>
                                            <td class="ps-3 fw-semibold text-dark">{{ $rb->astrologer->display_name }}</td>
                                            <td>{{ $rb->booking_date->format('d M Y') }} &bull; {{ $rb->formatted_time_slot }}</td>
                                            <td class="text-capitalize">{{ $rb->consultation_type }}</td>
                                            <td>{!! $rb->status_badge !!}</td>
                                            <td class="pe-3 text-end">
                                                <a href="{{ route('user.bookings.show', $rb) }}" class="btn btn-sm btn-light">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Profile Summary & Phase 1 Confirmation -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-lines-fill text-primary me-2"></i> Account Details & Astrology Preferences</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-4">
                            <span class="text-muted small d-block">Full Name</span>
                            <span class="fw-semibold">{{ $user->full_name }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span class="text-muted small d-block">Email Address</span>
                            <span class="fw-semibold">{{ $user->email }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span class="text-muted small d-block">Phone Number</span>
                            <span class="fw-semibold">{{ $user->phone ?? 'Not provided' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span class="text-muted small d-block">Date of Birth</span>
                            <span class="fw-semibold">{{ $user->date_of_birth ? $user->date_of_birth->format('d M, Y') : 'Not provided' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span class="text-muted small d-block">Gender</span>
                            <span class="fw-semibold text-capitalize">{{ $user->gender ?? 'Not specified' }}</span>
                        </div>
                        <div class="col-sm-6 col-md-4">
                            <span class="text-muted small d-block">Location</span>
                            <span class="fw-semibold">{{ $user->city ? $user->city . ', ' . $user->state : ($user->country ?? 'India') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Cards -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-4 rounded-3 border bg-white shadow-sm h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-25 text-dark p-3">
                                <i class="bi bi-headset fs-4 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Need Astrology Consultation?</h6>
                                <p class="small text-muted mb-0">Speak with experienced Vedic astrologers, tarot readers & numerologists.</p>
                            </div>
                        </div>
                        <a href="{{ route('astrologers.index') }}" class="btn btn-sm btn-astro-gold">Browse Astrologers</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 rounded-3 border bg-white shadow-sm h-100">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                                <i class="bi bi-gem fs-4"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Certified Spiritual Remedies</h6>
                                <p class="small text-muted mb-0">Shop certified gemstones, authentic rudraksha beads, and sacred yantras.</p>
                            </div>
                        </div>
                        <a href="#shop" class="btn btn-sm btn-astro-outline">Visit Shop</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
