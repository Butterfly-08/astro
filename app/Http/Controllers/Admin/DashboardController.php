<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Metrics for modules to be integrated in future phases (with safe schema checks)
        $totalAstrologers = Schema::hasTable('astrologers') ? \Illuminate\Support\Facades\DB::table('astrologers')->count() : 0;
        $totalServices = Schema::hasTable('services') ? \Illuminate\Support\Facades\DB::table('services')->count() : 0;
        $totalProducts = Schema::hasTable('products') ? \Illuminate\Support\Facades\DB::table('products')->count() : 0;
        $totalBookings = Schema::hasTable('bookings') ? \Illuminate\Support\Facades\DB::table('bookings')->count() : 0;
        $totalOrders = Schema::hasTable('orders') ? \Illuminate\Support\Facades\DB::table('orders')->count() : 0;
        $totalRevenue = Schema::hasTable('payments') ? \Illuminate\Support\Facades\DB::table('payments')->where('payment_status', 'completed')->sum('amount') : 0;

        // Recent users for quick view
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'admin',
            'totalUsers',
            'activeUsers',
            'totalAdmins',
            'totalAstrologers',
            'totalServices',
            'totalProducts',
            'totalBookings',
            'totalOrders',
            'totalRevenue',
            'recentUsers'
        ));
    }
}
