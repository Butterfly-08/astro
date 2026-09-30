<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AstroVani — Premier Astrology Consultation & Spiritual E-Commerce')</title>
    <meta name="description" content="@yield('meta_description', 'Connect with verified Vedic astrologers, tarot readers, numerologists, and shop genuine spiritual products on AstroVani.')">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --astro-purple-dark: #1A0B2E;
            --astro-purple: #2D124D;
            --astro-purple-light: #4A1E80;
            --astro-gold: #F5B041;
            --astro-gold-hover: #D4AC0D;
            --astro-gold-soft: rgba(245, 176, 65, 0.15);
            --astro-bg-cream: #FDFBF7;
            --astro-text-dark: #1F2937;
            --astro-text-muted: #6B7280;
            --astro-border: #E5E7EB;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--astro-bg-cream);
            color: var(--astro-text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Top Bar */
        .astro-topbar {
            background-color: var(--astro-purple-dark);
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.85rem;
            padding: 6px 0;
            border-bottom: 1px solid rgba(245, 176, 65, 0.2);
        }

        /* Main Navbar */
        .astro-navbar {
            background-color: #FFFFFF;
            box-shadow: 0 2px 15px rgba(26, 11, 46, 0.06);
            padding: 14px 0;
        }

        .astro-logo {
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--astro-purple);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .astro-logo .logo-star {
            color: var(--astro-gold);
            font-size: 1.5rem;
        }

        .astro-logo span.tagline {
            font-size: 0.72rem;
            font-weight: 500;
            color: var(--astro-gold-hover);
            letter-spacing: 1px;
            text-transform: uppercase;
            display: block;
            margin-top: -4px;
        }

        .nav-link {
            font-weight: 500;
            color: #374151;
            padding: 8px 14px !important;
            transition: all 0.2s ease;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--astro-purple) !important;
        }

        /* Gold Button */
        .btn-astro-gold {
            background-color: var(--astro-gold);
            color: var(--astro-purple-dark);
            font-weight: 600;
            border-radius: 8px;
            border: none;
            padding: 8px 20px;
            transition: all 0.25s ease;
        }

        .btn-astro-gold:hover {
            background-color: var(--astro-gold-hover);
            color: #FFFFFF;
            transform: translateY(-1px);
        }

        /* Outline Purple Button */
        .btn-astro-outline {
            border: 1.5px solid var(--astro-purple);
            color: var(--astro-purple);
            font-weight: 600;
            border-radius: 8px;
            padding: 7px 18px;
            transition: all 0.25s ease;
        }

        .btn-astro-outline:hover {
            background-color: var(--astro-purple);
            color: #FFFFFF;
        }

        /* Footer */
        .astro-footer {
            background-color: var(--astro-purple-dark);
            color: #D1D5DB;
            margin-top: auto;
            padding: 50px 0 25px;
            border-top: 3px solid var(--astro-gold);
        }

        .astro-footer a {
            color: #9CA3AF;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .astro-footer a:hover {
            color: var(--astro-gold);
        }

        .footer-heading {
            color: #FFFFFF;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 8px;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 35px;
            height: 2px;
            background-color: var(--astro-gold);
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Top Bar -->
    <div class="astro-topbar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">
            <div>
                <span class="me-3"><i class="bi bi-stars text-warning me-1"></i> 100% Genuine Consultation & Certified Remedies</span>
                <span><i class="bi bi-shield-check text-success me-1"></i> Verified Astrologers</span>
            </div>
            <div>
                <a href="{{ route('admin.login') }}" class="text-white-50 text-decoration-none me-3" style="font-size: 0.8rem;">
                    <i class="bi bi-shield-lock me-1"></i> Admin Portal
                </a>
                <span class="text-white-50">Support: support@astrovani.test</span>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg astro-navbar sticky-top">
        <div class="container">
            <a class="astro-logo" href="{{ route('home') }}">
                <div>
                    <i class="bi bi-sun-fill logo-star"></i>
                    <span>AstroVani</span>
                    <span class="tagline">Guidance & Spiritual Shop</span>
                </div>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active text-primary fw-bold' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('astrologers.*') ? 'active text-primary fw-bold' : '' }}" href="{{ route('astrologers.index') }}">Astrologers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services.*') ? 'active text-primary fw-bold' : '' }}" href="{{ route('services.index') }}">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.*') ? 'active text-primary fw-bold' : '' }}" href="{{ route('shop.index') }}">
                            <i class="bi bi-bag-heart me-1 text-warning"></i>Spiritual Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#horoscope">Horoscope</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#blogs">Blogs</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-2">
                    @auth('web')
                        <div class="dropdown">
                            <button class="btn btn-astro-outline dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle"></i>
                                <span>{{ Auth::guard('web')->user()->first_name }}</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userMenuButton">
                                <li>
                                    <h6 class="dropdown-header">Logged in as customer</h6>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2 text-primary"></i> My Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.bookings.index') }}">
                                        <i class="bi bi-calendar-event me-2 text-warning"></i> My Consultations
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-astro-outline me-1">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-astro-gold">
                            <i class="bi bi-person-plus me-1"></i> Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Alerts Container -->
    <div class="container mt-3">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="astro-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="astro-logo mb-3 text-white">
                        <i class="bi bi-sun-fill logo-star"></i>
                        <span class="text-white">AstroVani</span>
                    </div>
                    <p class="text-white-50 small mb-3">
                        AstroVani is an authentic digital sanctuary bringing ancient Vedic wisdom into modern life. Connect with experienced, verified astrologers and discover genuine, sacred remedies for personal growth and harmony.
                    </p>
                    <div class="d-flex gap-3 text-white-50">
                        <a href="#"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#"><i class="bi bi-youtube fs-5"></i></a>
                        <a href="#"><i class="bi bi-whatsapp fs-5"></i></a>
                    </div>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">Quick Links</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('home') }}">Home</a></li>
                        <li class="mb-2"><a href="{{ route('astrologers.index') }}">Verified Astrologers</a></li>
                        <li class="mb-2"><a href="{{ route('services.index') }}">Consultation Services</a></li>
                        <li class="mb-2"><a href="{{ route('shop.index') }}">Spiritual Shop</a></li>
                        <li class="mb-2"><a href="#horoscope">Daily Horoscopes</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-heading">Customer Care</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="{{ route('user.dashboard') }}">My Account</a></li>
                        <li class="mb-2"><a href="{{ route('login') }}">Customer Login</a></li>
                        <li class="mb-2"><a href="{{ route('register') }}">Create Account</a></li>
                        <li class="mb-2"><a href="{{ route('admin.login') }}">Admin Login</a></li>
                    </ul>
                </div>

                <div class="col-lg-4 col-md-6">
                    <h5 class="footer-heading">Astrology Disclaimer</h5>
                    <p class="text-white-50" style="font-size: 0.78rem; line-height: 1.5;">
                        Astrology readings and spiritual insights provided on AstroVani are based on Vedic and symbolic traditions. They are meant for guidance and self-discovery and do not constitute professional medical, legal, or financial advice. We do not make supernatural guarantees.
                    </p>
                    <div class="small text-warning">
                        <i class="bi bi-patch-check-fill me-1"></i> Dedicated 24/7 Spiritual Platform Support
                    </div>
                </div>
            </div>

            <hr class="border-secondary mt-4 mb-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-white-50">
                <div>&copy; {{ date('Y') }} AstroVani Platform. All rights reserved.</div>
                <div class="mt-2 mt-md-0">
                    <span class="me-3">Crafted with Laravel 13 & Bootstrap 5</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
