<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the user login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle user login attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        /*
         * The existing users table only has:
         * id, name, email, password, etc.
         *
         * So status is not checked here.
         */

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::guard('web')->user();

            return redirect()->intended(route('user.dashboard'))
                ->with(
                    'success',
                    'Welcome back, ' . ($user->name ?? 'Customer') . '!'
                );
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'These credentials do not match our records.'
            ]);
    }

    /**
     * Show user registration form.
     */
    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('user.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle user registration.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        /*
         * The actual users table has only one name column.
         *
         * Registration form may collect first_name and last_name,
         * so combine them into the existing name column.
         */
        $firstName = trim($validated['first_name'] ?? '');
        $lastName = trim($validated['last_name'] ?? '');

        $fullName = trim($firstName . ' ' . $lastName);

        /*
         * Create user using only columns that actually exist
         * in the current users table.
         */
        $user = User::create([
            'name' => $fullName,
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        /*
         * Automatically log in the user after registration.
         */
        Auth::guard('web')->login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('user.dashboard')
            ->with(
                'success',
                'Your AstroVani account has been created successfully! Welcome aboard.'
            );
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('info', 'You have been safely logged out.');
    }
}