<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Withdrawal — request by an astrologer to withdraw available wallet funds.
 */
class Withdrawal extends Model
{
    protected $fillable = [
        'withdrawal_number',
        'astrologer_id',
        'wallet_id',
        'amount',
        'fee',
        'net_amount',
        'payment_method',
        'upi_id',
        'account_holder_name',
        'account_number_masked',
        'account_number_encrypted',
        'bank_name',
        'ifsc_code',
        'status',
        'requested_at',
        'approved_at',
        'processed_at',
        'rejected_at',
        'cancelled_at',
        'rejection_reason',
        'transaction_reference',
        'admin_id',
        'admin_notes',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'fee'          => 'decimal:2',
        'net_amount'   => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at'  => 'datetime',
        'processed_at' => 'datetime',
        'rejected_at'  => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Status Constants
    // -------------------------------------------------------------------------

    public const STATUS_PENDING      = 'pending';
    public const STATUS_UNDER_REVIEW = 'under_review';
    public const STATUS_APPROVED     = 'approved';
    public const STATUS_PROCESSING   = 'processing';
    public const STATUS_PAID         = 'paid';
    public const STATUS_REJECTED     = 'rejected';
    public const STATUS_CANCELLED    = 'cancelled';
    public const STATUS_FAILED       = 'failed';

    // -------------------------------------------------------------------------
    // Boot — auto-generate withdrawal number
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Withdrawal $withdrawal) {
            if (empty($withdrawal->withdrawal_number)) {
                $withdrawal->withdrawal_number = 'WD-'
                    . now()->format('Ymd')
                    . '-'
                    . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
            }
            $withdrawal->requested_at = $withdrawal->requested_at ?? now();
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Whether the admin can still act on this withdrawal.
     */
    public function isPending(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_APPROVED,
            self::STATUS_PROCESSING,
        ], true);
    }

    /**
     * Whether funds held for this withdrawal should be released back.
     */
    public function shouldReleaseHold(): bool
    {
        return in_array($this->status, [
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
            self::STATUS_FAILED,
        ], true);
    }

    /**
     * Status badge for UI.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING      => ['label' => 'Pending',      'class' => 'warning text-dark'],
            self::STATUS_UNDER_REVIEW => ['label' => 'Under Review', 'class' => 'info'],
            self::STATUS_APPROVED     => ['label' => 'Approved',     'class' => 'primary'],
            self::STATUS_PROCESSING   => ['label' => 'Processing',   'class' => 'primary'],
            self::STATUS_PAID         => ['label' => 'Paid',         'class' => 'success'],
            self::STATUS_REJECTED     => ['label' => 'Rejected',     'class' => 'danger'],
            self::STATUS_CANCELLED    => ['label' => 'Cancelled',    'class' => 'secondary'],
            self::STATUS_FAILED       => ['label' => 'Failed',       'class' => 'danger'],
            default                   => ['label' => ucfirst($this->status), 'class' => 'secondary'],
        };
    }

    /**
     * Masked UPI: pr****@upi
     */
    public function getMaskedUpiAttribute(): ?string
    {
        if (!$this->upi_id) {
            return null;
        }
        $parts = explode('@', $this->upi_id, 2);
        $prefix = substr($parts[0], 0, 2);
        return $prefix . '****@' . ($parts[1] ?? 'upi');
    }

    /**
     * Alias: upi_id_masked (used in views)
     */
    public function getUpiIdMaskedAttribute(): ?string
    {
        return $this->masked_upi;
    }

    /**
     * Decrypted account number (admin only — guard access in views).
     */
    public function getAccountNumberAttribute(): ?string
    {
        if (!$this->account_number_encrypted) {
            return $this->account_number_masked;
        }
        try {
            return decrypt($this->account_number_encrypted);
        } catch (\Throwable) {
            return $this->account_number_masked;
        }
    }

    /**
     * All valid statuses — used by AdminWithdrawalController filter.
     */
    public static function allStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_UNDER_REVIEW,
            self::STATUS_APPROVED,
            self::STATUS_PROCESSING,
            self::STATUS_PAID,
            self::STATUS_REJECTED,
            self::STATUS_CANCELLED,
            self::STATUS_FAILED,
        ];
    }
}
