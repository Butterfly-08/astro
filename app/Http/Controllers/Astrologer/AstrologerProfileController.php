<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AstrologerProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $astrologer = $user->astrologer;

        return view('astrologer.profile.index', compact('user', 'astrologer'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $astrologer = $user->astrologer;

        $validated = $request->validate([
            'first_name'        => ['required', 'string', 'max:50'],
            'last_name'         => ['required', 'string', 'max:50'],
            'phone'             => ['required', 'string', 'max:20'],
            'specializations'   => ['required', 'string', 'max:255'],
            'languages'         => ['required', 'string', 'max:255'],
            'experience_years'  => ['required', 'integer', 'min:0', 'max:60'],
            'bio'               => ['required', 'string', 'min:30'],
        ]);

        $displayName = trim($validated['first_name'] . ' ' . $validated['last_name']);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'phone'      => $validated['phone'],
        ]);

        $astrologer->update([
            'display_name'     => $displayName,
            'phone'            => $validated['phone'],
            'specializations'  => $validated['specializations'],
            'languages'        => $validated['languages'],
            'experience_years' => $validated['experience_years'],
            'bio'              => $validated['bio'],
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
