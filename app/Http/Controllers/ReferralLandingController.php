<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\Product;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralLandingController extends Controller
{
    public function handle(Request $request, string $code, ReferralService $referralService)
    {
        $code = strtoupper(trim($code));

        $astrologer = Astrologer::where('referral_code', $code)
            ->where('approval_status', Astrologer::APPROVAL_APPROVED)
            ->first();

        if (!$astrologer) {
            return redirect()->route('shop.index')
                ->with('warning', 'The referral link you followed is invalid or has expired.');
        }

        // If specific product slug provided
        $product = null;
        $targetUrl = route('shop.index');

        if ($request->filled('product')) {
            $product = Product::where('slug', $request->query('product'))->first();
            if ($product) {
                $targetUrl = route('shop.product.show', ['slug' => $product->slug]);
            }
        }

        // Track referral click (stores attribution & queues cookie)
        $referralService->trackClick($code, $request, $product);

        // Flash banner
        session()->flash('referred_by', [
            'name' => $astrologer->display_name,
            'code' => $code,
            'image' => $astrologer->profile_image,
        ]);

        return redirect($targetUrl);
    }
}
