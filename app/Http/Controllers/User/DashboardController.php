<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the User Dashboard.
     */
    public function index(): View
    {
        $user = Auth::guard('web')->user();

        /*
         * Query tables directly — they exist after migrations.
         * Removed Schema::hasTable() which hits information_schema
         * on every request and causes 30-second timeouts.
         */
        try {
            $totalOrders = DB::table('orders')
                ->where('user_id', $user->id)
                ->count();
        } catch (\Throwable) {
            $totalOrders = 0;
        }
        try {
            $totalBookings = DB::table('bookings')
                ->where('user_id', $user->id)
                ->count();

            $upcomingBookings = DB::table('bookings')
                ->where('user_id', $user->id)
                ->where('booking_date', '>=', now()->toDateString())
                ->whereIn('status', ['pending', 'confirmed'])
                ->count();

            $recentBookings = Booking::where('user_id', $user->id)
                ->with('astrologer')
                ->latest()
                ->take(3)
                ->get();
        } catch (\Throwable) {
            $totalBookings    = 0;
            $upcomingBookings = 0;
            $recentBookings   = collect();
        }

        try {
            $wishlistCount = DB::table('wishlists')
                ->where('user_id', $user->id)
                ->count();
        } catch (\Throwable) {
            $wishlistCount = 0;
        }

        return view('user.dashboard', compact(
            'user',
            'totalOrders',
            'totalBookings',
            'upcomingBookings',
            'wishlistCount',
            'recentBookings'
        ));
    }
}
