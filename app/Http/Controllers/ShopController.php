<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    /**
     * Display public astrology e-commerce catalog.
     */
    public function index(Request $request): View
    {
        $query = Product::active()->with('category');

        // Filter by category slug or id
        $selectedCategory = null;
        if ($request->filled('category')) {
            $categorySlug = $request->category;
            $selectedCategory = ProductCategory::active()->where('slug', $categorySlug)->first();
            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        // Search term
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Price range
        $minPrice = $request->filled('min_price') ? (float) $request->min_price : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->max_price : null;
        $query->priceRange($minPrice, $maxPrice);

        // In Stock filter
        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        // Sorting
        $sort = $request->query('sort', 'featured');
        match ($sort) {
            'price_asc'  => $query->orderByRaw('COALESCE(sale_price, price) ASC'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) DESC'),
            'newest'     => $query->latest(),
            'rating'     => $query->orderByDesc('rating_avg'),
            default      => $query->orderByDesc('is_featured')->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = ProductCategory::active()
            ->ordered()
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get();

        return view('shop.index', compact('products', 'categories', 'selectedCategory'));
    }

    /**
     * Display products for a specific category.
     */
    public function category(string $slug): View
    {
        $category = ProductCategory::active()->where('slug', $slug)->firstOrFail();
        $products = $category->activeProducts()->with('category')->latest()->paginate(12);
        
        $categories = ProductCategory::active()
            ->ordered()
            ->withCount(['products' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get();

        $selectedCategory = $category;

        return view('shop.index', compact('products', 'categories', 'selectedCategory'));
    }

    /**
     * Display detailed product page.
     */
    public function show(string $slug): View
    {
        $product = Product::active()
            ->where('slug', $slug)
            ->with('category')
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
