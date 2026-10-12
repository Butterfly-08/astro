<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Services\CommissionService;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderApiController extends Controller
{
    public function __construct(
        protected CartService $cart,
        protected ReferralService $referralService,
        protected CommissionService $commissionService
    ) {}

    /**
     * List authenticated user's orders.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $user->orders()->with(['items'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $orders->items(),
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    /**
     * Show single order details.
     */
    public function show(Request $request, string $identifier): JsonResponse
    {
        $user = $request->user();

        $order = Order::with(['items.product:id,name,slug,image'])
            ->where('user_id', $user->id)
            ->where(function ($q) use ($identifier) {
                if (is_numeric($identifier)) {
                    $q->where('id', (int) $identifier);
                }
                $q->orWhere('order_number', $identifier);
            })
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $order,
        ]);
    }

    /**
     * Place order from cart items.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'shipping_name'          => ['required', 'string', 'max:100'],
            'shipping_phone'         => ['required', 'string', 'max:20'],
            'shipping_address_line1' => ['required', 'string', 'max:255'],
            'shipping_address_line2' => ['nullable', 'string', 'max:255'],
            'shipping_city'          => ['required', 'string', 'max:80'],
            'shipping_state'         => ['required', 'string', 'max:80'],
            'shipping_pincode'       => ['required', 'string', 'min:4', 'max:10'],
            'shipping_country'       => ['nullable', 'string', 'max:60'],
            'payment_method'         => ['required', Rule::in(['cod', 'online', 'upi'])],
        ]);

        $cartItems = $this->cart->items();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Please add items before checking out.',
            ], 422);
        }

        $user = $request->user();
        $summary = $this->cart->summary();

        try {
            $order = DB::transaction(function () use ($validated, $cartItems, $summary, $user, $request) {
                // Referral attribution check
                $referral = $this->referralService->findClickedReferral($request, $user->id);

                $order = Order::create([
                    'user_id'                => $user->id,
                    'coupon_code'            => $summary['coupon']['code'] ?? null,
                    'coupon_discount'        => $summary['discount'],
                    'subtotal'               => $summary['subtotal'],
                    'shipping_charge'        => $summary['shipping'],
                    'tax_amount'             => 0.00,
                    'total_amount'           => $summary['total'],
                    'payment_method'         => $validated['payment_method'],
                    'shipping_name'          => $validated['shipping_name'],
                    'shipping_phone'         => $validated['shipping_phone'],
                    'shipping_address_line1' => $validated['shipping_address_line1'],
                    'shipping_address_line2' => $validated['shipping_address_line2'] ?? null,
                    'shipping_city'          => $validated['shipping_city'],
                    'shipping_state'         => $validated['shipping_state'],
                    'shipping_pincode'       => $validated['shipping_pincode'],
                    'shipping_country'       => $validated['shipping_country'] ?? 'India',
                    'status'                 => Order::STATUS_PENDING,
                    'referral_code'          => $referral?->referral_code,
                    'referrer_astrologer_id' => $referral?->astrologer_id,
                    'commission_status'      => $referral ? 'pending' : 'none',
                ]);

                foreach ($cartItems as $item) {
                    $product = $item->product;

                    if (!$product) {
                        throw new \RuntimeException("Product {$item->product_id} is no longer available.");
                    }

                    $price = $product->effective_price;
                    $quantity = (int) $item->quantity;
                    $subtotal = $price * $quantity;

                    OrderItem::create([
                        'order_id'       => $order->id,
                        'product_id'     => $product->id,
                        'product_name'   => $product->name,
                        'product_sku'    => $product->sku,
                        'quantity'       => $quantity,
                        'unit_price'     => $price,
                        'original_price' => $product->price,
                        'subtotal'       => $subtotal,
                    ]);

                    // Decrement stock
                    if ($product->stock !== null) {
                        $product->decrement('stock', $quantity);
                    }
                }

                // Commission creation if referral present
                if ($referral) {
                    $this->commissionService->createForOrder($order, $referral);
                    $this->referralService->clearAttribution($request);
                }

                $this->cart->clear();

                return $order;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully.',
                'data'    => $order->load('items'),
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
