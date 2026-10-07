<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Commission;
use App\Models\Product;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AstrologerApiController extends Controller
{
    public function __construct(
        private readonly WithdrawalService $withdrawalService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        if (!$user->isAstrologer()) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Account is not registered as an astrologer.',
            ], 403);
        }

        $astrologer = $user->astrologer;

        if (!$astrologer || $astrologer->approval_status !== Astrologer::APPROVAL_APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Astrologer partner account is not active or approved.',
                'status'  => $astrologer?->approval_status ?? 'not_found',
            ], 403);
        }

        // Generate Sanctum token if available
        $token = method_exists($user, 'createToken')
            ? $user->createToken('astrologer_token')->plainTextToken
            : base64_encode(random_bytes(32));

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data'    => [
                'token'      => $token,
                'token_type' => 'Bearer',
                'user'       => [
                    'id'           => $user->id,
                    'name'         => $user->full_name,
                    'email'        => $user->email,
                    'role'         => $user->role,
                ],
                'astrologer' => [
                    'id'            => $astrologer->id,
                    'display_name'  => $astrologer->display_name,
                    'referral_code' => $astrologer->referral_code,
                    'referral_url'  => url('/ref/' . $astrologer->referral_code),
                ],
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $astrologer = $user->astrologer;

        return response()->json([
            'success' => true,
            'data'    => [
                'user'       => $user,
                'astrologer' => $astrologer,
            ],
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $astrologer = $request->user()->astrologer;
        $wallet = $astrologer->wallet;

        $stats = [
            'total_clicks'        => Referral::forAstrologer($astrologer->id)->count(),
            'total_conversions'   => Referral::forAstrologer($astrologer->id)->converted()->count(),
            'available_balance'   => (float) ($wallet?->available_balance ?? 0),
            'pending_balance'     => (float) ($wallet?->pending_balance ?? 0),
            'lifetime_earnings'   => (float) ($wallet?->lifetime_earnings ?? 0),
            'lifetime_withdrawn'  => (float) ($wallet?->lifetime_withdrawn ?? 0),
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'referral_code' => $astrologer->referral_code,
                'referral_url'  => url('/ref/' . $astrologer->referral_code),
                'stats'         => $stats,
            ],
        ]);
    }

    public function wallet(Request $request): JsonResponse
    {
        $astrologer = $request->user()->astrologer;
        $wallet = $astrologer->wallet;

        $transactions = WalletTransaction::where('astrologer_id', $astrologer->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => [
                'wallet'       => $wallet,
                'transactions' => $transactions,
            ],
        ]);
    }

    public function commissions(Request $request): JsonResponse
    {
        $astrologer = $request->user()->astrologer;

        $query = Commission::forAstrologer($astrologer->id)
            ->with(['product:id,name,slug,price,image', 'order:id,order_number,created_at'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $commissions = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $commissions,
        ]);
    }

    public function withdrawals(Request $request): JsonResponse
    {
        $astrologer = $request->user()->astrologer;

        $withdrawals = Withdrawal::where('astrologer_id', $astrologer->id)
            ->latest()
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $withdrawals,
        ]);
    }

    public function requestWithdrawal(Request $request): JsonResponse
    {
        $astrologer = $request->user()->astrologer;

        $validated = $request->validate([
            'amount'                => ['required', 'numeric', 'min:1'],
            'payment_method'        => ['required', 'in:bank_transfer,upi'],
            'upi_id'                => ['required_if:payment_method,upi', 'nullable', 'string', 'max:100'],
            'account_holder_name'   => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'bank_name'             => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'account_number'        => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'min:9', 'max:20'],
            'ifsc_code'             => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'size:11'],
        ]);

        try {
            $withdrawal = $this->withdrawalService->createRequest($astrologer, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Withdrawal request created successfully.',
                'data'    => $withdrawal,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function referralLinks(Request $request): JsonResponse
    {
        $astrologer = $request->user()->astrologer;
        $code = $astrologer->referral_code;

        $products = Product::active()
            ->where('referral_enabled', true)
            ->select('id', 'name', 'slug', 'price', 'sale_price', 'image')
            ->paginate(20);

        $products->getCollection()->transform(function ($product) use ($code) {
            $product->referral_url = url('/shop/product/' . $product->slug . '?ref=' . $code);
            return $product;
        });

        return response()->json([
            'success' => true,
            'data'    => [
                'general_url' => url('/ref/' . $code),
                'products'    => $products,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        if (method_exists($request->user(), 'currentAccessToken') && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }
}
