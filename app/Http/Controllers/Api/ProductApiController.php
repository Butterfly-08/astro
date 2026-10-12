<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductApiController extends Controller
{
    /**
     * List all active product categories.
     */
    public function categories(): JsonResponse
    {
        $categories = ProductCategory::where('status', 'active')
            ->withCount(['products' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    /**
     * List products with search, category filtering, price range, and sorting.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::where('status', 'active')->with('category');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        } elseif ($request->filled('category_slug')) {
            $slug = $request->input('category_slug');
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->input('max_price'));
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderByDesc('rating_avg'),
            'popular'    => $query->orderByDesc('total_reviews'),
            default      => $query->latest(),
        };

        $perPage = min(50, max(1, (int) $request->input('per_page', 12)));
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $products->items(),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    /**
     * List featured products.
     */
    public function featured(): JsonResponse
    {
        $featured = Product::where('status', 'active')
            ->where('is_featured', true)
            ->with('category')
            ->take(8)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $featured,
        ]);
    }

    /**
     * Show single product by ID or slug.
     */
    public function show(string $identifier): JsonResponse
    {
        $product = Product::where('status', 'active')
            ->with('category')
            ->where(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', (int) $identifier);
                }
                $query->orWhere('slug', $identifier);
            })
            ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }
}
