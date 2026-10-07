<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Product;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReferralApiController extends Controller
{
    public function __construct(
        private readonly ReferralService $referralService
    ) {}

    public function validateCode(string $code): JsonResponse
    {
        $code = strtoupper(trim($code));

        $astrologer = Astrologer::where('referral_code', $code)
            ->where('approval_status', Astrologer::APPROVAL_APPROVED)
            ->where('status', 'active')
            ->first();

        if (!$astrologer) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive referral code.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'referral_code'   => $code,
                'astrologer_name' => $astrologer->display_name,
                'profile_image'   => $astrologer->profile_image,
                'specializations' => $astrologer->specializations,
            ],
        ]);
    }

    public function trackClick(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'referral_code' => ['required', 'string'],
            'product_id'    => ['nullable', 'exists:products,id'],
        ]);

        $product = !empty($validated['product_id']) ? Product::find($validated['product_id']) : null;
        $referral = $this->referralService->trackClick($validated['referral_code'], $request, $product);

        if (!$referral) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to track referral. Code may be invalid or system disabled.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Referral click registered successfully.',
            'data'    => [
                'referral_id'   => $referral->id,
                'referral_code' => $referral->referral_code,
            ],
        ]);
    }
}
