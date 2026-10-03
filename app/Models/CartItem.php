<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer',
        'quantity' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Computed Accessors
    // -------------------------------------------------------------------------

    public function getPriceAttribute(): float
    {
        return (float) ($this->product?->effective_price ?? 0);
    }

    /**
     * Line-item subtotal
     * Stored cart price × quantity
     */
    public function getLineTotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
}
