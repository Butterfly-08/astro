<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $display_name
 */
class Astrologer extends Model
{
    /** @use HasFactory<\Database\Factories\AstrologerFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'email',
        'phone',
        'slug',
        'profile_image',
        'bio',
        'short_bio',
        'specializations',
        'languages',
        'experience_years',
        'education',
        'chat_rate',
        'call_rate',
        'video_rate',
        'rating_avg',
        'total_reviews',
        'total_consultations',
        'status',
        'approval_status',
        'suspension_reason',
        'referral_code',
        'is_featured',
        'is_available',
        'rejection_reason',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'chat_rate'           => 'decimal:2',
        'call_rate'           => 'decimal:2',
        'video_rate'          => 'decimal:2',
        'rating_avg'          => 'decimal:2',
        'is_featured'         => 'boolean',
        'is_available'        => 'boolean',
        'approved_at'         => 'datetime',
        'experience_years'    => 'integer',
        'total_reviews'       => 'integer',
        'total_consultations' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Approval Status Constants
    // -------------------------------------------------------------------------

    public const APPROVAL_PENDING   = 'pending';
    public const APPROVAL_APPROVED  = 'approved';
    public const APPROVAL_REJECTED  = 'rejected';
    public const APPROVAL_SUSPENDED = 'suspended';

    // -------------------------------------------------------------------------
    // Accessors / Helpers
    // -------------------------------------------------------------------------

    public function getSpecializationsArrayAttribute(): array
    {
        return $this->specializations
            ? array_map('trim', explode(',', $this->specializations))
            : [];
    }

    public function getLanguagesArrayAttribute(): array
    {
        return $this->languages
            ? array_map('trim', explode(',', $this->languages))
            : [];
    }

    public function getProfileUrlAttribute(): string
    {
        return route('astrologers.show', $this->slug);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : asset('images/default-astrologer.png');
    }

    /**
     * Generic referral landing page URL.
     */
    public function getReferralUrlAttribute(): string
    {
        if (!$this->referral_code) {
            return '#';
        }
        return url('/ref/' . $this->referral_code);
    }

    /**
     * Product-specific referral URL.
     */
    public function productReferralUrl(Product $product): string
    {
        return url('/shop/product/' . $product->slug . '?ref=' . $this->referral_code);
    }

    /**
     * Can this astrologer earn new commissions right now?
     */
    public function isActiveForReferral(): bool
    {
        return $this->approval_status === self::APPROVAL_APPROVED
            && $this->status === 'active';
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', self::APPROVAL_APPROVED);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('display_name', 'like', "%{$term}%")
              ->orWhere('specializations', 'like', "%{$term}%")
              ->orWhere('languages', 'like', "%{$term}%")
              ->orWhere('short_bio', 'like', "%{$term}%");
        });
    }

    public function scopeByService(Builder $query, int $serviceId): Builder
    {
        return $query->whereHas('services', fn($q) => $q->where('services.id', $serviceId));
    }

    public function scopeByLanguage(Builder $query, string $language): Builder
    {
        return $query->where('languages', 'like', "%{$language}%");
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'astrologer_service')
                    ->withTimestamps();
    }

    public function availability(): HasMany
    {
        return $this->hasMany(AstrologerAvailability::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AstrologerReview::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class)->latest();
    }

    // Referral system relationships

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function withdrawals(): HasMany
    {
        return $this->hasMany(Withdrawal::class)->latest();
    }

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Astrologer $astrologer) {
            if (empty($astrologer->slug)) {
                $astrologer->slug = Str::slug($astrologer->display_name) . '-' . Str::random(5);
            }
        });
    }
}
