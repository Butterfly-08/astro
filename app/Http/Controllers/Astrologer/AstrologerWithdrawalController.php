<?php

namespace App\Http\Controllers\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Withdrawal;
use App\Services\WithdrawalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AstrologerWithdrawalController extends Controller
{
    public function __construct(
        private readonly WithdrawalService $withdrawalService
    ) {}

    public function index(Request $request)
    {
        $astrologer = Auth::user()->astrologer;
        $wallet = $astrologer->wallet;

        $withdrawals = Withdrawal::where('astrologer_id', $astrologer->id)
            ->latest()
            ->paginate(15);

        $minAmount = (float) Setting::get(Setting::WITHDRAWAL_MIN_AMOUNT, 500);
        $maxAmount = (float) Setting::get(Setting::WITHDRAWAL_MAX_AMOUNT, 50000);
        $feeType   = Setting::get(Setting::WITHDRAWAL_FEE_TYPE, 'fixed');
        $feeValue  = (float) Setting::get(Setting::WITHDRAWAL_FEE_VALUE, 0);

        // Check if there is an active pending withdrawal
        $hasPending = Withdrawal::where('astrologer_id', $astrologer->id)
            ->whereIn('status', [
                Withdrawal::STATUS_PENDING,
                Withdrawal::STATUS_UNDER_REVIEW,
                Withdrawal::STATUS_APPROVED,
                Withdrawal::STATUS_PROCESSING,
            ])
            ->exists();

        return view('astrologer.withdrawals.index', compact(
            'astrologer',
            'wallet',
            'withdrawals',
            'minAmount',
            'maxAmount',
            'feeType',
            'feeValue',
            'hasPending'
        ));
    }

    public function store(Request $request)
    {
        $astrologer = Auth::user()->astrologer;

        $validated = $request->validate([
            'amount'                => ['required', 'numeric', 'min:1'],
            'payment_method'        => ['required', 'in:bank_transfer,upi'],
            'upi_id'                => ['required_if:payment_method,upi', 'nullable', 'string', 'max:100', 'regex:/^[a-zA-Z0-9.\-_]{2,49}@[a-zA-Z]{2,}/'],
            'account_holder_name'   => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'bank_name'             => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'max:100'],
            'account_number'        => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'min:9', 'max:20'],
            'ifsc_code'             => ['required_if:payment_method,bank_transfer', 'nullable', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/i'],
        ], [
            'upi_id.regex'          => 'Please enter a valid UPI ID (e.g., yourname@okhdfcbank or 9876543210@upi).',
            'ifsc_code.regex'       => 'Please enter a valid 11-character Indian IFSC code (e.g., HDFC0001234).',
        ]);

        try {
            $withdrawal = $this->withdrawalService->createRequest($astrologer, $validated);

            return redirect()->route('astrologer.withdrawals.index')
                ->with('success', "Withdrawal request for ₹" . number_format($withdrawal->amount, 2) . " submitted successfully! Our finance team will review and disburse shortly.");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function cancel(Withdrawal $withdrawal)
    {
        $astrologer = Auth::user()->astrologer;

        if ($withdrawal->astrologer_id !== $astrologer->id) {
            abort(403, 'Unauthorized access.');
        }

        try {
            $this->withdrawalService->cancel($withdrawal, $astrologer);

            return redirect()->route('astrologer.withdrawals.index')
                ->with('success', 'Withdrawal request was cancelled and funds have been restored to your available balance.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
