<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Commission — one record per order_item when a referral is present.
 * Financial amounts stored as DECIMAL(12,2) — never float.
 */
class Commission extends Model
{
    protected $fillable = [
        'commission_number',
        'astrologer_id',
        'customer_id',
        'order_id',
        'order_item_id',
        'product_id',
        'referral_id',
        'commission_type',
        'commission_rate',
        'order_amount',
        'commission_amount',
        'status',
        'available_at',
        'approved_at',
        'rejected_at',
        'rejection_reason',
        'action_by',
    ];

    protected $casts = [
        'commission_rate'   => 'decimal:2',
        'order_amount'      => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'available_at'      => 'datetime',
        'approved_at'       => 'datetime',
        'rejected_at'       => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Status Constants
    // -------------------------------------------------------------------------

    public const STATUS_PENDING   = 'pending';
    public const STATUS_APPROVED  = 'approved';
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_REJECTED  = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REVERSED  = 'cancelled';
    public const STATUS_PAID      = 'paid';

    // -------------------------------------------------------------------------
    // Boot — auto-generate commission number
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Commission $commission) {
            if (empty($commission->commission_number)) {
                $commission->commission_number = 'COM-'
                    . now()->format('Ymd')
                    . '-'
                    . str_pad(self::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function actionBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'action_by');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_AVAILABLE);
    }

    public function scopeForAstrologer($query, int $astrologerId)
    {
        return $query->where('astrologer_id', $astrologerId);
    }

    public function scopeEligibleForRelease($query)
    {
        return $query->where('status', self::STATUS_PENDING)
                     ->where('available_at', '<=', now());
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Determine if this commission can be withdrawn.
     */
    public function isWithdrawable(): bool
    {
        return $this->status === self::STATUS_AVAILABLE;
    }

    /**
     * Status badge config for UI.
     */
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING   => ['label' => 'Pending',   'class' => 'warning text-dark'],
            self::STATUS_APPROVED  => ['label' => 'Approved',  'class' => 'info'],
            self::STATUS_AVAILABLE => ['label' => 'Available', 'class' => 'success'],
            self::STATUS_REJECTED  => ['label' => 'Rejected',  'class' => 'danger'],
            self::STATUS_CANCELLED => ['label' => 'Cancelled', 'class' => 'secondary'],
            self::STATUS_PAID      => ['label' => 'Paid',      'class' => 'success'],
            default                => ['label' => ucfirst($this->status), 'class' => 'secondary'],
        };
    }
}
