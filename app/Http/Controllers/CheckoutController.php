<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cart
    ) {}

    public function index(): View|RedirectResponse
    {
        if ($this->cart->count() === 0) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Your cart is empty. Please add items before checking out.'
                );
        }

        $user = Auth::guard('web')->user();
        $items = $this->cart->items();
        $summary = $this->cart->summary();

        return view(
            'checkout.index',
            compact('user', 'items', 'summary')
        );
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_name' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address_line1' => 'required|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'required|string|max:80',
            'shipping_state' => 'required|string|max:80',
            'shipping_pincode' => 'required|string|min:4|max:10',
            'shipping_country' => 'nullable|string|max:60',
            'payment_method' => 'required|in:cod,online,upi',
        ]);

        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()
                ->route('login')
                ->with(
                    'error',
                    'Please login before placing your order.'
                );
        }

        $summary = $this->cart->summary();

        $shippingAddress = $validated['shipping_address_line1'];

        if (!empty($validated['shipping_address_line2'])) {
            $shippingAddress .= "\n"
                . $validated['shipping_address_line2'];
        }

        $shippingAddress .= "\n"
            . $validated['shipping_city']
            . ', '
            . $validated['shipping_state']
            . ' - '
            . $validated['shipping_pincode'];

        $shippingAddress .= "\n"
            . ($validated['shipping_country'] ?? 'India');

        try {
            $order = DB::transaction(function () use (
                $validated,
                $cartItems,
                $summary,
                $user,
                $shippingAddress
            ) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'customer_name' => $validated['shipping_name'],
                    'customer_email' => $user->email,
                    'customer_phone' => $validated['shipping_phone'],
                    'shipping_address' => $shippingAddress,
                    'status' => Order::STATUS_PENDING,
                    'subtotal' => $summary['subtotal'],
                    'total' => $summary['total'],
                ]);

                foreach ($cartItems as $item) {
                    $product = $item->product;

                    $productName = $product?->name ?? 'Product';

                    $variantName = 'Standard';

                    $price = (float) (
                        $item->price
                        ?? $product?->price
                        ?? 0
                    );

                    $quantity = (int) $item->quantity;

                    $subtotal = $price * $quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variant_id' => $item->product_variant_id,
                        'product_name' => $productName,
                        'variant_name' => $variantName,
                        'quantity' => $quantity,
                        'price' => $price,
                        'subtotal' => $subtotal,
                    ]);
                }

                $this->cart->clear();

                return $order;
            });
        } catch (\Throwable $e) {
            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Failed to place your order. Please try again. '
                    . $e->getMessage()
                );
        }

        return redirect()
            ->route(
                'checkout.confirmation',
                $order->id
            )
            ->with(
                'success',
                'Your order has been placed successfully!'
            );
    }

    public function confirmation(
        int $orderNumber
    ): View|RedirectResponse {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return redirect()
                ->route('login');
        }

        $order = Order::with([
            'items',
        ])
            ->where('id', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view(
            'checkout.confirmation',
            compact('order')
        );
    }
}