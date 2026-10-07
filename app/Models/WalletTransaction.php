<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * WalletTransaction — the immutable financial ledger.
 * Records every credit, debit, and adjustment with before/after balance snapshot.
 */
class WalletTransaction extends Model
{
    protected $fillable = [
        'wallet_id',
        'astrologer_id',
        'type',
        'reference_type',
        'reference_id',
        'reference_key',
        'amount',
        'balance_before',
        'balance_after',
        'description',
        'status',
        'admin_id',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after'  => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Type Constants
    // -------------------------------------------------------------------------

    public const TYPE_COMMISSION_CREDIT   = 'commission_credit';
    public const TYPE_COMMISSION_REVERSAL = 'commission_reversal';
    public const TYPE_WITHDRAWAL_HOLD     = 'withdrawal_hold';
    public const TYPE_WITHDRAWAL_RELEASE  = 'withdrawal_release';
    public const TYPE_WITHDRAWAL_PAID     = 'withdrawal_paid';
    public const TYPE_BONUS_CREDIT        = 'bonus_credit';
    public const TYPE_MANUAL_ADJUSTMENT   = 'manual_adjustment';
    public const TYPE_REFUND_ADJUSTMENT   = 'refund_adjustment';

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Is this a credit (money added to available balance)?
     */
    public function isCredit(): bool
    {
        return in_array($this->type, [
            self::TYPE_COMMISSION_CREDIT,
            self::TYPE_BONUS_CREDIT,
            self::TYPE_WITHDRAWAL_RELEASE,
        ], true);
    }

    /**
     * Badge data for UI display.
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_COMMISSION_CREDIT   => 'Commission Credit',
            self::TYPE_COMMISSION_REVERSAL => 'Commission Reversal',
            self::TYPE_WITHDRAWAL_HOLD     => 'Withdrawal Hold',
            self::TYPE_WITHDRAWAL_RELEASE  => 'Withdrawal Released',
            self::TYPE_WITHDRAWAL_PAID     => 'Withdrawal Paid',
            self::TYPE_BONUS_CREDIT        => 'Bonus Credit',
            self::TYPE_MANUAL_ADJUSTMENT   => 'Manual Adjustment',
            self::TYPE_REFUND_ADJUSTMENT   => 'Refund Adjustment',
            default                        => ucwords(str_replace('_', ' ', $this->type)),
        };
    }
}
