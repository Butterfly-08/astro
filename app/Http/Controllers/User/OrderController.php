<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    protected function user()
    {
        return Auth::guard('web')->user();
    }

    /**
     * List all orders for the current customer.
     */
    public function index(): View
    {
        $orders = Order::with('items')
            ->where('user_id', $this->user()->id)
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    /**
     * Show a single order detail page.
     */
    public function show(Order $order): View
    {
        // Ensure the user owns this order
        abort_if($order->user_id !== $this->user()->id, 403);

        $order->load('items.product');
        return view('user.orders.show', compact('order'));
    }

    /**
     * Cancel a pending/confirmed order.
     */
    public function cancel(Order $order): RedirectResponse
    {
        abort_if($order->user_id !== $this->user()->id, 403);

        if (!$order->isCancellable()) {
            return back()->with('error', 'This order can no longer be cancelled (status: ' . $order->status . ').');
        }

        // Restore stock for each item
        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order #' . $order->order_number . ' has been cancelled. Stock has been restored.');
    }
}
