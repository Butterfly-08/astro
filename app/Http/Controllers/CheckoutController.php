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
        protected CartService $cart,
        protected \App\Services\ReferralService $referralService,
        protected \App\Services\CommissionService $commissionService
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

        try {
            $order = DB::transaction(function () use (
                $validated,
                $cartItems,
                $summary,
                $user,
                $request
            ) {
                // Check for referral attribution
                $referral = $this->referralService->findClickedReferral($request, $user->id);

                $order = Order::create([
                    'user_id' => $user->id,
                    'coupon_code' => $summary['coupon']['code'] ?? null,
                    'coupon_discount' => $summary['discount'],
                    'subtotal' => $summary['subtotal'],
                    'shipping_charge' => $summary['shipping'],
                    'total_amount' => $summary['total'],
                    'payment_method' => $validated['payment_method'],
                    'shipping_name' => $validated['shipping_name'],
                    'shipping_phone' => $validated['shipping_phone'],
                    'shipping_address_line1' => $validated['shipping_address_line1'],
                    'shipping_address_line2' => $validated['shipping_address_line2'] ?? null,
                    'shipping_city' => $validated['shipping_city'],
                    'shipping_state' => $validated['shipping_state'],
                    'shipping_pincode' => $validated['shipping_pincode'],
                    'shipping_country' => $validated['shipping_country'] ?? 'India',
                    'status' => Order::STATUS_PENDING,
                    'referral_code' => $referral?->referral_code,
                    'referrer_astrologer_id' => $referral?->astrologer_id,
                    'commission_status' => $referral ? 'pending' : 'none',
                ]);

                foreach ($cartItems as $item) {
                    $product = $item->product;

                    if (!$product) {
                        throw new \RuntimeException(
                            'A product in the cart is no longer available.'
                        );
                    }

                    $price = $product->effective_price;
                    $quantity = (int) $item->quantity;
                    $subtotal = $price * $quantity;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_sku' => $product->sku,
                        'quantity' => $quantity,
                        'unit_price' => $price,
                        'original_price' => $product->price,
                        'subtotal' => $subtotal,
                    ]);
                }

                // If referral exists, create pending commissions
                if ($referral) {
                    $this->commissionService->createForOrder($order, $referral);
                    $this->referralService->clearAttribution($request);
                }

                $this->cart->clear();

                return $order;
            });
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('checkout.index')
                ->with('error', 'Failed to place your order. Please try again.');
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

        $order = Order::with(['items', 'user'])
            ->where('id', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view(
            'checkout.confirmation',
            compact('order')
        );
    }
}