<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Referral – tracks every referral link click and eventual conversion.
 */
class Referral extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'astrologer_id',
        'referral_code',
        'customer_id',
        'session_id',
        'product_id',
        'order_id',
        'clicked_at',
        'converted_at',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'clicked_at'   => 'datetime',
        'converted_at' => 'datetime',
    ];

    // -------------------------------------------------------------------------
    // Status Constants
    // -------------------------------------------------------------------------

    public const STATUS_CLICKED   = 'clicked';
    public const STATUS_CONVERTED = 'converted';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_EXPIRED   = 'expired';
    public const STATUS_INVALID   = 'invalid';

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

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeConverted($query)
    {
        return $query->where('status', self::STATUS_CONVERTED);
    }

    public function scopeForAstrologer($query, int $astrologerId)
    {
        return $query->where('astrologer_id', $astrologerId);
    }
}
