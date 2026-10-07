<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Astrologer Partner Registration — AstroReferral</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            padding: 40px 20px;
        }
        .register-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 640px;
            padding: 40px;
        }
        .brand-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #F5B041 0%, #E67E22 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.6rem;
            margin: 0 auto 14px;
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
    </style>
</head>
<body>
    <div class="register-card">
        <div class="text-center mb-4">
            <div class="brand-icon">
                <i class="bi bi-stars"></i>
            </div>
            <h3 class="fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Join Astrologer Referral Partner Program</h3>
            <p class="text-muted small">Earn attractive commissions by recommending sanctified gemstones, rudraksha & remedies</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('astrologer.register.post') }}" method="POST">
            @csrf
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Email Address *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Phone / WhatsApp Number *</label>
                    <input type="text" name="phone" class="form-control" placeholder="+91 9876543210" value="{{ old('phone') }}" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Password (min 8 chars) *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Confirm Password *</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Experience (Years) *</label>
                    <input type="number" name="experience_years" class="form-control" min="0" max="60" value="{{ old('experience_years', 5) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Specializations *</label>
                    <input type="text" name="specializations" class="form-control" placeholder="Vedic, Tarot, Gemology" value="{{ old('specializations', 'Vedic Astrology, Gemology') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Languages *</label>
                    <input type="text" name="languages" class="form-control" placeholder="Hindi, English" value="{{ old('languages', 'Hindi, English') }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-semibold">Professional Bio & Experience *</label>
                <textarea name="bio" class="form-control" rows="3" placeholder="Describe your astrology background, education and expertise in prescribing spiritual remedies..." required>{{ old('bio') }}</textarea>
                <small class="text-muted">Minimum 30 characters. Will be reviewed by our compliance team.</small>
            </div>

            <button type="submit" class="btn btn-gold mb-3">
                <i class="bi bi-send-check me-2"></i> Submit Partner Application
            </button>
        </form>

        <div class="text-center pt-3 border-top">
            <p class="small text-muted mb-0">Already registered? <a href="{{ route('astrologer.login') }}" class="fw-semibold text-decoration-none" style="color: #E67E22;">Sign In here</a></p>
        </div>
    </div>
</body>
</html>
