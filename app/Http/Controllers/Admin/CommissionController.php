<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Commission;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommissionController extends Controller
{
    public function __construct(
        private readonly CommissionService $commissionService
    ) {}

    public function index(Request $request)
    {
        $query = Commission::with(['astrologer', 'product', 'order'])
            ->latest();

        if ($request->filled('status') && in_array($request->status, Commission::allStatuses())) {
            $query->where('status', $request->status);
        }

        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_id', $request->astrologer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('commission_number', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($oq) use ($search) {
                      $oq->where('order_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('astrologer', function ($aq) use ($search) {
                      $aq->where('display_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $commissions = $query->paginate(20)->withQueryString();

        $astrologers = Astrologer::approved()->orderBy('display_name')->get();

        $metrics = [
            'total_commissions' => Commission::count(),
            'pending_count'     => Commission::where('status', Commission::STATUS_PENDING)->count(),
            'pending_amount'    => Commission::where('status', Commission::STATUS_PENDING)->sum('commission_amount'),
            'available_amount'  => Commission::where('status', Commission::STATUS_AVAILABLE)->sum('commission_amount'),
            'paid_amount'       => Commission::where('status', Commission::STATUS_PAID)->sum('commission_amount'),
        ];

        return view('admin.commissions.index', compact('commissions', 'astrologers', 'metrics'));
    }

    public function show(Commission $commission)
    {
        $commission->load(['astrologer.wallet', 'product', 'order.items', 'referral']);

        return view('admin.commissions.show', compact('commission'));
    }

    public function approve(Commission $commission)
    {
        $adminId = Auth::guard('admin')->id();

        try {
            $this->commissionService->approve($commission, $adminId);

            return back()->with('success', "Commission #{$commission->commission_number} has been approved.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Commission $commission)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $adminId = Auth::guard('admin')->id();

        try {
            $this->commissionService->reject($commission, $adminId, $request->reason);

            return back()->with('success', "Commission #{$commission->commission_number} has been rejected.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
