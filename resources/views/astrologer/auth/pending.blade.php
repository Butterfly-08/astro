<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Under Review — AstroReferral</title>
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
            padding: 24px;
        }
        .card-box {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 500px;
            padding: 44px 36px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="card-box">
        <div class="mb-4 text-warning">
            <i class="bi bi-hourglass-split" style="font-size: 4rem;"></i>
        </div>
        <h3 class="fw-bold mb-2" style="font-family: 'Outfit', sans-serif;">Application Under Review</h3>
        <p class="text-muted mb-4">
            Thank you for registering with AstroReferral! Your astrologer partner profile has been submitted and is currently being verified by our compliance team.
        </p>
        <div class="alert alert-light border text-start small mb-4">
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Account Name:</span>
                <span class="fw-semibold">{{ $astrologer->display_name ?? 'Partner' }}</span>
            </div>
            <div class="d-flex justify-content-between mb-1">
                <span class="text-muted">Status:</span>
                <span class="badge bg-warning text-dark">Pending Verification</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-muted">Expected Review:</span>
                <span class="fw-semibold">Within 24–48 Hours</span>
            </div>
        </div>

        <form action="{{ route('astrologer.logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100">
                <i class="bi bi-box-arrow-right me-2"></i> Log Out
            </button>
        </form>
    </div>
</body>
</html>
