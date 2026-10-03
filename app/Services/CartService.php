<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    // -------------------------------------------------------------------------
    // Current User / Session
    // -------------------------------------------------------------------------

    protected function userId(): ?int
    {
        return Auth::guard('web')->id();
    }

    protected function sessionId(): string
    {
        return Session::getId();
    }

    // -------------------------------------------------------------------------
    // -------------------------------------------------------------------------
    // Cart Query
    // -------------------------------------------------------------------------

    /** @return Builder<CartItem> */
    protected function cartQuery(): Builder
    {
        $query = CartItem::query();
        $userId = $this->userId();

        if ($userId) {
            return $query->where('user_id', $userId);
        }

        return $query
            ->whereNull('user_id')
            ->where('session_id', $this->sessionId());
    }

    // -------------------------------------------------------------------------
    // Add
    // -------------------------------------------------------------------------

    public function add(int $productId, int $qty = 1): CartItem
    {
        $product = Product::active()->inStock()->findOrFail($productId);

        if ($qty < 1) {
            $qty = 1;
        }

        // -------------------------------------------------------------
        $existing = $this->cartQuery()
            ->where('product_id', $productId)
            ->first();

        $maxStock = (int) $product->stock;
        $newQuantity = min(($existing?->quantity ?? 0) + $qty, $maxStock);

        if ($existing) {
            $existing->update([
                'quantity' => $newQuantity,
            ]);

            return $existing;
        }

        $uid = $this->userId();

        return CartItem::create([
            'session_id' => $uid ? null : $this->sessionId(),
            'user_id'    => $uid,
            'product_id' => $productId,
            'quantity'   => $newQuantity,
        ]);
    }

    // -------------------------------------------------------------------------
    // Update Quantity
    // -------------------------------------------------------------------------

    public function update(int $cartItemId, int $qty): void
    {
        $item = $this->cartQuery()
            ->with('product')
            ->findOrFail($cartItemId);

        // Quantity 0 = remove
        if ($qty < 1) {
            $item->delete();
            return;
        }

        $maxStock = $item->product?->stock ?? 0;

        $qty = min($qty, $maxStock);

        $item->update([
            'quantity'   => $qty,
            'updated_at' => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Remove
    // -------------------------------------------------------------------------

    public function remove(int $cartItemId): void
    {
        $this->cartQuery()
            ->whereKey($cartItemId)
            ->delete();
    }

    // -------------------------------------------------------------------------
    // Clear Cart
    // -------------------------------------------------------------------------

    public function clear(): void
    {
        $this->cartQuery()->delete();
        Session::forget('coupon');
    }

    // -------------------------------------------------------------------------
    // Items
    // -------------------------------------------------------------------------

    public function items(): Collection
    {
        return $this->cartQuery()
            ->with(['product.category'])
            ->get();
    }

    // -------------------------------------------------------------------------
    // Count
    // -------------------------------------------------------------------------

    public function count(): int
    {
        return (int) $this->cartQuery()->sum('quantity');
    }

    // -------------------------------------------------------------------------
    // Subtotal
    // -------------------------------------------------------------------------

    public function subtotal(): float
    {
        $total = 0.0;

        foreach ($this->items() as $item) {

            $price = $item->price;

            if (!$price && $item->product) {
                $price = $item->product->effective_price;
            }

            $total += ((float) $price * (int) $item->quantity);
        }

        return round($total, 2);
    }

    // -------------------------------------------------------------------------
    // Shipping
    // -------------------------------------------------------------------------

    public function shippingCharge(): float
    {
        return $this->subtotal() >= 1000 ? 0.0 : 80.0;
    }

    // -------------------------------------------------------------------------
    // Coupon
    // -------------------------------------------------------------------------

    public function applyCoupon(string $code): array
    {
        $coupon = Coupon::active()
            ->where('code', strtoupper(trim($code)))
            ->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Coupon code not found or has expired.',
            ];
        }

        $subtotal = $this->subtotal();

        if (!$coupon->isValidFor($subtotal)) {

            if ($subtotal < $coupon->min_order_value) {
                return [
                    'success' => false,
                    'message' => 'Minimum order of ₹' .
                        number_format($coupon->min_order_value, 2) .
                        ' required for this coupon.',
                ];
            }

            return [
                'success' => false,
                'message' => 'This coupon cannot be applied right now.',
            ];
        }

        $discount = $coupon->computeDiscount($subtotal);

        Session::put('coupon', [
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => $coupon->value,
            'discount' => $discount,
        ]);

        return [
            'success'  => true,
            'message'  => 'Coupon "' . $coupon->code . '" applied successfully!',
            'discount' => $discount,
        ];
    }

    // -------------------------------------------------------------------------
    // Remove Coupon
    // -------------------------------------------------------------------------

    public function removeCoupon(): void
    {
        Session::forget('coupon');
    }

    // -------------------------------------------------------------------------
    // Current Coupon
    // -------------------------------------------------------------------------

    public function coupon(): ?array
    {
        return Session::get('coupon');
    }

    // -------------------------------------------------------------------------
    // Coupon Discount
    // -------------------------------------------------------------------------

    public function couponDiscount(): float
    {
        $couponData = $this->coupon();

        if (!$couponData) {
            return 0.0;
        }

        $coupon = Coupon::active()
            ->where('code', $couponData['code'])
            ->first();

        if (!$coupon || !$coupon->isValidFor($this->subtotal())) {
            $this->removeCoupon();
            return 0.0;
        }

        return (float) $coupon->computeDiscount($this->subtotal());
    }

    // -------------------------------------------------------------------------
    // Grand Total
    // -------------------------------------------------------------------------

    public function grandTotal(): float
    {
        $total =
            $this->subtotal()
            - $this->couponDiscount()
            + $this->shippingCharge();

        return max(0, round($total, 2));
    }

    // -------------------------------------------------------------------------
    // Summary
    // -------------------------------------------------------------------------

    public function summary(): array
    {
        $subtotal = $this->subtotal();
        $shipping = $this->shippingCharge();
        $coupon   = $this->coupon();
        $discount = $this->couponDiscount();

        $total = max(
            0,
            round($subtotal - $discount + $shipping, 2)
        );

        return compact(
            'subtotal',
            'shipping',
            'coupon',
            'discount',
            'total'
        );
    }

    // -------------------------------------------------------------------------
    // Guest Cart → User Cart
    // -------------------------------------------------------------------------

    public function mergeGuestCart(int $userId): void
    {
        $sid = $this->sessionId();

        $guestItems = CartItem::where('session_id', $sid)
            ->whereNull('user_id')
            ->with('product')
            ->get();

        if ($guestItems->isEmpty()) {
            return;
        }

        foreach ($guestItems as $guest) {
            $existing = CartItem::where('user_id', $userId)
                ->where('product_id', $guest->product_id)
                ->first();

            if ($existing) {

                $newQty =
                    $existing->quantity +
                    $guest->quantity;

                $maxStock =
                    $guest->product?->stock ?? 0;

                $existing->update([
                    'quantity'   => min($newQty, $maxStock),
                    'updated_at' => now(),
                ]);

                $guest->delete();

            } else {

                $guest->update([
                    'user_id'    => $userId,
                    'session_id' => null,
                ]);
            }
        }
    }
}
