<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id',
        'product_name', 'product_sku',
        'quantity', 'unit_price', 'original_price', 'subtotal',
    ];

    protected $casts = [
        'unit_price'     => 'float',
        'original_price' => 'float',
        'subtotal'       => 'float',
        'quantity'       => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getIsDiscountedAttribute(): bool
    {
        return $this->unit_price < $this->original_price;
    }

    public function getSavingsAttribute(): float
    {
        return max(0, ($this->original_price - $this->unit_price) * $this->quantity);
    }
}
