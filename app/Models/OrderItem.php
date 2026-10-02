<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'order_id'           => 'integer',
        'product_variant_id' => 'integer',
        'quantity'           => 'integer',
        'price'              => 'float',
        'subtotal'           => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getIsDiscountedAttribute(): bool
    {
        return false;
    }

    public function getSavingsAttribute(): float
    {
        return 0.0;
    }

    public function getLineTotalAttribute(): float
    {
        return (float) $this->subtotal;
    }
}