<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Wallet — one per astrologer.
 * All balance changes MUST go through WalletService to keep ledger in sync.
 */
class Wallet extends Model
{
    protected $fillable = [
        'astrologer_id',
        'available_balance',
        'pending_balance',
        'held_balance',
        'lifetime_earnings',
        'lifetime_withdrawn',
        'currency',
        'status',
    ];

    protected $casts = [
        'available_balance'  => 'decimal:2',
        'pending_balance'    => 'decimal:2',
        'held_balance'       => 'decimal:2',
        'lifetime_earnings'  => 'decimal:2',
        'lifetime_withdrawn' => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class)->latest();
    }

    // -------------------------------------------------------------------------
    // Computed Helpers
    // -------------------------------------------------------------------------

    /**
     * Total balance the astrologer sees (available + pending).
     */
    public function getTotalBalanceAttribute(): float
    {
        return round((float) $this->available_balance + (float) $this->pending_balance, 2);
    }

    /**
     * Withdrawable balance = available minus currently held amounts.
     */
    public function getWithdrawableAttribute(): float
    {
        return max(0, round((float) $this->available_balance - (float) $this->held_balance, 2));
    }

    /**
     * Formatted available balance with ₹ symbol.
     */
    public function getFormattedAvailableAttribute(): string
    {
        return '₹' . number_format((float) $this->available_balance, 2);
    }

    public function getFormattedPendingAttribute(): string
    {
        return '₹' . number_format((float) $this->pending_balance, 2);
    }
}
