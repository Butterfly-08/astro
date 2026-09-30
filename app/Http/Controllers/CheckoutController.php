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
    public function __construct(protected CartService $cart) {}

    // -------------------------------------------------------------------------
    // Step 1: Checkout form (address + payment method)
    // -------------------------------------------------------------------------

    /**
     * Show the checkout form.
     * Requires auth — guest users are redirected to login first.
     */
    public function index(): View|RedirectResponse
    {
        if ($this->cart->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add items before checking out.');
        }

        $user    = Auth::guard('web')->user();
        $items   = $this->cart->items();
        $summary = $this->cart->summary();

        return view('checkout.index', compact('user', 'items', 'summary'));
    }

    // -------------------------------------------------------------------------
    // Step 2: Place order
    // -------------------------------------------------------------------------

    /**
     * Process and place the order.
     */
    public function placeOrder(Request $request): RedirectResponse
    {
        // Validate shipping address and payment choice
        $validated = $request->validate([
            'shipping_name'         => 'required|string|max:100',
            'shipping_phone'        => 'required|string|max:20',
            'shipping_address_line1'=> 'required|string|max:255',
            'shipping_address_line2'=> 'nullable|string|max:255',
            'shipping_city'         => 'required|string|max:80',
            'shipping_state'        => 'required|string|max:80',
            'shipping_pincode'      => 'required|string|min:4|max:10',
            'shipping_country'      => 'nullable|string|max:60',
            'payment_method'        => 'required|in:cod,online,upi',
        ]);

        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        // Guard: verify all items still in stock
        foreach ($cartItems as $item) {
            if (!$item->product || !$item->product->in_stock || $item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', '"' . ($item->product?->name ?? 'A product') . '" is no longer available in the requested quantity. Please update your cart.');
            }
        }

        $summary = $this->cart->summary();
        $user    = Auth::guard('web')->user();

        try {
            DB::transaction(function () use ($validated, $cartItems, $summary, $user) {
                // Create order record
                $order = Order::create([
                    'user_id'               => $user->id,
                    'coupon_code'           => $summary['coupon']['code'] ?? null,
                    'coupon_discount'       => $summary['discount'],
                    'subtotal'              => $summary['subtotal'],
                    'shipping_charge'       => $summary['shipping'],
                    'tax_amount'            => 0,
                    'total_amount'          => $summary['total'],
                    'payment_method'        => $validated['payment_method'],
                    'payment_status'        => $validated['payment_method'] === 'cod' ? 'unpaid' : 'unpaid',
                    'status'                => 'pending',
                    'shipping_name'         => $validated['shipping_name'],
                    'shipping_phone'        => $validated['shipping_phone'],
                    'shipping_address_line1'=> $validated['shipping_address_line1'],
                    'shipping_address_line2'=> $validated['shipping_address_line2'] ?? null,
                    'shipping_city'         => $validated['shipping_city'],
                    'shipping_state'        => $validated['shipping_state'],
                    'shipping_pincode'      => $validated['shipping_pincode'],
                    'shipping_country'      => $validated['shipping_country'] ?? 'India',
                ]);

                // Create order items + deduct stock
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id'       => $order->id,
                        'product_id'     => $item->product_id,
                        'product_name'   => $item->product->name,
                        'product_sku'    => $item->product->sku,
                        'quantity'       => $item->quantity,
                        'unit_price'     => $item->product->effective_price,
                        'original_price' => $item->product->price,
                        'subtotal'       => $item->line_total,
                    ]);

                    // Deduct stock atomically
                    $item->product->decrement('stock', $item->quantity);
                }

                // Increment coupon usage if applied
                if (!empty($summary['coupon']['code'])) {
                    \App\Models\Coupon::where('code', $summary['coupon']['code'])->increment('used_count');
                }

                // Clear cart
                $this->cart->clear();

                // Store order number in session for confirmation page
                session(['last_order_number' => $order->order_number]);
            });
        } catch (\Exception $e) {
            return redirect()->route('checkout.index')
                ->with('error', 'Failed to place your order. Please try again. ' . $e->getMessage());
        }

        $orderNumber = session('last_order_number');
        return redirect()->route('checkout.confirmation', $orderNumber);
    }

    // -------------------------------------------------------------------------
    // Step 3: Order Confirmation
    // -------------------------------------------------------------------------

    /**
     * Show the order success / confirmation page.
     */
    public function confirmation(string $orderNumber): View|RedirectResponse
    {
        $user  = Auth::guard('web')->user();
        $order = Order::with('items.product')
            ->where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('checkout.confirmation', compact('order'));
    }
}
