<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Astrologer Portal') — AstroReferral Partner</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --astro-dark: #12072B;
            --astro-purple: #2D124D;
            --astro-gold: #F5B041;
            --astro-gold-hover: #D4AC0D;
            --astro-bg: #F8FAFC;
            --astro-card: #FFFFFF;
            --astro-border: #E2E8F0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--astro-bg);
            color: #1E293B;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        .portal-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .portal-sidebar {
            width: 270px;
            background: linear-gradient(180deg, var(--astro-dark) 0%, #1E0836 100%);
            color: #E2E8F0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            border-right: 1px solid rgba(245, 176, 65, 0.15);
            z-index: 1040;
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .brand-logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--astro-gold) 0%, #E67E22 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            font-size: 1.35rem;
            box-shadow: 0 4px 15px rgba(245, 176, 65, 0.35);
        }

        .sidebar-menu {
            padding: 16px 12px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94A3B8;
            padding: 14px 12px 6px;
            font-weight: 700;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 14px;
            color: #CBD5E1;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-bottom: 3px;
        }

        .nav-link-custom:hover {
            color: #FFFFFF;
            background-color: rgba(255, 255, 255, 0.06);
        }

        .nav-link-custom.active {
            background-color: rgba(245, 176, 65, 0.15);
            color: var(--astro-gold);
            font-weight: 600;
            border-left: 3px solid var(--astro-gold);
        }

        .nav-link-custom i {
            font-size: 1.15rem;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .portal-main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .portal-topbar {
            background: #FFFFFF;
            border-bottom: 1px solid var(--astro-border);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .portal-content {
            padding: 28px;
            flex-grow: 1;
        }

        /* Metric cards */
        .metric-card {
            background: #FFFFFF;
            border-radius: 14px;
            padding: 22px;
            border: 1px solid var(--astro-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        /* Badges */
        .badge-pending { background-color: #FEF3C7; color: #92400E; }
        .badge-available { background-color: #D1FAE5; color: #065F46; }
        .badge-approved { background-color: #E0E7FF; color: #3730A3; }
        .badge-paid { background-color: #DCFCE7; color: #166534; }
        .badge-rejected { background-color: #FEE2E2; color: #991B1B; }
        .badge-cancelled { background-color: #F1F5F9; color: #475569; }

        .btn-gold {
            background: linear-gradient(135deg, var(--astro-gold) 0%, #E67E22 100%);
            color: #FFFFFF;
            font-weight: 600;
            border: none;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #E5A030 0%, #D35400 100%);
            color: #FFFFFF;
        }

        .referral-pill {
            background: rgba(245, 176, 65, 0.12);
            border: 1px dashed var(--astro-gold);
            padding: 6px 14px;
            border-radius: 30px;
            font-family: monospace;
            font-weight: 700;
            color: #9A6700;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="portal-wrapper">
        <!-- Sidebar -->
        <aside class="portal-sidebar d-none d-lg-flex">
            <div class="sidebar-brand">
                <div class="brand-logo-icon">
                    <i class="bi bi-moon-stars-fill"></i>
                </div>
                <div>
                    <h5 class="mb-0 text-white brand-font fw-bold">AstroReferral</h5>
                    <small class="text-white-50" style="font-size: 0.72rem;">Partner E-Commerce Portal</small>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="sidebar-header">Main Menu</div>
                <a href="{{ route('astrologer.dashboard') }}" class="nav-link-custom {{ request()->routeIs('astrologer.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>

                <div class="sidebar-header">Referral & Links</div>
                <a href="{{ route('astrologer.referrals.index') }}" class="nav-link-custom {{ request()->routeIs('astrologer.referrals.*') ? 'active' : '' }}">
                    <i class="bi bi-link-45deg"></i>
                    <span>Referral Links & Products</span>
                </a>
                <a href="{{ route('astrologer.analytics.index') }}" class="nav-link-custom {{ request()->routeIs('astrologer.analytics.*') ? 'active' : '' }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Traffic & Analytics</span>
                </a>

                <div class="sidebar-header">Finance & Earnings</div>
                <a href="{{ route('astrologer.commissions.index') }}" class="nav-link-custom {{ request()->routeIs('astrologer.commissions.*') ? 'active' : '' }}">
                    <i class="bi bi-percent"></i>
                    <span>Commissions History</span>
                </a>
                <a href="{{ route('astrologer.wallet.index') }}" class="nav-link-custom {{ request()->routeIs('astrologer.wallet.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>My Wallet & Ledger</span>
                </a>
                <a href="{{ route('astrologer.withdrawals.index') }}" class="nav-link-custom {{ request()->routeIs('astrologer.withdrawals.*') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>Withdrawal Requests</span>
                </a>

                <div class="sidebar-header">Account Settings</div>
                <a href="{{ route('astrologer.profile.index') }}" class="nav-link-custom {{ request()->routeIs('astrologer.profile.*') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span>Partner Profile</span>
                </a>
                <a href="{{ route('shop.index') }}" target="_blank" class="nav-link-custom">
                    <i class="bi bi-shop"></i>
                    <span>Store Catalog <i class="bi bi-box-arrow-up-right ms-auto" style="font-size:0.75rem;"></i></span>
                </a>

                <div class="mt-4 pt-3 border-top border-white border-opacity-10">
                    <form action="{{ route('astrologer.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2" style="font-size: 0.88rem;">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Body -->
        <div class="portal-main">
            <!-- Topbar -->
            <header class="portal-topbar">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h5 class="mb-0 fw-bold">@yield('page_title', 'Partner Dashboard')</h5>
                        <small class="text-muted">Welcome, {{ Auth::user()->astrologer->display_name ?? Auth::user()->full_name }}</small>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    @if(Auth::user()->astrologer?->referral_code)
                        <div class="d-none d-md-flex align-items-center gap-2">
                            <span class="small text-muted">My Code:</span>
                            <span class="referral-pill" id="topbarReferralCode">{{ Auth::user()->astrologer->referral_code }}</span>
                            <button type="button" class="btn btn-sm btn-light border" onclick="navigator.clipboard.writeText('{{ url('/ref/' . Auth::user()->astrologer->referral_code) }}'); alert('Referral link copied to clipboard!');">
                                <i class="bi bi-clipboard"></i> Copy Link
                            </button>
                        </div>
                    @endif

                    <div class="dropdown">
                        <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2 py-1 px-3" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 text-secondary"></i>
                            <span class="fw-semibold small">{{ Auth::user()->astrologer->display_name ?? Auth::user()->first_name }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><a class="dropdown-item" href="{{ route('astrologer.profile.index') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="{{ route('astrologer.wallet.index') }}"><i class="bi bi-wallet2 me-2"></i> Wallet</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('astrologer.logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Alerts -->
            <div class="portal-content">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                        <div>{{ session('success') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <div>{{ session('error') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                        <i class="bi bi-info-circle-fill fs-5"></i>
                        <div>{{ session('info') }}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
