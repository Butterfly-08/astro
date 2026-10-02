<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    // Cart ID
    // -------------------------------------------------------------------------

    /**
     * Get the current cart ID.
     *
     * Logged-in user:
     *   carts.user_id
     *
     * Guest:
     *   Find cart through cart_items.session_id
     *   or create a new cart.
     */
    protected function cartId(bool $create = true): ?int
    {
        $uid = $this->userId();
        $sid = $this->sessionId();

        // -------------------------------------------------------------
        // Logged-in user cart
        // -------------------------------------------------------------
        if ($uid) {
            $cart = DB::table('carts')
                ->where('user_id', $uid)
                ->orderByDesc('id')
                ->first();

            if ($cart) {
                return (int) $cart->id;
            }

            if (!$create) {
                return null;
            }

            return (int) DB::table('carts')->insertGetId([
                'user_id'    => $uid,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // -------------------------------------------------------------
        // Guest cart
        // -------------------------------------------------------------
        $existingCartId = DB::table('cart_items')
            ->where('session_id', $sid)
            ->value('cart_id');

        if ($existingCartId) {
            return (int) $existingCartId;
        }

        if (!$create) {
            return null;
        }

        return (int) DB::table('carts')->insertGetId([
            'user_id'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Cart Query
    // -------------------------------------------------------------------------

    protected function cartQuery(): Builder
    {
        $cartId = $this->cartId(false);

        if (!$cartId) {
            return CartItem::query()->whereRaw('1 = 0');
        }

        return CartItem::query()
            ->where('cart_id', $cartId);
    }

    // -------------------------------------------------------------------------
    // Add
    // -------------------------------------------------------------------------

    public function add(int $productId, int $qty = 1): CartItem
    {
        $product = Product::active()->findOrFail($productId);

        if ($qty < 1) {
            $qty = 1;
        }

        // -------------------------------------------------------------
        // Get / create cart
        // -------------------------------------------------------------
        $cartId = $this->cartId(true);

        // -------------------------------------------------------------
        // Find active product variant
        // -------------------------------------------------------------
        $variant = DB::table('product_variants')
            ->where('product_id', $productId)
            ->where('is_active', 1)
            ->orderBy('id')
            ->first();

        // If no variant exists, create standard variant.
        if (!$variant) {

            $variantId = DB::table('product_variants')->insertGetId([
                'product_id' => $productId,
                'name'       => 'Standard',
                'price'      => $product->price,
                'is_active'  => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $variant = DB::table('product_variants')
                ->where('id', $variantId)
                ->first();
        }

        $variantId = (int) $variant->id;
        $price     = (float) $variant->price;

        // -------------------------------------------------------------
        // Check existing item
        // -------------------------------------------------------------
        $existing = CartItem::where('cart_id', $cartId)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($existing) {

            $newQty = $existing->quantity + $qty;

            $maxStock = $product->stock ?? 999;

            $newQty = min($newQty, $maxStock);

            $existing->update([
                'quantity'   => $newQty,
                'price'      => $price,
                'product_id' => $productId,
                'updated_at' => now(),
            ]);

            return $existing->fresh();
        }

        // -------------------------------------------------------------
        // New cart item
        // -------------------------------------------------------------
        $maxStock = $product->stock ?? 999;

        $qty = min($qty, $maxStock);

        $uid = $this->userId();
        $sid = $uid ? null : $this->sessionId();

        return CartItem::create([
            'cart_id'           => $cartId,
            'product_variant_id' => $variantId,
            'session_id'        => $sid,
            'user_id'           => $uid,
            'product_id'        => $productId,
            'quantity'          => $qty,
            'price'             => $price,
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

        $maxStock = $item->product?->stock ?? 999;

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
        $item = $this->cartQuery()
            ->find($cartItemId);

        if (!$item) {
            return;
        }

        $item->delete();

        // -------------------------------------------------------------
        // If cart becomes empty, remove guest cart.
        // -------------------------------------------------------------
        $cartId = $item->cart_id;

        $remainingItems = CartItem::where('cart_id', $cartId)->exists();

        if (!$remainingItems) {

            $uid = $this->userId();

            if (!$uid) {
                DB::table('carts')
                    ->where('id', $cartId)
                    ->whereNull('user_id')
                    ->delete();
            }
        }
    }

    // -------------------------------------------------------------------------
    // Clear Cart
    // -------------------------------------------------------------------------

    public function clear(): void
    {
        $cartId = $this->cartId(false);

        if ($cartId) {

            CartItem::where('cart_id', $cartId)->delete();

            $uid = $this->userId();

            if (!$uid) {
                DB::table('carts')
                    ->where('id', $cartId)
                    ->whereNull('user_id')
                    ->delete();
            }
        }

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

        // Get / create user's cart
        $userCart = DB::table('carts')
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->first();

        if (!$userCart) {

            $userCartId = DB::table('carts')->insertGetId([
                'user_id'    => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } else {
            $userCartId = (int) $userCart->id;
        }

        foreach ($guestItems as $guest) {

            $existing = CartItem::where('cart_id', $userCartId)
                ->where('product_variant_id', $guest->product_variant_id)
                ->first();

            if ($existing) {

                $newQty =
                    $existing->quantity +
                    $guest->quantity;

                $maxStock =
                    $guest->product?->stock ?? 999;

                $existing->update([
                    'quantity'   => min($newQty, $maxStock),
                    'updated_at' => now(),
                ]);

                $guest->delete();

            } else {

                $guest->update([
                    'cart_id'    => $userCartId,
                    'user_id'    => $userId,
                    'session_id' => null,
                    'updated_at' => now(),
                ]);
            }
        }
    }
}