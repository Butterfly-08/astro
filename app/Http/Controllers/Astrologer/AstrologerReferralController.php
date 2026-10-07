<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Product;
use App\Models\Referral;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AstrologerReferralController extends Controller
{
    public function index(Request $request, CommissionService $commissionService)
    {
        $astrologer = Auth::user()->astrologer;
        $referralCode = $astrologer->referral_code;

        $generalReferralUrl = url('/ref/' . $referralCode);

        // Fetch active products with referral enabled
        $products = Product::with('category')
            ->where('status', 'active')
            ->where('referral_enabled', true)
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->category, function ($query, $catId) {
                $query->where('category_id', $catId);
            })
            ->paginate(12)
            ->withQueryString();

        // Enrich products with calculated referral link and estimated commission
        $products->getCollection()->transform(function ($product) use ($referralCode, $commissionService) {
            $product->referral_url = url('/shop/product/' . $product->slug . '?ref=' . $referralCode);

            // Calculate sample commission for 1 item
            $price = $product->effective_price;
            [$type, $rate] = $commissionService->resolveCommissionConfig($product, $product->category);

            if ($type === 'percent') {
                $est = ($price * $rate) / 100;
                if ($product->commission_cap > 0) {
                    $est = min($est, (float) $product->commission_cap);
                }
                $product->est_commission_display = '₹' . number_format($est, 2) . " ({$rate}%)";
            } else {
                $product->est_commission_display = '₹' . number_format($rate, 2) . " (Fixed)";
            }

            return $product;
        });

        // Referral stats
        $totalClicks = Referral::forAstrologer($astrologer->id)->count();
        $totalConversions = Referral::forAstrologer($astrologer->id)->converted()->count();

        return view('astrologer.referrals.index', compact(
            'astrologer',
            'referralCode',
            'generalReferralUrl',
            'products',
            'totalClicks',
            'totalConversions'
        ));
    }

    public function qrCode(Request $request)
    {
        $astrologer = Auth::user()->astrologer;
        $targetUrl = url('/ref/' . $astrologer->referral_code);

        if ($request->filled('product_slug')) {
            $product = Product::where('slug', $request->product_slug)->first();
            if ($product) {
                $targetUrl = url('/shop/product/' . $product->slug . '?ref=' . $astrologer->referral_code);
            }
        }

        // Generate QR code safely
        if (class_exists(\SimpleSoftwareIO\QrCode\Facades\QrCode::class)) {
            $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(250)->generate($targetUrl);
            return response($qrSvg)->header('Content-Type', 'image/svg+xml');
        }

        // Fallback SVG QR code using quickchart.io or inline redirect
        $apiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode($targetUrl);
        return redirect()->away($apiUrl);
    }
}
