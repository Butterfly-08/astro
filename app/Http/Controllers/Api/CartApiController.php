<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartApiController extends Controller
{
    public function __construct(
        protected CartService $cart
    ) {}

    /**
     * Get cart items and summary.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'items'   => $this->cart->items(),
                'count'   => $this->cart->count(),
                'summary' => $this->cart->summary(),
            ],
        ]);
    }

    /**
     * Add product to cart.
     */
    public function addItem(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::where('status', 'active')->find($validated['product_id']);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found or inactive.',
            ], 404);
        }

        if ($product->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Product is currently out of stock.',
            ], 422);
        }

        $qty = max(1, (int) ($validated['quantity'] ?? 1));
        $item = $this->cart->add($product->id, $qty);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart.',
            'data'    => [
                'item'    => $item->load('product'),
                'summary' => $this->cart->summary(),
            ],
        ], 201);
    }

    /**
     * Update cart item quantity.
     */
    public function updateItem(Request $request, int $itemId): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0'],
        ]);

        $this->cart->update($itemId, $validated['quantity']);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated.',
            'data'    => [
                'items'   => $this->cart->items(),
                'summary' => $this->cart->summary(),
            ],
        ]);
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $itemId): JsonResponse
    {
        $this->cart->remove($itemId);

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'data'    => [
                'summary' => $this->cart->summary(),
            ],
        ]);
    }

    /**
     * Apply coupon code.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $result = $this->cart->applyCoupon($validated['code']);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data'    => [
                'summary' => $this->cart->summary(),
            ],
        ], $result['success'] ? 200 : 422);
    }

    /**
     * Remove applied coupon.
     */
    public function removeCoupon(): JsonResponse
    {
        $this->cart->removeCoupon();

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed.',
            'data'    => [
                'summary' => $this->cart->summary(),
            ],
        ]);
    }

    /**
     * Clear all items from cart.
     */
    public function clear(): JsonResponse
    {
        $this->cart->clear();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
        ]);
    }
}
