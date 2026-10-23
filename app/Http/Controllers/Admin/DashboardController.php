<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Astrologer;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the Admin Dashboard.
     */
    public function index(): View
    {
        $admin = Auth::guard('admin')->user();

        // Database-driven metrics
        $totalUsers = User::count();
        $activeUsers = User::where('status', 'active')->count();
        $totalAdmins = Admin::count();

        // Astrologer and Services Metrics
        $totalAstrologers = Astrologer::count();
        $pendingAstrologers = Astrologer::where('status', 'pending')->orWhere('approval_status', 'pending')->count();
        $activeAstrologers = Astrologer::where('status', 'active')->where('approval_status', 'approved')->count();
        $pendingAstrologersList = Astrologer::where('status', 'pending')
            ->orWhere('approval_status', 'pending')
            ->with('services')
            ->latest()
            ->take(5)
            ->get();

        $totalServices = Service::count();
        $activeServices = Service::where('status', 'active')->count();

        $totalProducts = DB::table('products')->count();
        $totalBookings = Booking::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::whereIn('payment_status', ['paid', 'completed'])->sum('total_amount')
            + Booking::whereIn('payment_status', ['paid', 'completed'])->sum('amount');

        // Recent users for quick view
        $recentUsers = User::latest()->take(5)->get();

        // Astrologers Performance Reports (Bookings & Orders)
        $astrologersPerformance = Astrologer::with(['wallet', 'user'])
            ->withCount([
                'bookings as total_bookings_count',
                'bookings as completed_bookings_count' => fn($q) => $q->where('status', 'completed'),
                'bookings as pending_bookings_count' => fn($q) => $q->whereIn('status', ['pending', 'confirmed']),
                'bookings as cancelled_bookings_count' => fn($q) => $q->where('status', 'cancelled'),
                'calls as total_calls_count',
                'attendedCalls as attended_calls_count',
                'ignoredCalls as ignored_calls_count',
            ])
            ->withSum(['bookings as total_booking_revenue' => fn($q) => $q->where('status', 'completed')], 'amount')
            ->orderByDesc('total_bookings_count')
            ->get()
            ->map(function ($astro) {
                // Store orders attributed / taken through astrologer referral
                $astro->total_orders_count = Order::where('referrer_astrologer_id', $astro->id)->count();
                $astro->completed_orders_count = Order::where('referrer_astrologer_id', $astro->id)->where('status', 'delivered')->count();
                $astro->pending_orders_count = Order::where('referrer_astrologer_id', $astro->id)->whereIn('status', ['pending', 'processing', 'confirmed', 'shipped'])->count();
                $astro->total_orders_revenue = (float) Order::where('referrer_astrologer_id', $astro->id)->where('payment_status', 'paid')->sum('total_amount');
                $astro->total_commissions_earned = (float) Commission::where('astrologer_id', $astro->id)->sum('commission_amount');
                return $astro;
            });

        $astrologerBookingsSummary = [
            'total_bookings'        => Booking::count(),
            'completed_bookings'    => Booking::where('status', 'completed')->count(),
            'pending_bookings'      => Booking::whereIn('status', ['pending', 'confirmed'])->count(),
            'cancelled_bookings'    => Booking::where('status', 'cancelled')->count(),
            'total_booking_revenue' => (float) Booking::where('status', 'completed')->sum('amount'),
        ];

        $astrologerOrdersSummary = [
            'total_astrologer_orders'     => Order::whereNotNull('referrer_astrologer_id')->count(),
            'completed_astrologer_orders' => Order::whereNotNull('referrer_astrologer_id')->where('status', 'delivered')->count(),
            'pending_astrologer_orders'   => Order::whereNotNull('referrer_astrologer_id')->whereIn('status', ['pending', 'processing', 'confirmed', 'shipped'])->count(),
            'total_orders_value'          => (float) Order::whereNotNull('referrer_astrologer_id')->where('payment_status', 'paid')->sum('total_amount'),
            'total_commissions_generated' => (float) Commission::sum('commission_amount'),
        ];

        $astrologerCallsSummary = [
            'total_calls'    => Booking::where('consultation_type', 'call')->count(),
            'attended_calls' => Booking::where('consultation_type', 'call')->where('status', 'completed')->count(),
            'ignored_calls'  => Booking::where('consultation_type', 'call')->whereIn('status', ['rejected', 'cancelled'])->count(),
            'pending_calls'  => Booking::where('consultation_type', 'call')->whereIn('status', ['pending', 'confirmed'])->count(),
        ];

        $recentAstrologerBookings = Booking::with(['astrologer', 'user'])
            ->latest()
            ->take(8)
            ->get();

        $recentAstrologerOrders = Order::with(['referrerAstrologer', 'user'])
            ->whereNotNull('referrer_astrologer_id')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'admin',
            'totalUsers',
            'activeUsers',
            'totalAdmins',
            'totalAstrologers',
            'pendingAstrologers',
            'activeAstrologers',
            'pendingAstrologersList',
            'totalServices',
            'activeServices',
            'totalProducts',
            'totalBookings',
            'totalOrders',
            'totalRevenue',
            'recentUsers',
            'astrologersPerformance',
            'astrologerBookingsSummary',
            'astrologerOrdersSummary',
            'astrologerCallsSummary',
            'recentAstrologerBookings',
            'recentAstrologerOrders'
        ));
    }
}
