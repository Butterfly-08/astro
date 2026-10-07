<?php

namespace App\Services;

use App\Models\Astrologer;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

/**
 * WalletService — all wallet balance operations go through here.
 *
 * RULES:
 * 1. NEVER update wallet balance directly; always use this service.
 * 2. Every balance change creates a corresponding WalletTransaction (ledger entry).
 * 3. All operations run inside DB::transaction.
 * 4. Use lockForUpdate() to prevent concurrent modification.
 */
class WalletService
{
    // -------------------------------------------------------------------------
    // Get or Create Wallet
    // -------------------------------------------------------------------------

    /**
     * Get the wallet for an astrologer, creating one if it doesn't exist.
     */
    public function getOrCreate(Astrologer $astrologer): Wallet
    {
        return Wallet::firstOrCreate(
            ['astrologer_id' => $astrologer->id],
            [
                'available_balance'  => 0,
                'pending_balance'    => 0,
                'held_balance'       => 0,
                'lifetime_earnings'  => 0,
                'lifetime_withdrawn' => 0,
                'currency'           => 'INR',
                'status'             => 'active',
            ]
        );
    }

    // -------------------------------------------------------------------------
    // Credit (Available Balance)
    // -------------------------------------------------------------------------

    /**
     * Credit the available balance.
     * Used for manual bonuses / admin adjustments.
     *
     * @param  Wallet $wallet
     * @param  float  $amount
     * @param  string $type       WalletTransaction type constant
     * @param  string $refKey     Unique reference key (prevents double-credit)
     * @param  string $description
     * @param  mixed  $refType    Model class name (nullable)
     * @param  mixed  $refId      Model ID (nullable)
     * @param  int|null $adminId
     * @throws \RuntimeException if reference key already used
     */
    public function credit(
        Wallet  $wallet,
        float   $amount,
        string  $type,
        string  $refKey,
        string  $description = '',
        ?string $refType  = null,
        ?int    $refId    = null,
        ?int    $adminId  = null
    ): WalletTransaction {
        return DB::transaction(function () use (
            $wallet, $amount, $type, $refKey, $description, $refType, $refId, $adminId
        ) {
            // Idempotency guard
            if (WalletTransaction::where('reference_key', $refKey)->exists()) {
                throw new \RuntimeException("Duplicate wallet transaction: {$refKey}");
            }

            /** @var Wallet $locked */
            $locked = Wallet::lockForUpdate()->find($wallet->id);

            $balanceBefore = (float) $locked->available_balance;
            $locked->increment('available_balance', $amount);
            $locked->increment('lifetime_earnings', $amount);

            return WalletTransaction::create([
                'wallet_id'      => $locked->id,
                'astrologer_id'  => $locked->astrologer_id,
                'type'           => $type,
                'reference_type' => $refType,
                'reference_id'   => $refId,
                'reference_key'  => $refKey,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => $description,
                'status'         => 'completed',
                'admin_id'       => $adminId,
            ]);
        });
    }

    // -------------------------------------------------------------------------
    // Hold (lock funds for a pending withdrawal)
    // -------------------------------------------------------------------------

    /**
     * Move funds from available to held.
     * Called when a withdrawal request is created.
     *
     * @throws \RuntimeException if insufficient available balance
     */
    public function hold(
        Wallet $wallet,
        float  $amount,
        string $refKey,
        string $description = ''
    ): WalletTransaction {
        return DB::transaction(function () use ($wallet, $amount, $refKey, $description) {
            /** @var Wallet $locked */
            $locked = Wallet::lockForUpdate()->find($wallet->id);

            $available = (float) $locked->available_balance;

            if ($available < $amount) {
                throw new \RuntimeException(
                    "Insufficient available balance. Available: ₹{$available}, Requested: ₹{$amount}"
                );
            }

            if (WalletTransaction::where('reference_key', $refKey)->exists()) {
                throw new \RuntimeException("Duplicate wallet transaction: {$refKey}");
            }

            $locked->decrement('available_balance', $amount);
            $locked->increment('held_balance', $amount);

            return WalletTransaction::create([
                'wallet_id'      => $locked->id,
                'astrologer_id'  => $locked->astrologer_id,
                'type'           => WalletTransaction::TYPE_WITHDRAWAL_HOLD,
                'reference_key'  => $refKey,
                'amount'         => $amount,
                'balance_before' => $available,
                'balance_after'  => $available - $amount,
                'description'    => $description ?: "Withdrawal hold: {$refKey}",
                'status'         => 'completed',
            ]);
        });
    }

