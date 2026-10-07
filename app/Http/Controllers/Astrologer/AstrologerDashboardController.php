<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * AstrologerDashboardController — main dashboard for approved astrologers.
 */
class AstrologerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $astrologer = $this->getAstrologer();
        $wallet     = $astrologer->wallet;

        // Dashboard stats
        $stats = [
            'total_clicks'        => Referral::forAstrologer($astrologer->id)->count(),
            'total_orders'        => Referral::forAstrologer($astrologer->id)->converted()->count(),
            'pending_commission'  => Commission::forAstrologer($astrologer->id)
                                        ->where('status', Commission::STATUS_PENDING)
                                        ->sum('commission_amount'),
            'available_balance'   => $wallet ? (float) $wallet->available_balance : 0,
            'lifetime_earnings'   => $wallet ? (float) $wallet->lifetime_earnings : 0,
            'total_withdrawn'     => $wallet ? (float) $wallet->lifetime_withdrawn : 0,
            'pending_withdrawals' => Withdrawal::where('astrologer_id', $astrologer->id)
                                        ->whereIn('status', [
                                            Withdrawal::STATUS_PENDING,
                                            Withdrawal::STATUS_UNDER_REVIEW,
                                            Withdrawal::STATUS_APPROVED,
                                            Withdrawal::STATUS_PROCESSING,
                                        ])
                                        ->count(),
        ];

        // Conversion rate
        $stats['conversion_rate'] = $stats['total_clicks'] > 0
            ? round(($stats['total_orders'] / $stats['total_clicks']) * 100, 1)
            : 0;

        // Recent commissions
        $recentCommissions = Commission::forAstrologer($astrologer->id)
            ->with(['product', 'order'])
            ->latest()
            ->take(5)
            ->get();

        // Recent referrals
        $recentReferrals = Referral::forAstrologer($astrologer->id)
            ->with(['product'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly earnings (last 6 months) for chart
        $driver = DB::connection()->getDriverName();
        $query = Commission::forAstrologer($astrologer->id)
            ->where('status', Commission::STATUS_AVAILABLE)
            ->where('created_at', '>=', now()->subMonths(6));

        if ($driver === 'sqlite') {
            $monthlyEarnings = $query
                ->selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, SUM(commission_amount) as total")
                ->groupByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
                ->orderByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
                ->get()
                ->mapWithKeys(function ($row) {
                    $label = \Carbon\Carbon::create((int)$row->year, (int)$row->month, 1)->format('M Y');
                    return [$label => round($row->total, 2)];
                });
        } else {
            $monthlyEarnings = $query
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(commission_amount) as total')
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->orderByRaw('YEAR(created_at), MONTH(created_at)')
                ->get()
                ->mapWithKeys(function ($row) {
                    $label = \Carbon\Carbon::create((int)$row->year, (int)$row->month, 1)->format('M Y');
                    return [$label => round($row->total, 2)];
                });
        }

        return view('astrologer.dashboard', compact(
            'astrologer',
            'wallet',
            'stats',
            'recentCommissions',
            'recentReferrals',
            'monthlyEarnings'
        ));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getAstrologer(): Astrologer
    {
        return Auth::user()->astrologer;
    }
}
