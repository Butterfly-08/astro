<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function __construct(
        private readonly WithdrawalService $withdrawalService
    ) {}

    public function index(Request $request)
    {
        $query = Withdrawal::with(['astrologer', 'wallet'])
            ->latest();

        if ($request->filled('status') && in_array($request->status, Withdrawal::allStatuses())) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_id', $request->astrologer_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('withdrawal_number', 'like', "%{$search}%")
                  ->orWhere('transaction_reference', 'like', "%{$search}%")
                  ->orWhere('upi_id', 'like', "%{$search}%")
                  ->orWhereHas('astrologer', function ($aq) use ($search) {
                      $aq->where('display_name', 'like', "%{$search}%");
                  });
            });
        }

        $withdrawals = $query->paginate(20)->withQueryString();

        $astrologers = Astrologer::approved()->orderBy('display_name')->get();

        $metrics = [
            'total_requests'  => Withdrawal::count(),
            'pending_count'   => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->count(),
            'approved_count'  => Withdrawal::where('status', Withdrawal::STATUS_APPROVED)->count(),
            'processing_count'=> Withdrawal::where('status', Withdrawal::STATUS_PROCESSING)->count(),
            'paid_amount'     => Withdrawal::where('status', Withdrawal::STATUS_PAID)->sum('amount'),
            'pending_amount'  => Withdrawal::whereIn('status', [Withdrawal::STATUS_PENDING, Withdrawal::STATUS_APPROVED, Withdrawal::STATUS_PROCESSING])->sum('amount'),
        ];

        return view('admin.withdrawals.index', compact('withdrawals', 'astrologers', 'metrics'));
    }

    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->load(['astrologer.wallet', 'wallet']);

        return view('admin.withdrawals.show', compact('withdrawal'));
    }

    public function approve(Request $request, Withdrawal $withdrawal)
    {
        $adminId = Auth::guard('admin')->id();

        try {
            $this->withdrawalService->approve($withdrawal, $adminId, $request->notes);

            return back()->with('success', "Withdrawal request #{$withdrawal->withdrawal_number} has been approved.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markProcessing(Withdrawal $withdrawal)
    {
        $adminId = Auth::guard('admin')->id();

        try {
            $this->withdrawalService->markProcessing($withdrawal, $adminId);

            return back()->with('success', "Withdrawal #{$withdrawal->withdrawal_number} marked as processing.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function markPaid(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'transaction_reference' => ['required', 'string', 'min:4', 'max:100'],
        ]);

        $adminId = Auth::guard('admin')->id();

        try {
            $this->withdrawalService->markPaid($withdrawal, $adminId, $request->transaction_reference);

            return back()->with('success', "Withdrawal #{$withdrawal->withdrawal_number} marked as PAID. Bank/UTR reference: {$request->transaction_reference}");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Withdrawal $withdrawal)
    {
        $request->validate([
            'reason' => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $adminId = Auth::guard('admin')->id();

        try {
            $this->withdrawalService->reject($withdrawal, $adminId, $request->reason);

            return back()->with('success', "Withdrawal request #{$withdrawal->withdrawal_number} was rejected. Funds restored to astrologer available balance.");
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