    // -------------------------------------------------------------------------
    // Release (return held funds — withdrawal rejected/cancelled)
    // -------------------------------------------------------------------------

    /**
     * Release held funds back to available balance.
     */
    public function release(
        Wallet $wallet,
        float  $amount,
        string $refKey,
        string $description = '',
        ?int   $adminId = null
    ): WalletTransaction {
        return DB::transaction(function () use ($wallet, $amount, $refKey, $description, $adminId) {
            $releaseKey = 'RELEASE-' . $refKey;

            if (WalletTransaction::where('reference_key', $releaseKey)->exists()) {
                throw new \RuntimeException("Duplicate release transaction: {$releaseKey}");
            }

            /** @var Wallet $locked */
            $locked = Wallet::lockForUpdate()->find($wallet->id);

            $balanceBefore = (float) $locked->available_balance;
            $locked->increment('available_balance', $amount);
            $locked->decrement('held_balance', $amount);

            return WalletTransaction::create([
                'wallet_id'      => $locked->id,
                'astrologer_id'  => $locked->astrologer_id,
                'type'           => WalletTransaction::TYPE_WITHDRAWAL_RELEASE,
                'reference_key'  => $releaseKey,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => $description ?: "Hold released: {$refKey}",
                'status'         => 'completed',
                'admin_id'       => $adminId,
            ]);
        });
    }

    // -------------------------------------------------------------------------
    // Finalise (debit held funds when withdrawal is paid)
    // -------------------------------------------------------------------------

    /**
     * Finalise a paid withdrawal — debit from held balance permanently.
     */
    public function finaliseWithdrawal(
        Wallet $wallet,
        float  $amount,
        string $refKey,
        string $description = '',
        ?int   $adminId = null
    ): WalletTransaction {
        return DB::transaction(function () use ($wallet, $amount, $refKey, $description, $adminId) {
            $paidKey = 'PAID-' . $refKey;

            if (WalletTransaction::where('reference_key', $paidKey)->exists()) {
                throw new \RuntimeException("Duplicate paid transaction: {$paidKey}");
            }

            /** @var Wallet $locked */
            $locked = Wallet::lockForUpdate()->find($wallet->id);

            $balanceBefore = (float) $locked->available_balance;
            $locked->decrement('held_balance', $amount);
            $locked->increment('lifetime_withdrawn', $amount);

            return WalletTransaction::create([
                'wallet_id'      => $locked->id,
                'astrologer_id'  => $locked->astrologer_id,
                'type'           => WalletTransaction::TYPE_WITHDRAWAL_PAID,
                'reference_key'  => $paidKey,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore,  // available didn't change; held decreased
                'description'    => $description ?: "Withdrawal paid: {$refKey}",
                'status'         => 'completed',
                'admin_id'       => $adminId,
            ]);
        });
    }

    // -------------------------------------------------------------------------
    // Admin Manual Adjustment
    // -------------------------------------------------------------------------

    /**
     * Admin adds or subtracts from the available balance.
     *
     * @param  float  $amount  Positive = credit, negative = debit
     */
    public function adminAdjust(
        Wallet $wallet,
        float  $amount,
        string $reason,
        int    $adminId
    ): WalletTransaction {
        $refKey = 'ADMIN-ADJ-' . $wallet->id . '-' . now()->timestamp;

        if ($amount > 0) {
            return $this->credit(
                $wallet,
                $amount,
                WalletTransaction::TYPE_MANUAL_ADJUSTMENT,
                $refKey,
                $reason,
                null,
                null,
                $adminId
            );
        }

        // Debit: treat like a hold-then-finalise in one step
        return DB::transaction(function () use ($wallet, $amount, $reason, $adminId, $refKey) {
            /** @var Wallet $locked */
            $locked = Wallet::lockForUpdate()->find($wallet->id);

            $debit         = abs($amount);
            $balanceBefore = (float) $locked->available_balance;

            if ($balanceBefore < $debit) {
                throw new \RuntimeException('Insufficient balance for admin debit adjustment.');
            }

            $locked->decrement('available_balance', $debit);

            return WalletTransaction::create([
                'wallet_id'      => $locked->id,
                'astrologer_id'  => $locked->astrologer_id,
                'type'           => WalletTransaction::TYPE_MANUAL_ADJUSTMENT,
                'reference_key'  => $refKey,
                'amount'         => $debit,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore - $debit,
                'description'    => $reason,
                'status'         => 'completed',
                'admin_id'       => $adminId,
            ]);
        });
    }
}
