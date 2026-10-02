<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
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
        // Ensure the user owns this order.
        abort_if($order->user_id !== $this->user()->id, 403);

        // OrderItem has no product relationship in the current database structure.
        $order->load('items');

        return view('user.orders.show', compact('order'));
    }

    /**
     * Cancel a pending/confirmed order.
     */
    public function cancel(Order $order): RedirectResponse
    {
        // Ensure the user owns this order.
        abort_if($order->user_id !== $this->user()->id, 403);

        if (!$order->isCancellable()) {
            return back()->with(
                'error',
                'This order can no longer be cancelled (status: ' . $order->status . ').'
            );
        }

        // Update only the order status.
        // The current products table does not contain a stock column,
        // so there is no stock restoration here.
        $order->update([
            'status' => Order::STATUS_CANCELLED,
        ]);

        return back()->with(
            'success',
            'Order #' . $order->id . ' has been cancelled successfully.'
        );
    }
}