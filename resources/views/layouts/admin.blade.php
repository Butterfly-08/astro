<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') — AstroVani Control Panel</title>
    <link rel="stylesheet" href="{{ asset('css/page-loader.css') }}">
    <script src="{{ asset('js/page-loader.js') }}"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --admin-sidebar-bg: #1A0B2E;
            --admin-sidebar-hover: #261142;
            --admin-sidebar-active: #381861;
            --admin-gold: #F5B041;
            --admin-gold-hover: #D4AC0D;
            --admin-body-bg: #F4F6F9;
            --admin-card-bg: #FFFFFF;
            --admin-text-main: #1F2937;
            --admin-text-muted: #6B7280;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--admin-body-bg);
            color: var(--admin-text-main);
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Layout Container */
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .admin-sidebar {
            width: 270px;
            background: linear-gradient(180deg, var(--admin-sidebar-bg) 0%, #120720 100%);
            color: #E2E8F0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            transition: all 0.3s ease;
            z-index: 1040;
            border-right: 1px solid rgba(245, 176, 65, 0.15);
        }

        .admin-brand {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
            color: #FFFFFF;
        }

        .admin-brand .brand-logo-icon {
            color: var(--admin-gold);
            font-size: 1.6rem;
        }

        .admin-brand .brand-title {
            font-size: 1.35rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .admin-brand .brand-subtitle {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: var(--admin-gold);
            display: block;
        }

        .sidebar-menu {
            padding: 16px 12px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #94A3B8;
            padding: 12px 14px 6px;
            font-weight: 600;
        }

        .nav-item-custom {
            margin-bottom: 3px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            color: #CBD5E1;
            text-decoration: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            color: #FFFFFF;
            background-color: var(--admin-sidebar-hover);
        }

        .nav-link-custom.active {
            background-color: var(--admin-sidebar-active);
            color: var(--admin-gold);
            font-weight: 600;
            border-left: 3px solid var(--admin-gold);
        }

        .nav-link-custom i {
            font-size: 1.15rem;
            width: 22px;
            text-align: center;
        }

        /* Top Navbar */
        .admin-topbar {
            background-color: #FFFFFF;
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.04);
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        /* Main Content */
        .admin-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .admin-content {
            padding: 28px;
            flex-grow: 1;
        }

        /* Cards */
        .stat-card {
            background: #FFFFFF;
            border-radius: 12px;
            padding: 22px;
            border: 1px solid #E5E7EB;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .icon-purple {
            background-color: rgba(45, 18, 77, 0.1);
            color: #2D124D;
        }

        .icon-gold {
            background-color: rgba(245, 176, 65, 0.15);
            color: #D4AC0D;
        }

        .icon-blue {
            background-color: rgba(59, 130, 246, 0.1);
            color: #2563EB;
        }

        .icon-green {
            background-color: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        /* Responsive Sidebar */
        @media (max-width: 991.98px) {
            .admin-sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: -270px;
            }

            .admin-sidebar.show {
                left: 0;
            }

            .sidebar-backdrop {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1035;
            }

            .sidebar-backdrop.show {
                display: block;
            }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/astro-live-wallpaper.css') }}">
    @stack('styles')
</head>
<body>
    <x-page-loader />

    <!-- Live Background Wallpaper Video & Constellation FX -->
    <x-live-wallpaper />

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="admin-wrapper position-relative" style="z-index: 10;">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar" id="adminSidebar">
            <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                <i class="bi bi-shield-fill-check brand-logo-icon"></i>
                <div>
                    <span class="brand-title">AstroVani</span>
                    <span class="brand-subtitle">Master Administration</span>
                </div>
            </a>

            <div class="sidebar-menu">
                <div class="sidebar-header">Core Management</div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link-custom {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="sidebar-header">Platform Users</div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.users.index') }}" class="nav-link-custom {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i>
                        <span>Users</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    @php $pendingCount = \App\Models\Astrologer::where('status','pending')->count(); @endphp
                    <a href="{{ route('admin.astrologers.index') }}" class="nav-link-custom {{ request()->routeIs('admin.astrologers.*') && request('status') !== 'pending' ? 'active' : '' }}">
                        <i class="bi bi-star-fill text-warning"></i>
                        <span>Astrologers</span>
                        @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark ms-auto" style="font-size:0.65rem;">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </div>
                @if($pendingCount > 0)
                <div class="nav-item-custom" style="padding-left: 12px;">
                    <a href="{{ route('admin.astrologers.index', ['status' => 'pending']) }}" class="nav-link-custom py-1 {{ request('status') === 'pending' ? 'active' : '' }}" style="font-size: 0.82rem;">
                        <i class="bi bi-clock-history text-warning"></i>
                        <span>Pending Approval</span>
                        <span class="badge bg-danger ms-auto" style="font-size:0.62rem;">{{ $pendingCount }}</span>
                    </a>
                </div>
                @endif
                <div class="nav-item-custom">
                    <a href="{{ route('admin.services.index') }}" class="nav-link-custom {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                        <i class="bi bi-gem"></i>
                        <span>Services</span>
                    </a>
                </div>

                <div class="sidebar-header">Commerce & Bookings</div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.categories.index') }}" class="nav-link-custom {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags-fill"></i>
                        <span>Product Categories</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.products.index') }}" class="nav-link-custom {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam-fill"></i>
                        <span>Products & Stock</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    @php $pendingOrdersCount = \Illuminate\Support\Facades\Schema::hasTable('orders') ? \App\Models\Order::where('status', 'pending')->count() : 0; @endphp
                    <a href="{{ route('admin.orders.index') }}" class="nav-link-custom {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                        <i class="bi bi-bag-check-fill"></i>
                        <span>Orders</span>
                        @if($pendingOrdersCount > 0)
                            <span class="badge bg-danger ms-auto" style="font-size:0.65rem;">{{ $pendingOrdersCount }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-item-custom">
                    @php $pendingBookingsCount = \Illuminate\Support\Facades\Schema::hasTable('bookings') ? \App\Models\Booking::where('status', 'pending')->count() : 0; @endphp
                    <a href="{{ route('admin.bookings.index') }}" class="nav-link-custom {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar2-check-fill"></i>
                        <span>Bookings</span>
                        @if($pendingBookingsCount > 0)
                            <span class="badge bg-warning text-dark ms-auto" style="font-size:0.65rem;">{{ $pendingBookingsCount }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="#reviews" class="nav-link-custom">
                        <i class="bi bi-chat-square-quote-fill"></i>
                        <span>Reviews</span>
                    </a>
                </div>

                <div class="sidebar-header">Referral & Commissions</div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.commissions.index') }}" class="nav-link-custom {{ request()->routeIs('admin.commissions.*') ? 'active' : '' }}">
                        <i class="bi bi-percent"></i>
                        <span>Commissions</span>
                        @php $pendingCommCount = \Illuminate\Support\Facades\Schema::hasTable('commissions') ? \App\Models\Commission::where('status', 'pending')->count() : 0; @endphp
                        @if($pendingCommCount > 0)
                            <span class="badge bg-warning text-dark ms-auto" style="font-size:0.65rem;">{{ $pendingCommCount }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.withdrawals.index') }}" class="nav-link-custom {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i>
                        <span>Withdrawals</span>
                        @php $pendingWithCount = \Illuminate\Support\Facades\Schema::hasTable('withdrawals') ? \App\Models\Withdrawal::whereIn('status', ['pending', 'under_review', 'approved'])->count() : 0; @endphp
                        @if($pendingWithCount > 0)
                            <span class="badge bg-danger ms-auto" style="font-size:0.65rem;">{{ $pendingWithCount }}</span>
                        @endif
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.wallets.index') }}" class="nav-link-custom {{ request()->routeIs('admin.wallets.*') ? 'active' : '' }}">
                        <i class="bi bi-wallet2"></i>
                        <span>Wallets & Ledger</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.referrals.partners') }}" class="nav-link-custom {{ request()->routeIs('admin.referrals.partners') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i>
                        <span>Referral Partners</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.referrals.index') }}" class="nav-link-custom {{ request()->routeIs('admin.referrals.index') ? 'active' : '' }}">
                        <i class="bi bi-cursor-fill"></i>
                        <span>Referral Clicks</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link-custom {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="bi bi-sliders"></i>
                        <span>Referral Settings</span>
                    </a>
                </div>

                <div class="sidebar-header">Content & Settings</div>
                <div class="nav-item-custom">
                    <a href="#blogs" class="nav-link-custom">
                        <i class="bi bi-journal-richtext"></i>
                        <span>Blogs & Articles</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="#horoscopes" class="nav-link-custom">
                        <i class="bi bi-compass-fill"></i>
                        <span>Horoscopes</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="#reports" class="nav-link-custom">
                        <i class="bi bi-bar-chart-fill"></i>
                        <span>Reports</span>
                    </a>
                </div>
                <div class="nav-item-custom">
                    <a href="#settings" class="nav-link-custom">
                        <i class="bi bi-gear-fill"></i>
                        <span>Platform Settings</span>
                    </a>
                </div>

                <div class="mt-4 pt-3 border-top border-secondary border-opacity-25">
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2" style="font-size: 0.88rem;">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Admin Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-main">
            <!-- Topbar -->
            <header class="admin-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle" type="button">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h5 class="mb-0 fw-bold">@yield('page_title', 'Dashboard Overview')</h5>
                        <small class="text-muted d-none d-sm-inline">AstroVani Enterprise Administration Portal</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-outline-secondary d-none d-md-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-up-right"></i> View Website
                    </a>

                    <!-- Admin Profile Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge bg-warning text-dark"><i class="bi bi-shield-check"></i> SuperAdmin</span>
                            <span class="fw-semibold small">{{ Auth::guard('admin')->user()->name ?? 'Administrator' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li class="dropdown-header">
                                <div class="fw-bold">{{ Auth::guard('admin')->user()->email ?? 'admin@astrovani.test' }}</div>
                                <small class="text-muted">Administrator Guard</small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('admin.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Log Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Alerts Container -->
            <div class="container-fluid px-4 pt-3">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <div class="admin-content">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-muted small text-center text-md-start d-flex justify-content-between">
                <div>&copy; {{ date('Y') }} AstroVani Platform Administration.</div>
                <div>Server Environment: <span class="badge bg-light text-dark border">Laravel 13 • PHP {{ PHP_VERSION }}</span></div>
            </footer>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Responsive sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const adminSidebar = document.getElementById('adminSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        if (sidebarToggle && adminSidebar && sidebarBackdrop) {
            sidebarToggle.addEventListener('click', () => {
                adminSidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            });

            sidebarBackdrop.addEventListener('click', () => {
                adminSidebar.classList.remove('show');
                sidebarBackdrop.classList.remove('show');
            });
        }
    </script>
    <script src="{{ asset('js/astro-live-wallpaper.js') }}"></script>
    @stack('scripts')
</body>
</html>
