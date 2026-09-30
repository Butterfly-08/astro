<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

/**
 * CartService — handles all cart operations.
 * Supports:
 *  - Guest carts (session_id based)
 *  - Logged-in user carts (user_id based)
 *  - Coupon application (stored in session)
 *  - Automatic guest→user cart merge on login
 */
class CartService
{
    // -------------------------------------------------------------------------
    // Identity helpers
    // -------------------------------------------------------------------------

    protected function userId(): ?int
    {
        return Auth::guard('web')->id();
    }

    protected function sessionId(): string
    {
        return Session::getId();
    }

    protected function cartQuery()
    {
        $uid = $this->userId();
        if ($uid) {
            return CartItem::where('user_id', $uid);
        }
        return CartItem::where('session_id', $this->sessionId())
                       ->whereNull('user_id');
    }

    // -------------------------------------------------------------------------
    // Add / Update / Remove
    // -------------------------------------------------------------------------

    /**
     * Add a product to the cart (or increment quantity if already present).
     */
    public function add(int $productId, int $qty = 1): CartItem
    {
        $product = Product::active()->findOrFail($productId);

        if ($qty < 1) {
            $qty = 1;
        }

        $uid = $this->userId();
        $sid = $this->sessionId();

        $existing = $uid
            ? CartItem::where('user_id', $uid)->where('product_id', $productId)->first()
            : CartItem::where('session_id', $sid)->whereNull('user_id')->where('product_id', $productId)->first();

        if ($existing) {
            $newQty = $existing->quantity + $qty;
            // Cap at stock
            $newQty = min($newQty, $product->stock);
            $existing->update(['quantity' => $newQty]);
            return $existing->fresh();
        }

        $qty = min($qty, $product->stock);

        return CartItem::create([
            'user_id'    => $uid,
            'session_id' => $uid ? null : $sid,
            'product_id' => $productId,
            'quantity'   => $qty,
        ]);
    }

    /**
     * Set the exact quantity of a cart item.
     */
    public function update(int $cartItemId, int $qty): void
    {
        $item = $this->cartQuery()->findOrFail($cartItemId);

        if ($qty < 1) {
            $item->delete();
            return;
        }

        $max = $item->product?->stock ?? 999;
        $item->update(['quantity' => min($qty, $max)]);
    }

    /**
     * Remove a specific item from the cart.
     */
    public function remove(int $cartItemId): void
    {
        $this->cartQuery()->where('id', $cartItemId)->delete();
    }

    /**
     * Empty the entire cart.
     */
    public function clear(): void
    {
        $this->cartQuery()->delete();
        Session::forget('coupon');
    }

    // -------------------------------------------------------------------------
    // Read
    // -------------------------------------------------------------------------

    /**
     * Get all cart items with their products.
     */
    public function items(): Collection
    {
        return $this->cartQuery()
            ->with(['product.category'])
            ->get();
    }

    /**
     * Total quantity of all items across the cart.
     */
    public function count(): int
    {
        return (int) $this->cartQuery()->sum('quantity');
    }

    /**
     * Cart subtotal (sum of line_total for all items).
     */
    public function subtotal(): float
    {
        $total = 0.0;
        foreach ($this->items() as $item) {
            $total += $item->line_total;
        }
        return round($total, 2);
    }

    /**
     * Shipping charge — free above ₹1000 (can be expanded later).
     */
    public function shippingCharge(): float
    {
        return $this->subtotal() >= 1000 ? 0.0 : 80.0;
    }

    // -------------------------------------------------------------------------
    // Coupon
    // -------------------------------------------------------------------------

    public function applyCoupon(string $code): array
    {
        $coupon = Coupon::active()->where('code', strtoupper(trim($code)))->first();

        if (!$coupon) {
            return ['success' => false, 'message' => 'Coupon code not found or has expired.'];
        }

        $subtotal = $this->subtotal();

        if (!$coupon->isValidFor($subtotal)) {
            if ($subtotal < $coupon->min_order_value) {
                return [
                    'success' => false,
                    'message' => 'Minimum order of ₹' . number_format($coupon->min_order_value, 2) . ' required for this coupon.',
                ];
            }
            return ['success' => false, 'message' => 'This coupon cannot be applied right now.'];
        }

        Session::put('coupon', [
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
            'discount' => $coupon->computeDiscount($subtotal),
        ]);

        return [
            'success'  => true,
            'message'  => 'Coupon "' . $coupon->code . '" applied successfully!',
            'discount' => $coupon->computeDiscount($subtotal),
        ];
    }

    public function removeCoupon(): void
    {
        Session::forget('coupon');
    }

    public function coupon(): ?array
    {
        return Session::get('coupon');
    }

    public function couponDiscount(): float
    {
        $couponData = $this->coupon();
        if (!$couponData) {
            return 0.0;
        }
        // Recalculate on each call (subtotal may have changed)
        $coupon = Coupon::active()->where('code', $couponData['code'])->first();
        if (!$coupon || !$coupon->isValidFor($this->subtotal())) {
            $this->removeCoupon();
            return 0.0;
        }
        return $coupon->computeDiscount($this->subtotal());
    }

    /**
     * Grand total after discount + shipping.
     */
    public function grandTotal(): float
    {
        $total = $this->subtotal() - $this->couponDiscount() + $this->shippingCharge();
        return max(0, round($total, 2));
    }

    /**
     * Full cart summary for views/checkout.
     */
    public function summary(): array
    {
        $subtotal  = $this->subtotal();
        $shipping  = $this->shippingCharge();
        $coupon    = $this->coupon();
        $discount  = $this->couponDiscount();
        $total     = max(0, round($subtotal - $discount + $shipping, 2));

        return compact('subtotal', 'shipping', 'coupon', 'discount', 'total');
    }

    // -------------------------------------------------------------------------
    // Merge guest cart into user cart on login
    // -------------------------------------------------------------------------

    public function mergeGuestCart(int $userId): void
    {
        $sid = $this->sessionId();

        $guestItems = CartItem::where('session_id', $sid)
            ->whereNull('user_id')
            ->with('product')
            ->get();

        foreach ($guestItems as $guest) {
            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $guest->product_id)
                ->first();

            if ($existing) {
                $newQty = $existing->quantity + $guest->quantity;
                $max    = $guest->product?->stock ?? 999;
                $existing->update(['quantity' => min($newQty, $max)]);
                $guest->delete();
            } else {
                $guest->update(['user_id' => $userId, 'session_id' => null]);
            }
        }
    }
}
