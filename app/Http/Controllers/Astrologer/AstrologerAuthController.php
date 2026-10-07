<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\User;
use App\Models\Wallet;
use App\Services\ReferralCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AstrologerAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('web')->check() && Auth::user()->isAstrologer()) {
            return redirect()->route('astrologer.dashboard');
        }

        return view('astrologer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::guard('web')->attempt($credentials, $remember)) {
            return back()->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $user = Auth::guard('web')->user();

        if (!$user->isAstrologer()) {
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Access denied. This portal is exclusively for verified astrologers.']);
        }

        $astrologer = $user->astrologer;

        if (!$astrologer) {
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'No astrologer profile associated with this account.']);
        }

        if ($astrologer->approval_status === Astrologer::APPROVAL_PENDING) {
            return redirect()->route('astrologer.pending');
        }

        if ($astrologer->approval_status === Astrologer::APPROVAL_REJECTED) {
            Auth::guard('web')->logout();
            return back()->withErrors(['email' => 'Your astrologer partner application has been rejected.']);
        }

        if ($astrologer->approval_status === Astrologer::APPROVAL_SUSPENDED) {
            return redirect()->route('astrologer.suspended');
        }

        return redirect()->intended(route('astrologer.dashboard'))
            ->with('success', 'Welcome back, ' . $astrologer->display_name . '!');
    }

    public function showRegisterForm()
    {
        return view('astrologer.auth.register');
    }

    public function register(Request $request, ReferralCodeService $codeService)
    {
        $validated = $request->validate([
            'first_name'        => ['required', 'string', 'max:50'],
            'last_name'         => ['required', 'string', 'max:50'],
            'email'             => ['required', 'string', 'email', 'max:255', 'unique:users,email', 'unique:astrologers,email'],
            'phone'             => ['required', 'string', 'max:20'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'experience_years'  => ['required', 'integer', 'min:0', 'max:60'],
            'specializations'   => ['required', 'string', 'max:255'],
            'languages'         => ['required', 'string', 'max:255'],
            'bio'               => ['required', 'string', 'min:30'],
        ]);

        $displayName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        // Create User account
        $user = User::create([
            'role'              => 'astrologer',
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'email'             => $validated['email'],
            'phone'             => $validated['phone'],
            'password'          => Hash::make($validated['password']),
            'status'            => 'active',
            'email_verified_at' => now(),
        ]);

        // Generate base slug
        $baseSlug = Str::slug($displayName);
        $slug = $baseSlug;
        $counter = 1;
        while (Astrologer::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        // Create Astrologer profile
        $astrologer = Astrologer::create([
            'user_id'          => $user->id,
            'display_name'     => $displayName,
            'email'            => $validated['email'],
            'phone'            => $validated['phone'],
            'slug'             => $slug,
            'specializations'  => $validated['specializations'],
            'languages'        => $validated['languages'],
            'experience_years' => $validated['experience_years'],
            'bio'              => $validated['bio'],
            'short_bio'        => Str::limit($validated['bio'], 150),
            'status'           => 'pending',
            'approval_status'  => Astrologer::APPROVAL_PENDING,
        ]);

        // Generate unique referral code
        $referralCode = $codeService->generateForAstrologer($astrologer);
        $astrologer->update(['referral_code' => $referralCode]);

        // Create Wallet
        Wallet::create([
            'astrologer_id'      => $astrologer->id,
            'available_balance'  => 0.00,
            'pending_balance'    => 0.00,
            'held_balance'       => 0.00,
            'lifetime_earnings'  => 0.00,
            'lifetime_withdrawn' => 0.00,
            'currency'           => 'INR',
        ]);

        Auth::guard('web')->login($user);

        return redirect()->route('astrologer.pending')
            ->with('success', 'Registration submitted successfully! Your application is under admin review.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('astrologer.login')
            ->with('info', 'You have been logged out.');
    }

    public function pending()
    {
        $user = Auth::guard('web')->user();
        $astrologer = $user?->astrologer;

        if ($astrologer && $astrologer->approval_status === Astrologer::APPROVAL_APPROVED) {
            return redirect()->route('astrologer.dashboard');
        }

        return view('astrologer.auth.pending', compact('astrologer'));
    }

    public function suspended()
    {
        $user = Auth::guard('web')->user();
        $astrologer = $user?->astrologer;

        return view('astrologer.auth.suspended', compact('astrologer'));
    }
}
