<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        // Existing database has no featured column.
        // Keep this scope for compatibility.
        return $query;
    }

    public function scopeInStock($query)
    {
        // Existing database has no stock column.
        // Active products are treated as available.
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopePriceRange($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where('price', '>=', $min);
        }

        if ($max !== null) {
            $query->where('price', '<=', $max);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsOnSaleAttribute(): bool
    {
        return false;
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        return 0;
    }

    public function getInStockAttribute(): bool
    {
        return (bool) $this->is_active;
    }

    public function getStockBadgeAttribute(): array
    {
        if ($this->stock <= 0 || $this->status === 'out_of_stock') {
            return [
                'class' => 'danger',
                'label' => 'Out of Stock',
            ];
        }
        if ($this->stock <= 5) {
            return [
                'class' => 'warning text-dark',
                'label' => 'Only ' . $this->stock . ' Left',
            ];
        }
        return [
            'class' => 'success',
            'label' => 'In Stock (' . $this->stock . ')',
        ];
    }

    public function getStockAttribute(): int
    {
        // Existing DB has no stock column.
        // Use a safe UI fallback for the product page.
        return $this->is_active ? 99 : 0;
    }

    public function getRatingAvgAttribute(): float
    {
        return 0.0;
    }

    public function getTotalReviewsAttribute(): int
    {
        return 0;
    }

    public function getSkuAttribute(): string
    {
        return 'AV-' . str_pad((string) $this->id, 5, '0', STR_PAD_LEFT);
    }

    public function getShortDescriptionAttribute(): ?string
    {
        if (!$this->description) {
            return null;
        }

        return Str::limit(strip_tags($this->description), 180);
    }

    public function getIsFeaturedAttribute(): bool
    {
        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}