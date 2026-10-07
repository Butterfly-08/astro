<?php

namespace App\Services;

use App\Models\Astrologer;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;

/**
 * WithdrawalService — manages the full withdrawal lifecycle.
 *
 * Flow:
 *   createRequest() → hold wallet funds
 *   approve()       → mark approved (admin)
 *   markProcessing() → mark processing
 *   markPaid()      → finalise debit, record UTR (admin)
 *   reject()        → release held funds (admin)
 *   cancel()        → release held funds (astrologer)
 */
class WithdrawalService
{
    public function __construct(
        private readonly WalletService $walletService
    ) {}

    // -------------------------------------------------------------------------
    // Create Withdrawal Request
    // -------------------------------------------------------------------------

    /**
     * Create a new withdrawal request.
     *
     * Validates: min/max amount, wallet balance, and concurrent request limits.
     * Holds the requested amount in the wallet.
     *
     * @param  Astrologer $astrologer
     * @param  array      $data  Validated request data
     * @return Withdrawal
     * @throws \RuntimeException|\InvalidArgumentException
     */
    public function createRequest(Astrologer $astrologer, array $data): Withdrawal
    {
        $amount = round((float) $data['amount'], 2);

        // Configuration
        $minAmount = (float) Setting::get(Setting::WITHDRAWAL_MIN_AMOUNT, 500);
        $maxAmount = (float) Setting::get(Setting::WITHDRAWAL_MAX_AMOUNT, 50000);

        // Validate amount range
        if ($amount < $minAmount) {
            throw new \InvalidArgumentException(
                "Minimum withdrawal amount is ₹{$minAmount}. You requested ₹{$amount}."
            );
        }

        if ($amount > $maxAmount) {
            throw new \InvalidArgumentException(
                "Maximum withdrawal amount is ₹{$maxAmount}. You requested ₹{$amount}."
            );
        }

        $wallet = $this->walletService->getOrCreate($astrologer);

        // Check withdrawable balance
        $withdrawable = (float) $wallet->available_balance - (float) $wallet->held_balance;
        if ($amount > $withdrawable) {
            throw new \RuntimeException(
                "Insufficient available balance. Available: ₹{$withdrawable}, Requested: ₹{$amount}."
            );
        }

        // Check for existing pending request (prevent duplicate)
        $hasPending = Withdrawal::where('astrologer_id', $astrologer->id)
                                ->whereIn('status', [
                                    Withdrawal::STATUS_PENDING,
                                    Withdrawal::STATUS_UNDER_REVIEW,
                                    Withdrawal::STATUS_APPROVED,
                                    Withdrawal::STATUS_PROCESSING,
                                ])
                                ->exists();

        if ($hasPending) {
            throw new \RuntimeException(
                'You already have a pending withdrawal request. Please wait for it to be processed.'
            );
        }

        // Calculate fee
        [$fee, $netAmount] = $this->calculateFee($amount);

        return DB::transaction(function () use (
            $astrologer, $wallet, $data, $amount, $fee, $netAmount
        ) {
            // Prepare payment details
            $paymentMethod = $data['payment_method'];
            $upiId         = $data['upi_id'] ?? null;

            // Mask bank account number
            $rawAccount     = $data['account_number'] ?? null;
            $maskedAccount  = null;
            $encryptedAccnt = null;

            if ($rawAccount) {
                $maskedAccount  = 'XXXXXX' . substr($rawAccount, -4);
                // In production: encrypt with app key; here we store masked only
                $encryptedAccnt = encrypt($rawAccount);
            }

            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'astrologer_id'           => $astrologer->id,
                'wallet_id'               => $wallet->id,
                'amount'                  => $amount,
                'fee'                     => $fee,
                'net_amount'              => $netAmount,
                'payment_method'          => $paymentMethod,
                'upi_id'                  => $upiId,
                'account_holder_name'     => $data['account_holder_name'] ?? null,
                'account_number_masked'   => $maskedAccount,
                'account_number_encrypted'=> $encryptedAccnt,
                'bank_name'               => $data['bank_name'] ?? null,
                'ifsc_code'               => isset($data['ifsc_code']) ? strtoupper($data['ifsc_code']) : null,
                'status'                  => Withdrawal::STATUS_PENDING,
                'requested_at'            => now(),
            ]);

            // Hold funds in wallet
            $this->walletService->hold(
                $wallet,
                $amount,
                'WITHDRAWAL-' . $withdrawal->id,
                "Withdrawal request #{$withdrawal->withdrawal_number}"
            );

            // Audit log
            AuditLog::record(
                AuditLog::WITHDRAWAL_REQUESTED,
                $withdrawal,
                [],
                ['amount' => $amount, 'method' => $paymentMethod]
            );

            return $withdrawal;
        });
    }

    // -------------------------------------------------------------------------
    // Admin Actions
    // -------------------------------------------------------------------------

    /**
     * Admin approves a withdrawal request.
     */
    public function approve(Withdrawal $withdrawal, int $adminId, ?string $notes = null): void
    {
        $this->assertStatus($withdrawal, [
            Withdrawal::STATUS_PENDING,
            Withdrawal::STATUS_UNDER_REVIEW,
        ]);

        DB::transaction(function () use ($withdrawal, $adminId, $notes) {
            $withdrawal->update([
                'status'      => Withdrawal::STATUS_APPROVED,
                'approved_at' => now(),
                'admin_id'    => $adminId,
                'admin_notes' => $notes,
            ]);

            AuditLog::record(
                AuditLog::WITHDRAWAL_APPROVED,
                $withdrawal,
                ['status' => 'pending'],
                ['status' => 'approved'],
                null,
                $adminId,
                'Admin'
            );
        });
    }

    /**
     * Admin marks withdrawal as processing.
     */
    public function markProcessing(Withdrawal $withdrawal, int $adminId): void
    {
        $this->assertStatus($withdrawal, [Withdrawal::STATUS_APPROVED]);

        $withdrawal->update([
            'status'   => Withdrawal::STATUS_PROCESSING,
            'admin_id' => $adminId,
        ]);
    }

    /**
     * Admin marks withdrawal as paid (payout complete).
     *
     * @param  string $transactionReference  UTR / bank reference number
     */
    public function markPaid(
        Withdrawal $withdrawal,
        int        $adminId,
        string     $transactionReference
    ): void {
        $this->assertStatus($withdrawal, [
            Withdrawal::STATUS_APPROVED,
            Withdrawal::STATUS_PROCESSING,
        ]);

        DB::transaction(function () use ($withdrawal, $adminId, $transactionReference) {
            $wallet = $withdrawal->wallet;

            // Finalise the held debit in the ledger
            $this->walletService->finaliseWithdrawal(
                $wallet,
                (float) $withdrawal->amount,
                'WITHDRAWAL-' . $withdrawal->id,
                "Withdrawal #{$withdrawal->withdrawal_number} paid",
                $adminId
            );

            $withdrawal->update([
                'status'                  => Withdrawal::STATUS_PAID,
                'processed_at'            => now(),
                'admin_id'                => $adminId,
                'transaction_reference'   => $transactionReference,
            ]);

            AuditLog::record(
                AuditLog::WITHDRAWAL_PAID,
                $withdrawal,
                ['status' => 'processing'],
                ['status' => 'paid', 'utr' => $transactionReference],
                null,
                $adminId,
                'Admin'
            );
        });
    }

    /**
     * Admin rejects a withdrawal request and releases the held funds.
     */
    public function reject(
        Withdrawal $withdrawal,
        int        $adminId,
        string     $reason
    ): void {
        $this->assertStatus($withdrawal, [
            Withdrawal::STATUS_PENDING,
            Withdrawal::STATUS_UNDER_REVIEW,
            Withdrawal::STATUS_APPROVED,
        ]);

        DB::transaction(function () use ($withdrawal, $adminId, $reason) {
            $wallet = $withdrawal->wallet;

            // Release held funds
            $this->walletService->release(
                $wallet,
                (float) $withdrawal->amount,
                'WITHDRAWAL-' . $withdrawal->id,
                "Withdrawal #{$withdrawal->withdrawal_number} rejected: {$reason}",
                $adminId
            );

            $withdrawal->update([
                'status'           => Withdrawal::STATUS_REJECTED,
                'rejected_at'      => now(),
                'admin_id'         => $adminId,
                'rejection_reason' => $reason,
            ]);

            AuditLog::record(
                AuditLog::WITHDRAWAL_REJECTED,
                $withdrawal,
                ['status' => 'pending'],
                ['status' => 'rejected', 'reason' => $reason],
                null,
                $adminId,
                'Admin'
            );
        });
    }

    // -------------------------------------------------------------------------
    // Astrologer Cancel
    // -------------------------------------------------------------------------

    /**
     * Astrologer cancels their own pending withdrawal request.
     */
    public function cancel(Withdrawal $withdrawal): void
    {
        $this->assertStatus($withdrawal, [Withdrawal::STATUS_PENDING]);

        DB::transaction(function () use ($withdrawal) {
            $wallet = $withdrawal->wallet;

            $this->walletService->release(
                $wallet,
                (float) $withdrawal->amount,
                'WITHDRAWAL-' . $withdrawal->id,
                "Withdrawal #{$withdrawal->withdrawal_number} cancelled by astrologer"
            );

            $withdrawal->update([
                'status'       => Withdrawal::STATUS_CANCELLED,
                'cancelled_at' => now(),
            ]);
        });
    }

    // -------------------------------------------------------------------------
    // Fee Calculation
    // -------------------------------------------------------------------------

    /**
     * Calculate the withdrawal fee based on admin settings.
     *
     * @return array{float, float}  [fee, net_amount]
     */
    public function calculateFee(float $amount): array
    {
        $feeType  = Setting::get(Setting::WITHDRAWAL_FEE_TYPE, 'percentage');
        $feeValue = (float) Setting::get(Setting::WITHDRAWAL_FEE_VALUE, 0);

        if ($feeValue <= 0) {
            return [0.0, $amount];
        }

        $fee = match ($feeType) {
            'percentage' => round($amount * $feeValue / 100, 2),
            'fixed'      => round($feeValue, 2),
            default      => 0.0,
        };

        $netAmount = round($amount - $fee, 2);

        return [$fee, $netAmount];
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    /**
     * Assert that the withdrawal is in one of the allowed statuses.
     *
     * @throws \RuntimeException
     */
    private function assertStatus(Withdrawal $withdrawal, array $allowedStatuses): void
    {
        if (!in_array($withdrawal->status, $allowedStatuses, true)) {
            throw new \RuntimeException(
                "Cannot perform this action on a withdrawal with status '{$withdrawal->status}'."
            );
        }
    }
}
