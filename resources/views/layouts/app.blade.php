<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AstroVani — Premier Astrology Consultation & Spiritual E-Commerce')</title>
    <meta name="description" content="@yield('meta_description', 'Connect with verified Vedic astrologers, tarot readers, numerologists, and shop genuine spiritual products on AstroVani.')">
    <link rel="stylesheet" href="{{ asset('css/page-loader.css') }}">
    <script src="{{ asset('js/page-loader.js') }}"></script>

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

        .nav-link:hover,
        .nav-link.active {
            color: var(--astro-purple) !important;
        }

        .website-language-select {
            min-width: 132px;
            border-color: var(--astro-border);
            color: var(--astro-purple);
            font-size: 0.82rem;
            padding-top: 7px;
            padding-bottom: 7px;
        }

                                #google_translate_element {
            display: none;
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

        /* Cart Button */
        .btn-cart {
            position: relative;
            border: 1.5px solid var(--astro-purple);
            color: var(--astro-purple);
            font-weight: 600;
            border-radius: 8px;
            padding: 7px 15px;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn-cart:hover {
            background-color: var(--astro-purple);
            color: #FFFFFF;
        }

        .cart-badge {
            font-size: 0.68rem;
            min-width: 19px;
            height: 19px;
            padding: 2px 5px;
            border-radius: 50%;
            background-color: var(--astro-gold);
            color: var(--astro-purple-dark);
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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

    <link rel="stylesheet" href="{{ asset('css/astro-live-wallpaper.css') }}">
    <link rel="stylesheet" href="{{ asset('css/astro-dock.css') }}">
    @stack('styles')
</head>

<body>
    <x-page-loader />

    <!-- Live Background Wallpaper Video & Celestial Constellation FX -->
    <x-live-wallpaper />

    <!-- Top Bar -->
    <div class="astro-topbar d-none d-md-block">
        <div class="container d-flex justify-content-between align-items-center">

            <div>
                <span class="me-3">
                    <i class="bi bi-stars text-warning me-1"></i>
                    100% Genuine Consultation & Certified Remedies
                </span>

                <span>
                    <i class="bi bi-shield-check text-success me-1"></i>
                    Verified Astrologers
                </span>
            </div>

            <div>
                <a href="{{ route('admin.login') }}"
                   class="text-white-50 text-decoration-none me-3"
                   style="font-size: 0.8rem;">
                    <i class="bi bi-shield-lock me-1"></i>
                    Admin Portal
                </a>

                <span class="text-white-50">
                    Support: <span class="notranslate" translate="no">support@astrovani.test</span>
                </span>
            </div>

        </div>
    </div>


    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg astro-navbar sticky-top">

        <div class="container">

            <!-- Logo -->
            <a class="astro-logo" href="{{ route('home') }}">
                <div>
                    <i class="bi bi-sun-fill logo-star"></i>
                    <span class="notranslate" translate="no">AstroVani</span>
                    <span class="tagline">Guidance & Spiritual Shop</span>
                </div>
            </a>


            <!-- Mobile Toggle -->
            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarContent"
                    aria-controls="navbarContent"
                    aria-expanded="false"
                    aria-label="Toggle navigation">

                <span class="navbar-toggler-icon"></span>

            </button>


            <div class="collapse navbar-collapse" id="navbarContent">

                <!-- Menu -->
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active text-primary fw-bold' : '' }}"
                           href="{{ route('home') }}">
                            Home
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('astrologers.*') ? 'active text-primary fw-bold' : '' }}"
                           href="{{ route('astrologers.index') }}">
                            Astrologers
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('services.*') ? 'active text-primary fw-bold' : '' }}"
                           href="{{ route('services.index') }}">
                            Services
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.*') ? 'active text-primary fw-bold' : '' }}"
                           href="{{ route('shop.index') }}">

                            <i class="bi bi-bag-heart me-1 text-warning"></i>
                            Spiritual Shop

                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#horoscope">
                            Horoscope
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#blogs">
                            Blogs
                        </a>
                    </li>

                </ul>


                <!-- Right Side -->
                <div class="d-flex align-items-center gap-2">
                    <label class="visually-hidden" for="website-language">Website language</label>
                    <select id="website-language" class="form-select website-language-select" aria-label="Website language" onchange="changeWebsiteLanguage(this.value)">
                        <option value="en">English</option>
                        <option value="hi">Hindi</option>
                        <option value="ta">Tamil</option>
                        <option value="te">Telugu</option>
                        <option value="bn">Bengali</option>
                        <option value="mr">Marathi</option>
                        <option value="gu">Gujarati</option>
                        <option value="pa">Punjabi</option>
                    </select>
                    <div id="google_translate_element" aria-hidden="true"></div>

                    <!-- CART BUTTON -->
                    <a href="{{ route('cart.index') }}" class="btn-cart">

                        <i class="bi bi-cart3"></i>

                        <span>Cart</span>

                        <span class="cart-badge">
                            {{ app(\App\Services\CartService::class)->count() }}
                        </span>

                    </a>


                    @auth('web')

                        <div class="dropdown">

                            <button class="btn btn-astro-outline dropdown-toggle d-flex align-items-center gap-2"
                                    type="button"
                                    id="userMenuButton"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">

                                <i class="bi bi-person-circle"></i>

                                <span>
                                    {{ Auth::guard('web')->user()->first_name }}
                                </span>

                            </button>


                            <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                                aria-labelledby="userMenuButton">

                                <li>
                                    <h6 class="dropdown-header">
                                        Logged in as customer
                                    </h6>
                                </li>

                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('user.dashboard') }}">

                                        <i class="bi bi-speedometer2 me-2 text-primary"></i>
                                        My Dashboard

                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item"
                                       href="{{ route('user.bookings.index') }}">

                                        <i class="bi bi-calendar-event me-2 text-warning"></i>
                                        My Consultations

                                    </a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>

                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf

                                        <button type="submit"
                                                class="dropdown-item text-danger">

                                            <i class="bi bi-box-arrow-right me-2"></i>
                                            Logout

                                        </button>

                                    </form>

                                </li>

                            </ul>

                        </div>

                    @else

                        <a href="{{ route('login') }}"
                           class="btn btn-astro-outline me-1">

                            <i class="bi bi-box-arrow-in-right me-1"></i>
                            Login

                        </a>

                        <a href="{{ route('register') }}"
                           class="btn btn-astro-gold">

                            <i class="bi bi-person-plus me-1"></i>
                            Register

                        </a>

                    @endauth

                </div>

            </div>

        </div>

    </nav>


    <!-- Alerts -->
    <div class="container mt-3">

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center"
                 role="alert">

                <i class="bi bi-check-circle-fill me-2 fs-5"></i>

                <div>{{ session('success') }}</div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

            </div>

        @endif


        @if(session('error'))

            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"
                 role="alert">

                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>

                <div>{{ session('error') }}</div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

            </div>

        @endif


        @if(session('info'))

            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center"
                 role="alert">

                <i class="bi bi-info-circle-fill me-2 fs-5"></i>

                <div>{{ session('info') }}</div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Close">
                </button>

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

                        <span class="text-white">
                            <span class="notranslate" translate="no">AstroVani</span>
                        </span>

                    </div>

                    <p class="text-white-50 small mb-3">
                        <span class="notranslate" translate="no">AstroVani</span> is an authentic digital sanctuary bringing ancient Vedic wisdom into modern life. Connect with experienced, verified astrologers and discover genuine, sacred remedies for personal growth and harmony.
                    </p>

                    <div class="d-flex gap-3 text-white-50">

                        <a href="#">
                            <i class="bi bi-facebook fs-5"></i>
                        </a>

                        <a href="#">
                            <i class="bi bi-instagram fs-5"></i>
                        </a>

                        <a href="#">
                            <i class="bi bi-youtube fs-5"></i>
                        </a>

                        <a href="#">
                            <i class="bi bi-whatsapp fs-5"></i>
                        </a>

                    </div>

                </div>


                <div class="col-lg-2 col-md-6">

                    <h5 class="footer-heading">
                        Quick Links
                    </h5>

                    <ul class="list-unstyled small">

                        <li class="mb-2">
                            <a href="{{ route('home') }}">
                                Home
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="{{ route('astrologers.index') }}">
                                Verified Astrologers
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="{{ route('services.index') }}">
                                Consultation Services
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="{{ route('shop.index') }}">
                                Spiritual Shop
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="#horoscope">
                                Daily Horoscopes
                            </a>
                        </li>

                    </ul>

                </div>


                <div class="col-lg-2 col-md-6">

                    <h5 class="footer-heading">
                        Customer Care
                    </h5>

                    <ul class="list-unstyled small">

                        <li class="mb-2">
                            <a href="{{ route('user.dashboard') }}">
                                My Account
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="{{ route('login') }}">
                                Customer Login
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="{{ route('register') }}">
                                Create Account
                            </a>
                        </li>

                        <li class="mb-2">
                            <a href="{{ route('admin.login') }}">
                                Admin Login
                            </a>
                        </li>

                    </ul>

                </div>


                <div class="col-lg-4 col-md-6">

                    <h5 class="footer-heading">
                        Astrology Disclaimer
                    </h5>

                    <p class="text-white-50"
                       style="font-size: 0.78rem; line-height: 1.5;">

                        Astrology readings and spiritual insights provided on <span class="notranslate" translate="no">AstroVani</span> are based on Vedic and symbolic traditions. They are meant for guidance and self-discovery and do not constitute professional medical, legal, or financial advice. We do not make supernatural guarantees.

                    </p>

                    <div class="small text-warning">

                        <i class="bi bi-patch-check-fill me-1"></i>

                        Dedicated 24/7 Spiritual Platform Support

                    </div>

                </div>

            </div>


            <hr class="border-secondary mt-4 mb-3">


            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-white-50">

                <div>
                    &copy; {{ date('Y') }} <span class="notranslate" translate="no">AstroVani</span> Platform. All rights reserved.
                </div>

                <div class="mt-2 mt-md-0">

                    <span class="me-3">
                        Crafted with <span class="notranslate" translate="no">Laravel 13</span> &amp; <span class="notranslate" translate="no">Bootstrap 5</span>
                    </span>

                </div>

            </div>

        </div>

    </footer>

    <x-app-dock />

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const websiteLanguages = ['en', 'hi', 'ta', 'te', 'bn', 'mr', 'gu', 'pa'];

        function selectedWebsiteLanguage() {
            const match = document.cookie.match(/(?:^|;\s*)googtrans=\/en\/([a-z]{2})(?:;|$)/i);
            return match && websiteLanguages.includes(match[1]) ? match[1] : 'en';
        }

        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,hi,ta,te,bn,mr,gu,pa',
                autoDisplay: false
            }, 'google_translate_element');

            const languageSelect = document.getElementById('website-language');
            if (languageSelect) {
                languageSelect.value = selectedWebsiteLanguage();
            }
        }

        function changeWebsiteLanguage(language) {
            if (!websiteLanguages.includes(language)) {
                return;
            }

            const translationSelect = document.querySelector('.goog-te-combo');
            const languageSelect = document.getElementById('website-language');

            if (language === 'en') {
                document.cookie = 'googtrans=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                if (languageSelect) {
                    languageSelect.value = 'en';
                }
                window.location.reload();
                return;
            }

            document.cookie = `googtrans=/en/${language}; path=/;`;
            if (languageSelect) {
                languageSelect.value = language;
            }

            if (translationSelect) {
                translationSelect.value = language;
                translationSelect.dispatchEvent(new Event('change'));
            } else {
                window.location.reload();
            }
        }

        const websiteLanguageSelect = document.getElementById('website-language');
        if (websiteLanguageSelect) {
            websiteLanguageSelect.value = selectedWebsiteLanguage();
        }
    </script>
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <script src="{{ asset('js/astro-live-wallpaper.js') }}"></script>
    <script src="{{ asset('js/astro-dock.js') }}"></script>

    @stack('scripts')

</body>
</html>
