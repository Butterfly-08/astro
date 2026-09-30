<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * List all orders with filters and search.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['user', 'items'])
            ->withCount('items');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('shipping_name', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $statusCounts = [
            'all'        => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'confirmed'  => Order::where('status', 'confirmed')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'delivered'  => Order::where('status', 'delivered')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusCounts'));
    }

    /**
     * Show a single order in detail.
     */
    public function show(Order $order): View
    {
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of an order.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status'          => 'required|in:' . implode(',', Order::allStatuses()),
            'tracking_number' => 'nullable|string|max:100',
            'carrier_name'    => 'nullable|string|max:100',
            'admin_notes'     => 'nullable|string|max:1000',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('tracking_number')) {
            $data['tracking_number'] = $request->tracking_number;
        }
        if ($request->filled('carrier_name')) {
            $data['carrier_name'] = $request->carrier_name;
        }
        if ($request->filled('admin_notes')) {
            $data['admin_notes'] = $request->admin_notes;
        }

        if ($request->status === 'delivered' && !$order->delivered_at) {
            $data['delivered_at'] = now();
        }

        $order->update($data);

        return back()->with('success', 'Order #' . $order->order_number . ' status updated to "' . ucfirst($request->status) . '".');
    }

    /**
     * Update payment status of an order.
     */
    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid,failed,refunded',
            'payment_id'     => 'nullable|string|max:150',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
            'payment_id'     => $request->payment_id ?? $order->payment_id,
        ]);

        return back()->with('success', 'Payment status updated successfully.');
    }
}
