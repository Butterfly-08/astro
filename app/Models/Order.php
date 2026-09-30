<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id',
        'coupon_code', 'coupon_discount',
        'subtotal', 'shipping_charge', 'tax_amount', 'total_amount',
        'status', 'payment_method', 'payment_status', 'payment_id',
        'shipping_name', 'shipping_phone',
        'shipping_address_line1', 'shipping_address_line2',
        'shipping_city', 'shipping_state', 'shipping_pincode', 'shipping_country',
        'tracking_number', 'carrier_name', 'estimated_delivery', 'delivered_at',
        'admin_notes',
    ];

    protected $casts = [
        'subtotal'         => 'float',
        'shipping_charge'  => 'float',
        'tax_amount'       => 'float',
        'total_amount'     => 'float',
        'coupon_discount'  => 'float',
        'estimated_delivery' => 'date',
        'delivered_at'     => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // -------------------------------------------------------------------------
    // Status Helpers
    // -------------------------------------------------------------------------

    public const STATUS_PENDING    = 'pending';
    public const STATUS_CONFIRMED  = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_DELIVERED  = 'delivered';
    public const STATUS_CANCELLED  = 'cancelled';
    public const STATUS_REFUNDED   = 'refunded';

    public static function allStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PROCESSING,
            self::STATUS_SHIPPED,
            self::STATUS_DELIVERED,
            self::STATUS_CANCELLED,
            self::STATUS_REFUNDED,
        ];
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending'    => ['label' => 'Pending',    'class' => 'warning text-dark'],
            'confirmed'  => ['label' => 'Confirmed',  'class' => 'info text-dark'],
            'processing' => ['label' => 'Processing', 'class' => 'primary'],
            'shipped'    => ['label' => 'Shipped',    'class' => 'primary'],
            'delivered'  => ['label' => 'Delivered',  'class' => 'success'],
            'cancelled'  => ['label' => 'Cancelled',  'class' => 'danger'],
            'refunded'   => ['label' => 'Refunded',   'class' => 'secondary'],
            default      => ['label' => 'Unknown',    'class' => 'secondary'],
        };
    }

    public function getPaymentStatusBadgeAttribute(): array
    {
        return match ($this->payment_status) {
            'paid'     => ['label' => 'Paid',     'class' => 'success'],
            'unpaid'   => ['label' => 'Unpaid',   'class' => 'warning text-dark'],
            'failed'   => ['label' => 'Failed',   'class' => 'danger'],
            'refunded' => ['label' => 'Refunded', 'class' => 'secondary'],
            default    => ['label' => 'Unknown',  'class' => 'secondary'],
        };
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // -------------------------------------------------------------------------
    // Boot: auto-generate order number
    // -------------------------------------------------------------------------

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                do {
                    $number = 'AV-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
                } while (static::where('order_number', $number)->exists());
                $order->order_number = $number;
            }
        });
    }
}
