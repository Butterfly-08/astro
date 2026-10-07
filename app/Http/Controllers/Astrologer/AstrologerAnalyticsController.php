<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Product;
use App\Models\Referral;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AstrologerAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $astrologer = Auth::user()->astrologer;
        $range = $request->query('range', '30'); // 7, 30, 90 days

        $days = in_array($range, ['7', '30', '90']) ? (int) $range : 30;
        $startDate = Carbon::today()->subDays($days - 1);

        // Daily clicks and conversions
        $dailyClicks = Referral::forAstrologer($astrologer->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $dailyConversions = Referral::forAstrologer($astrologer->id)
            ->converted()
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $clicksData = [];
        $conversionsData = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('d M');
            $clicksData[] = $dailyClicks->get($date, 0);
            $conversionsData[] = $dailyConversions->get($date, 0);
        }

        // Top performing products by commissions
        $topProducts = Commission::forAstrologer($astrologer->id)
            ->where('status', '!=', Commission::STATUS_REJECTED)
            ->where('status', '!=', Commission::STATUS_CANCELLED)
            ->select('product_id', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(commission_amount) as total_commission'))
            ->groupBy('product_id')
            ->orderByDesc('total_commission')
            ->take(5)
            ->with('product')
            ->get();

        // Totals
        $totalClicks = Referral::forAstrologer($astrologer->id)->where('created_at', '>=', $startDate)->count();
        $totalConversions = Referral::forAstrologer($astrologer->id)->converted()->where('created_at', '>=', $startDate)->count();
        $totalEarnings = Commission::forAstrologer($astrologer->id)
            ->where('created_at', '>=', $startDate)
            ->whereIn('status', [Commission::STATUS_PENDING, Commission::STATUS_AVAILABLE, Commission::STATUS_PAID])
            ->sum('commission_amount');

        $conversionRate = $totalClicks > 0 ? round(($totalConversions / $totalClicks) * 100, 2) : 0;

        return view('astrologer.analytics.index', compact(
            'astrologer',
            'range',
            'labels',
            'clicksData',
            'conversionsData',
            'topProducts',
            'totalClicks',
            'totalConversions',
            'totalEarnings',
            'conversionRate'
        ));
    }
}
