@extends('layouts.auth')

@section('title', 'Sign In — AstroVani')

@section('content')
<div class="col-md-6 col-lg-5">
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-stars logo-icon"></i>
            <h3 class="text-white">Sign In to AstroVani</h3>
            <p class="text-white-50 small mb-0">Access your consultations, bookings, and orders</p>
        </div>

        <div class="p-4 p-md-5">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <div>{{ session('error') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               required 
                               autocomplete="email" 
                               autofocus 
                               placeholder="your.email@example.com">
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <label for="password" class="form-label fw-semibold mb-0">Password</label>
                    </div>
                    <div class="input-group mt-1">
                        <span class="input-group-text bg-light"><i class="bi bi-lock text-muted"></i></span>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required 
                               autocomplete="current-password" 
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-muted small" for="remember">Remember me on this device</label>
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-astro-primary py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Sign In</span>
                    </button>
                </div>

                <div class="text-center text-muted small mb-3">
                    Don't have an AstroVani account? 
                    <a href="{{ route('register') }}" class="text-decoration-none fw-bold text-primary">Create Account</a>
                </div>

                <div class="text-center pt-2 border-top">
                    <a href="{{ route('home') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Back to AstroVani Home
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
