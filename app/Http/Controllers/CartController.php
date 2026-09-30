<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected CartService $cart) {}

    // -------------------------------------------------------------------------
    // Cart View
    // -------------------------------------------------------------------------

    /**
     * Display the shopping cart.
     */
    public function index(): View
    {
        $items   = $this->cart->items();
        $summary = $this->cart->summary();

        return view('cart.index', compact('items', 'summary'));
    }

    // -------------------------------------------------------------------------
    // Add to Cart
    // -------------------------------------------------------------------------

    /**
     * Add a product to the cart (AJAX or standard form).
     */
    public function add(Request $request, int $productId): JsonResponse|RedirectResponse
    {
        $product = Product::active()->find($productId);

        if (!$product) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
            }
            return back()->with('error', 'Product not found.');
        }

        if (!$product->in_stock) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This product is currently out of stock.'], 422);
            }
            return back()->with('error', 'This product is currently out of stock.');
        }

        $qty = max(1, (int) $request->input('quantity', 1));
        $this->cart->add($productId, $qty);

        if ($request->ajax()) {
            return response()->json([
                'success'     => true,
                'message'     => '"' . $product->name . '" has been added to your cart.',
                'cart_count'  => $this->cart->count(),
            ]);
        }

        return back()->with('success', '"' . $product->name . '" has been added to your cart.');
    }

    // -------------------------------------------------------------------------
    // Update Quantity
    // -------------------------------------------------------------------------

    /**
     * Update a cart item's quantity (AJAX).
     */
    public function update(Request $request, int $cartItemId): JsonResponse
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:100']);

        $this->cart->update($cartItemId, (int) $request->quantity);

        $items   = $this->cart->items();
        $summary = $this->cart->summary();

        return response()->json([
            'success'      => true,
            'cart_count'   => $this->cart->count(),
            'subtotal'     => '₹' . number_format($summary['subtotal'], 2),
            'discount'     => '₹' . number_format($summary['discount'], 2),
            'shipping'     => $summary['shipping'] > 0 ? '₹' . number_format($summary['shipping'], 2) : 'Free',
            'grand_total'  => '₹' . number_format($summary['total'], 2),
        ]);
    }

    // -------------------------------------------------------------------------
    // Remove Item
    // -------------------------------------------------------------------------

    /**
     * Remove an item from the cart (AJAX or form).
     */
    public function remove(Request $request, int $cartItemId): JsonResponse|RedirectResponse
    {
        $this->cart->remove($cartItemId);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Item removed from cart.',
                'cart_count' => $this->cart->count(),
            ]);
        }

        return back()->with('success', 'Item removed from your cart.');
    }

    // -------------------------------------------------------------------------
    // Coupon
    // -------------------------------------------------------------------------

    /**
     * Apply a coupon code.
     */
    public function applyCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate(['coupon_code' => 'required|string|max:50']);

        $result = $this->cart->applyCoupon($request->coupon_code);

        if ($request->ajax()) {
            $summary = $this->cart->summary();
            return response()->json(array_merge($result, [
                'discount'    => '₹' . number_format($summary['discount'], 2),
                'grand_total' => '₹' . number_format($summary['total'], 2),
            ]));
        }

        $type = $result['success'] ? 'success' : 'error';
        return back()->with($type, $result['message']);
    }

    /**
     * Remove the currently applied coupon.
     */
    public function removeCoupon(Request $request): JsonResponse|RedirectResponse
    {
        $this->cart->removeCoupon();

        if ($request->ajax()) {
            $summary = $this->cart->summary();
            return response()->json([
                'success'     => true,
                'message'     => 'Coupon removed.',
                'discount'    => '₹0.00',
                'grand_total' => '₹' . number_format($summary['total'], 2),
            ]);
        }

        return back()->with('success', 'Coupon removed from cart.');
    }
}
