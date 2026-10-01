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
        'sku',
        'short_description',
        'description',
        'price',
        'sale_price',
        'stock',
        'image',
        'gallery',
        'status',
        'is_featured',
        'rating_avg',
        'total_reviews',
    ];

    protected $casts = [
        'price'         => 'decimal:2',
        'sale_price'    => 'decimal:2',
        'stock'         => 'integer',
        'is_featured'   => 'boolean',
        'rating_avg'    => 'decimal:2',
        'total_reviews' => 'integer',
        'gallery'       => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0)->where('status', '!=', 'out_of_stock');
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('sku', 'like', "%{$term}%")
              ->orWhere('short_description', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopePriceRange($query, ?float $min, ?float $max)
    {
        if ($min !== null) {
            $query->where(function ($q) use ($min) {
                $q->where('sale_price', '>=', $min)
                  ->orWhere(function ($q2) use ($min) {
                      $q2->whereNull('sale_price')->where('price', '>=', $min);
                  });
            });
        }
        if ($max !== null) {
            $query->where(function ($q) use ($max) {
                $q->where(function ($q2) use ($max) {
                    $q2->whereNotNull('sale_price')->where('sale_price', '<=', $max);
                })->orWhere(function ($q2) use ($max) {
                    $q2->whereNull('sale_price')->where('price', '<=', $max);
                });
            });
        }
        return $query;
    }

    // -------------------------------------------------------------------------
    // Accessors & Helpers
    // -------------------------------------------------------------------------

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->is_on_sale ? $this->sale_price : $this->price);
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (!$this->is_on_sale || $this->price <= 0) {
            return 0;
        }

        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    public function getInStockAttribute(): bool
    {
        return $this->stock > 0 && $this->status !== 'out_of_stock';
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

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = 'AV-' . strtoupper(Str::random(8));
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && !$product->isDirty('slug')) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
