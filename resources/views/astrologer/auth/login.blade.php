<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astrologer Partner Login — AstroReferral</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0F051D 0%, #1E0836 50%, #2D124D 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 440px;
            padding: 40px 36px;
        }
        .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #F5B041 0%, #E67E22 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.8rem;
            margin: 0 auto 16px;
            box-shadow: 0 8px 20px rgba(245, 176, 65, 0.35);
        }
        .btn-gold {
            background: linear-gradient(135deg, #F5B041 0%, #E67E22 100%);
            color: #fff;
            font-weight: 600;
            border: none;
            padding: 12px;
            border-radius: 10px;
            width: 100%;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #E5A030 0%, #D35400 100%);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-moon-stars-fill"></i>
            </div>
            <h3 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Astrologer Partner Portal</h3>
            <p class="text-muted small">Sign in to track your referrals, commissions & wallet</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info py-2 small">
                {{ session('info') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger py-2 small">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('astrologer.login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-semibold">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="astrologer@example.com" value="{{ old('email', 'anjali@astrovani.test') }}" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" value="Password@123" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                    <label class="form-check-label small" for="rememberMe">Remember me</label>
                </div>
            </div>

            <button type="submit" class="btn btn-gold mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In to Portal
            </button>
        </form>

        <div class="text-center pt-3 border-top">
            <p class="small text-muted mb-1">New astrologer? <a href="{{ route('astrologer.register') }}" class="text-decoration-none fw-semibold" style="color: #E67E22;">Register as Partner</a></p>
            <p class="small text-muted mb-0"><a href="{{ route('home') }}" class="text-muted text-decoration-none"><i class="bi bi-arrow-left"></i> Back to Website</a></p>
        </div>
    </div>
</body>
</html>
