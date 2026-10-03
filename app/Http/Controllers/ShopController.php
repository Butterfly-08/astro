<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::active()->with('category');

        $selectedCategory = null;

        // Category filter
        if ($request->filled('category')) {
            $categorySlug = $request->category;

            $selectedCategory = ProductCategory::active()
                ->where('slug', $categorySlug)
                ->first();

            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Price filter
        $minPrice = $request->filled('min_price')
            ? (float) $request->min_price
            : null;

        $maxPrice = $request->filled('max_price')
            ? (float) $request->max_price
            : null;

        $query->priceRange($minPrice, $maxPrice);

        // Products are available for purchase when their status is active.
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Sorting — use only columns that actually exist
        $sort = $request->query('sort', 'newest');

        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'newest'     => $query->latest(),
            'rating'     => $query->latest(),
            default      => $query->latest(),
        };

        $products = $query
            ->paginate(12)
            ->withQueryString();

        // Count active products for each category.
        $categories = ProductCategory::active()
            ->ordered()
            ->withCount([
                'products' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->get();

        return view('shop.index', compact(
            'products',
            'categories',
            'selectedCategory'
        ));
    }

    public function category(string $slug): View
    {
        $category = ProductCategory::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $products = $category
            ->activeProducts()
            ->with('category')
            ->latest()
            ->paginate(12);

        $categories = ProductCategory::active()
            ->ordered()
            ->withCount([
                'products' => function ($q) {
                    $q->where('status', 'active');
                }
            ])
            ->get();

        $selectedCategory = $category;

        return view('shop.index', compact(
            'products',
            'categories',
            'selectedCategory'
        ));
    }

    public function show(string $slug): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(4)
            ->get();

        return view('shop.show', compact(
            'product',
            'relatedProducts'
        ));
    }
}
