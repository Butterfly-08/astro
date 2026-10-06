<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'quantity',
        'unit_price',
        'original_price',
        'subtotal',
    ];

    protected $casts = [
        'order_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
        'unit_price' => 'float',
        'original_price' => 'float',
        'subtotal' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getPriceAttribute(): float
    {
        return (float) $this->unit_price;
    }

    public function getVariantNameAttribute(): ?string
    {
        return null;
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