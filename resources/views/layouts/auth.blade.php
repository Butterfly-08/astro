<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Authentication') — AstroVani</title>
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
            --astro-purple-dark: #1A0B2E;
            --astro-purple: #2D124D;
            --astro-purple-light: #481B7F;
            --astro-gold: #F5B041;
            --astro-gold-hover: #D4AC0D;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #2D124D, #1A0B2E 70%, #0F051C);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
            color: #374151;
        }

        .auth-card {
            background-color: #FFFFFF;
            border-radius: 16px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(245, 176, 65, 0.2);
            overflow: hidden;
            width: 100%;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--astro-purple) 0%, var(--astro-purple-dark) 100%);
            color: #FFFFFF;
            padding: 28px 24px;
            text-align: center;
            border-bottom: 2px solid var(--astro-gold);
        }

        .auth-header .logo-icon {
            font-size: 2.2rem;
            color: var(--astro-gold);
        }

        .auth-header h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            margin-top: 8px;
            margin-bottom: 4px;
        }

        .btn-astro-primary {
            background: linear-gradient(135deg, var(--astro-purple) 0%, var(--astro-purple-light) 100%);
            color: #FFFFFF;
            font-weight: 600;
            padding: 11px 20px;
            border-radius: 8px;
            border: none;
            transition: all 0.25s ease;
        }

        .btn-astro-primary:hover {
            background: linear-gradient(135deg, #3A1663 0%, #5B239F 100%);
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(45, 18, 77, 0.25);
        }

        .btn-astro-gold {
            background-color: var(--astro-gold);
            color: var(--astro-purple-dark);
            font-weight: 600;
            padding: 11px 20px;
            border-radius: 8px;
            border: none;
            transition: all 0.25s ease;
        }

        .btn-astro-gold:hover {
            background-color: var(--astro-gold-hover);
            color: #FFFFFF;
            transform: translateY(-1px);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--astro-purple);
            box-shadow: 0 0 0 0.2rem rgba(45, 18, 77, 0.15);
        }
    </style>
    <link rel="stylesheet" href="{{ asset('css/astro-live-wallpaper.css') }}">
    @stack('styles')
</head>
<body>
    <x-page-loader />

    <!-- Live Background Wallpaper Video & Constellation FX -->
    <x-live-wallpaper />

    <div class="container position-relative" style="z-index: 10;">
        <div class="row justify-content-center">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/astro-live-wallpaper.js') }}"></script>
    @stack('scripts')
</body>
</html>
