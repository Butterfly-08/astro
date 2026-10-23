<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthApiController extends Controller
{
    /**
     * Admin API Login to obtain Sanctum Bearer Token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('email', $credentials['email'])->first();

        if (!$admin || !Hash::check($credentials['password'], $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid administrator credentials.',
            ], 401);
        }

        if (!$admin->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'This administrator account is inactive.',
            ], 403);
        }

        $admin->update(['last_login_at' => now()]);

        $token = $admin->createToken('admin_api_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Administrator login successful.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'admin'      => [
                    'id'    => $admin->id,
                    'name'  => $admin->name,
                    'email' => $admin->email,
                    'phone' => $admin->phone,
                ],
            ],
        ]);
    }

    /**
     * Get authenticated admin profile.
     */
    public function me(Request $request): JsonResponse
    {
        $admin = $request->user();

        if (!$admin instanceof Admin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Administrator access required.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data'    => $admin,
        ]);
    }

    /**
     * Admin API Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Administrator logged out successfully.',
        ]);
    }
}
