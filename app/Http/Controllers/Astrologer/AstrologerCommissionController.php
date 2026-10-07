<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AstrologerCommissionController extends Controller
{
    public function index(Request $request)
    {
        $astrologer = Auth::user()->astrologer;

        $query = Commission::forAstrologer($astrologer->id)
            ->with(['product', 'order', 'orderItem'])
            ->latest();

        // Filter by status
        if ($request->filled('status') && in_array($request->status, Commission::allStatuses())) {
            $query->where('status', $request->status);
        }

        // Search by order ID or product name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('order', function ($oq) use ($search) {
                    $oq->where('order_number', 'like', "%{$search}%");
                })->orWhereHas('product', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->paginate(15)->withQueryString();

        // Summary metrics
        $metrics = [
            'total_earned'     => Commission::forAstrologer($astrologer->id)->sum('commission_amount'),
            'pending_amount'   => Commission::forAstrologer($astrologer->id)->where('status', Commission::STATUS_PENDING)->sum('commission_amount'),
            'available_amount' => Commission::forAstrologer($astrologer->id)->where('status', Commission::STATUS_AVAILABLE)->sum('commission_amount'),
            'paid_amount'      => Commission::forAstrologer($astrologer->id)->where('status', Commission::STATUS_PAID)->sum('commission_amount'),
        ];

        return view('astrologer.commissions.index', compact('astrologer', 'commissions', 'metrics'));
    }

    public function show(Commission $commission)
    {
        $astrologer = Auth::user()->astrologer;

        if ($commission->astrologer_id !== $astrologer->id) {
            abort(403, 'Unauthorized access to commission details.');
        }

        $commission->load(['product', 'order', 'orderItem', 'referral']);

        return view('astrologer.commissions.show', compact('commission', 'astrologer'));
    }
}
