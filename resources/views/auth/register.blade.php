@extends('layouts.auth')

@section('title', 'Create Account — AstroVani')

@section('content')
<div class="col-md-8 col-lg-7">
    <div class="auth-card">
        <div class="auth-header">
            <i class="bi bi-moon-stars-fill logo-icon"></i>
            <h3 class="text-white">Join AstroVani</h3>
            <p class="text-white-50 small mb-0">Begin your journey of spiritual guidance & authentic remedies</p>
        </div>

        <div class="p-4 p-md-5">
            <form action="{{ route('register.submit') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="first_name" class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="first_name" 
                               id="first_name" 
                               class="form-control @error('first_name') is-invalid @enderror" 
                               value="{{ old('first_name') }}" 
                               required 
                               placeholder="e.g. Rahul">
                        @error('first_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label fw-semibold">Last Name</label>
                        <input type="text" 
                               name="last_name" 
                               id="last_name" 
                               class="form-control @error('last_name') is-invalid @enderror" 
                               value="{{ old('last_name') }}" 
                               placeholder="e.g. Sharma">
                        @error('last_name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email') }}" 
                               required 
                               placeholder="name@example.com">
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Phone Number</label>
                        <input type="text" 
                               name="phone" 
                               id="phone" 
                               class="form-control @error('phone') is-invalid @enderror" 
                               value="{{ old('phone') }}" 
                               placeholder="+91 9876543210">
                        @error('phone')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               required 
                               placeholder="Minimum 8 characters">
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="form-control" 
                               required 
                               placeholder="Repeat password">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label fw-semibold">Date of Birth</label>
                        <input type="date" 
                               name="date_of_birth" 
                               id="date_of_birth" 
                               class="form-control @error('date_of_birth') is-invalid @enderror" 
                               value="{{ old('date_of_birth') }}">
                        @error('date_of_birth')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="gender" class="form-label fw-semibold">Gender</label>
                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label for="city" class="form-label fw-semibold">City</label>
                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city') }}" placeholder="City">
                    </div>
                    <div class="col-md-4">
                        <label for="state" class="form-label fw-semibold">State</label>
                        <input type="text" name="state" id="state" class="form-control" value="{{ old('state') }}" placeholder="State">
                    </div>
                    <div class="col-md-4">
                        <label for="country" class="form-label fw-semibold">Country</label>
                        <input type="text" name="country" id="country" class="form-control" value="{{ old('country', 'India') }}" placeholder="Country">
                    </div>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="terms" class="form-check-input @error('terms') is-invalid @enderror" id="terms" value="1" {{ old('terms') ? 'checked' : '' }} required>
                    <label class="form-check-label text-muted small" for="terms">
                        I agree to AstroVani's Terms of Service and Privacy Policy.
                    </label>
                    @error('terms')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-astro-primary py-2 fw-bold d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-person-check-fill"></i>
                        <span>Create My Account</span>
                    </button>
                </div>

                <div class="text-center text-muted small mb-2">
                    Already registered with AstroVani? 
                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-primary">Log In Here</a>
                </div>

                <div class="text-center pt-2 border-top">
                    <a href="{{ route('home') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i> Return to AstroVani Home
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
