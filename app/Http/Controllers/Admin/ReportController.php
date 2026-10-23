<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display comprehensive Astrologer Bookings & Orders Reports.
     */
    public function index(Request $request): View
    {
        $selectedAstrologerId = $request->input('astrologer_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $status = $request->input('status');

        $astrologers = Astrologer::approved()->orderBy('display_name')->get();

        // 1. Astrologers Matrix Query
        $astrologerReportsQuery = Astrologer::with(['wallet', 'user'])
            ->withCount([
                'bookings as total_bookings_count' => function ($q) use ($dateFrom, $dateTo) {
                    if ($dateFrom) $q->whereDate('booking_date', '>=', $dateFrom);
                    if ($dateTo) $q->whereDate('booking_date', '<=', $dateTo);
                },
                'bookings as completed_bookings_count' => function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'completed');
                    if ($dateFrom) $q->whereDate('booking_date', '>=', $dateFrom);
                    if ($dateTo) $q->whereDate('booking_date', '<=', $dateTo);
                },
                'bookings as pending_bookings_count' => function ($q) use ($dateFrom, $dateTo) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                    if ($dateFrom) $q->whereDate('booking_date', '>=', $dateFrom);
                    if ($dateTo) $q->whereDate('booking_date', '<=', $dateTo);
                },
                'attendedCalls as attended_calls_count' => function ($q) use ($dateFrom, $dateTo) {
                    if ($dateFrom) $q->whereDate('booking_date', '>=', $dateFrom);
                    if ($dateTo) $q->whereDate('booking_date', '<=', $dateTo);
                },
                'ignoredCalls as ignored_calls_count' => function ($q) use ($dateFrom, $dateTo) {
                    if ($dateFrom) $q->whereDate('booking_date', '>=', $dateFrom);
                    if ($dateTo) $q->whereDate('booking_date', '<=', $dateTo);
                },
                'calls as total_calls_count' => function ($q) use ($dateFrom, $dateTo) {
                    if ($dateFrom) $q->whereDate('booking_date', '>=', $dateFrom);
                    if ($dateTo) $q->whereDate('booking_date', '<=', $dateTo);
                },
            ])
            ->withSum([
                'bookings as total_booking_revenue' => function ($q) use ($dateFrom, $dateTo) {
                    $q->where('status', 'completed');
                    if ($dateFrom) $q->whereDate('booking_date', '>=', $dateFrom);
                    if ($dateTo) $q->whereDate('booking_date', '<=', $dateTo);
                }
            ], 'amount');

        if ($selectedAstrologerId) {
            $astrologerReportsQuery->where('id', $selectedAstrologerId);
        }

        $astrologersMatrix = $astrologerReportsQuery->get()->map(function ($astro) use ($dateFrom, $dateTo) {
            $ordersQuery = Order::where('referrer_astrologer_id', $astro->id);
            if ($dateFrom) $ordersQuery->whereDate('created_at', '>=', $dateFrom);
            if ($dateTo) $ordersQuery->whereDate('created_at', '<=', $dateTo);

            $astro->total_orders_count = (clone $ordersQuery)->count();
            $astro->completed_orders_count = (clone $ordersQuery)->where('status', 'delivered')->count();
            $astro->pending_orders_count = (clone $ordersQuery)->whereIn('status', ['pending', 'processing', 'confirmed', 'shipped'])->count();
            $astro->total_orders_revenue = (float) (clone $ordersQuery)->where('payment_status', 'paid')->sum('total_amount');
            $astro->total_commissions_earned = (float) Commission::where('astrologer_id', $astro->id)->sum('commission_amount');

            return $astro;
        });

        // 2. Bookings Details List Query
        $bookingsQuery = Booking::with(['astrologer', 'user'])->latest();
        if ($selectedAstrologerId) {
            $bookingsQuery->where('astrologer_id', $selectedAstrologerId);
        }
        if ($dateFrom) {
            $bookingsQuery->whereDate('booking_date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $bookingsQuery->whereDate('booking_date', '<=', $dateTo);
        }
        if ($status) {
            $bookingsQuery->where('status', $status);
        }
        $bookingsList = $bookingsQuery->paginate(15, ['*'], 'bookings_page')->withQueryString();

        // 3. Orders Details List Query (orders taken/referred by astrologers)
        $ordersQuery = Order::with(['referrerAstrologer', 'user'])
            ->whereNotNull('referrer_astrologer_id')
            ->latest();

        if ($selectedAstrologerId) {
            $ordersQuery->where('referrer_astrologer_id', $selectedAstrologerId);
        }
        if ($dateFrom) {
            $ordersQuery->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $ordersQuery->whereDate('created_at', '<=', $dateTo);
        }
        if ($status) {
            $ordersQuery->where('status', $status);
        }
        $ordersList = $ordersQuery->paginate(15, ['*'], 'orders_page')->withQueryString();

        // Overall summary numbers
        $summary = [
            'total_bookings'       => Booking::when($selectedAstrologerId, fn($q) => $q->where('astrologer_id', $selectedAstrologerId))->count(),
            'completed_bookings'   => Booking::when($selectedAstrologerId, fn($q) => $q->where('astrologer_id', $selectedAstrologerId))->where('status', 'completed')->count(),
            'booking_revenue'      => (float) Booking::when($selectedAstrologerId, fn($q) => $q->where('astrologer_id', $selectedAstrologerId))->where('status', 'completed')->sum('amount'),
            'total_orders'         => Order::whereNotNull('referrer_astrologer_id')->when($selectedAstrologerId, fn($q) => $q->where('referrer_astrologer_id', $selectedAstrologerId))->count(),
            'delivered_orders'     => Order::whereNotNull('referrer_astrologer_id')->when($selectedAstrologerId, fn($q) => $q->where('referrer_astrologer_id', $selectedAstrologerId))->where('status', 'delivered')->count(),
            'orders_value'         => (float) Order::whereNotNull('referrer_astrologer_id')->when($selectedAstrologerId, fn($q) => $q->where('referrer_astrologer_id', $selectedAstrologerId))->where('payment_status', 'paid')->sum('total_amount'),
            'commissions_paid'     => (float) Commission::when($selectedAstrologerId, fn($q) => $q->where('astrologer_id', $selectedAstrologerId))->sum('commission_amount'),
            'calls_attended'       => Booking::where('consultation_type', 'call')
                ->where('status', 'completed')
                ->when($selectedAstrologerId, fn($q) => $q->where('astrologer_id', $selectedAstrologerId))
                ->when($dateFrom, fn($q) => $q->whereDate('booking_date', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('booking_date', '<=', $dateTo))
                ->count(),
            'calls_ignored'        => Booking::where('consultation_type', 'call')
                ->whereIn('status', ['rejected', 'cancelled'])
                ->when($selectedAstrologerId, fn($q) => $q->where('astrologer_id', $selectedAstrologerId))
                ->when($dateFrom, fn($q) => $q->whereDate('booking_date', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->whereDate('booking_date', '<=', $dateTo))
                ->count(),
        ];

        return view('admin.reports.index', compact(
            'astrologers',
            'astrologersMatrix',
            'bookingsList',
            'ordersList',
            'summary',
            'selectedAstrologerId',
            'dateFrom',
            'dateTo',
            'status'
        ));
    }
}
