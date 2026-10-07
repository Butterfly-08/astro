<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    public function index(Request $request)
    {
        $query = Wallet::with('astrologer')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('astrologer', function ($q) use ($search) {
                $q->where('display_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        $wallets = $query->paginate(15)->withQueryString();

        $metrics = [
            'total_available'  => Wallet::sum('available_balance'),
            'total_pending'    => Wallet::sum('pending_balance'),
            'total_held'       => Wallet::sum('held_balance'),
            'lifetime_payouts' => Wallet::sum('lifetime_withdrawn'),
        ];

        return view('admin.wallets.index', compact('wallets', 'metrics'));
    }

    public function transactions(Request $request)
    {
        $query = WalletTransaction::with(['astrologer', 'wallet'])
            ->latest();

        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_id', $request->astrologer_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_key', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('astrologer', function ($aq) use ($search) {
                      $aq->where('display_name', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->paginate(20)->withQueryString();
        $astrologers = Astrologer::approved()->orderBy('display_name')->get();

        return view('admin.wallets.transactions', compact('transactions', 'astrologers'));
    }

    public function adjust(Request $request, Wallet $wallet)
    {
        $validated = $request->validate([
            'action'  => ['required', 'in:credit,debit'],
            'amount'  => ['required', 'numeric', 'min:1', 'max:500000'],
            'reason'  => ['required', 'string', 'min:5', 'max:255'],
        ]);

        $adminId = Auth::guard('admin')->id();
        $amount = (float) $validated['amount'];
        if ($validated['action'] === 'debit') {
            $amount = -$amount;
        }

        try {
            $this->walletService->adminAdjust($wallet, $amount, $validated['reason'], $adminId);

            $actionText = $validated['action'] === 'credit' ? 'Credited' : 'Debited';
            return back()->with('success', "Wallet {$actionText} successfully by ₹" . number_format(abs($amount), 2));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
