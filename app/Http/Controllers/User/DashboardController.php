<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the User Dashboard.
     */
    public function index(): View
    {
        $user = Auth::guard('web')->user();

        // Database-driven metrics (with safe checks for tables created in future phases)
        $totalOrders = Schema::hasTable('orders') ? \Illuminate\Support\Facades\DB::table('orders')->where('user_id', $user->id)->count() : 0;
        $totalBookings = Schema::hasTable('bookings') ? \Illuminate\Support\Facades\DB::table('bookings')->where('user_id', $user->id)->count() : 0;
        $upcomingBookings = Schema::hasTable('bookings') ? \Illuminate\Support\Facades\DB::table('bookings')
            ->where('user_id', $user->id)
            ->where('booking_date', '>=', now()->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->count() : 0;
        $wishlistCount = Schema::hasTable('wishlists') ? \Illuminate\Support\Facades\DB::table('wishlists')->where('user_id', $user->id)->count() : 0;
        
        $recentBookings = Schema::hasTable('bookings') 
            ? \App\Models\Booking::where('user_id', $user->id)->with('astrologer')->latest()->take(3)->get() 
            : collect();

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
