<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order_value', 'max_discount',
        'usage_limit', 'used_count', 'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'value'           => 'float',
        'min_order_value' => 'float',
        'max_discount'    => 'float',
        'is_active'       => 'boolean',
        'valid_from'      => 'date',
        'valid_until'     => 'date',
    ];

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // -------------------------------------------------------------------------
    // Business Logic
    // -------------------------------------------------------------------------

    /**
     * Check if the coupon is valid for the given cart subtotal.
     */
    public function isValidFor(float $cartSubtotal): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Date validity
        $today = Carbon::today();
        if ($this->valid_from && $today->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_until && $today->gt($this->valid_until)) {
            return false;
        }

        // Usage limit
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        // Minimum order
        if ($cartSubtotal < $this->min_order_value) {
            return false;
        }

        return true;
    }

    /**
     * Compute the discount amount for a given subtotal.
     */
    public function computeDiscount(float $subtotal): float
    {
        if ($this->type === 'percent') {
            $discount = $subtotal * ($this->value / 100);
            if ($this->max_discount !== null) {
                $discount = min($discount, $this->max_discount);
            }
            return round($discount, 2);
        }

        // Flat
        return min($this->value, $subtotal);
    }
}
