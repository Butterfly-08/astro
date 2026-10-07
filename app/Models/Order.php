<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'coupon_code',
        'coupon_discount',
        'subtotal',
        'shipping_charge',
        'tax_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'payment_id',
        'shipping_name',
        'shipping_phone',
        'shipping_address_line1',
        'shipping_address_line2',
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'shipping_country',
        'tracking_number',
        'carrier_name',
        'estimated_delivery',
        'delivered_at',
        'admin_notes',
        'referral_code',
        'referrer_astrologer_id',
        'commission_status',
    ];

    protected $casts = [
        'coupon_discount' => 'float',
        'subtotal' => 'float',
        'shipping_charge' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
        'estimated_delivery' => 'date',
        'delivered_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (!$order->order_number) {
                $order->order_number = 'ORD-'
                    . now()->format('Ymd')
                    . '-'
                    . Str::upper(Str::random(8));
            }
        });
    }

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

    public function referrerAstrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class, 'referrer_astrologer_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    // -------------------------------------------------------------------------
    // Status constants
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

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => [
                'label' => 'Pending',
                'class' => 'warning text-dark',
            ],

            'confirmed' => [
                'label' => 'Confirmed',
                'class' => 'info text-dark',
            ],

            'processing' => [
                'label' => 'Processing',
                'class' => 'primary',
            ],

            'shipped' => [
                'label' => 'Shipped',
                'class' => 'primary',
            ],

            'delivered' => [
                'label' => 'Delivered',
                'class' => 'success',
            ],

            'cancelled' => [
                'label' => 'Cancelled',
                'class' => 'danger',
            ],

            'refunded' => [
                'label' => 'Refunded',
                'class' => 'secondary',
            ],

            default => [
                'label' => ucfirst($this->status ?? 'Unknown'),
                'class' => 'secondary',
            ],
        };
    }

    public function getPaymentStatusBadgeAttribute(): array
    {
        return match ($this->payment_status) {
            'unpaid' => [
                'label' => 'Unpaid',
                'class' => 'warning text-dark',
            ],

            'paid' => [
                'label' => 'Paid',
                'class' => 'success',
            ],

            'failed' => [
                'label' => 'Failed',
                'class' => 'danger',
            ],

            'refunded' => [
                'label' => 'Refunded',
                'class' => 'info text-dark',
            ],

            default => [
                'label' => ucfirst($this->payment_status ?? 'Unknown'),
                'class' => 'secondary',
            ],
        };
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->total_amount;
    }

    public function getCustomerNameAttribute(): ?string
    {
        return $this->shipping_name;
    }

    public function getCustomerEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function getCustomerPhoneAttribute(): ?string
    {
        return $this->shipping_phone;
    }

    public function getShippingAddressAttribute(): string
    {
        return implode("\n", array_filter([
            $this->shipping_address_line1,
            $this->shipping_address_line2,
            trim(implode(', ', array_filter([
                $this->shipping_city,
                $this->shipping_state,
            ])) . ' - ' . $this->shipping_pincode),
            $this->shipping_country,
        ], static fn ($line) => $line !== null && $line !== ''));
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    public function isCancellable(): bool
    {
        return in_array(
            $this->status,
            [
                self::STATUS_PENDING,
                self::STATUS_CONFIRMED,
            ],
            true
        );
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
