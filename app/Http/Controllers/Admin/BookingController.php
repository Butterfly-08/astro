<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Display a listing of all customer consultations.
     */
    public function index(Request $request): View
    {
        $query = Booking::with('user', 'astrologer', 'service')->latest();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Astrologer filter
        if ($request->filled('astrologer_id')) {
            $query->where('astrologer_id', $request->astrologer_id);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('booking_date', $request->date);
        }

        // Search booking number or customer/astrologer name
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('booking_number', 'like', "%{$term}%")
                  ->orWhereHas('user', function ($uq) use ($term) {
                      $uq->where('first_name', 'like', "%{$term}%")
                         ->orWhere('last_name', 'like', "%{$term}%")
                         ->orWhere('email', 'like', "%{$term}%");
                  })
                  ->orWhereHas('astrologer', function ($aq) use ($term) {
                      $aq->where('display_name', 'like', "%{$term}%");
                  });
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $astrologers = Astrologer::active()->orderBy('display_name')->get();

        $stats = [
            'total'     => Booking::count(),
            'pending'   => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
        ];

        return view('admin.bookings.index', compact('bookings', 'astrologers', 'stats'));
    }

    /**
     * Display detailed booking information.
     */
    public function show(Booking $booking): View
    {
        $booking->load('user', 'astrologer.services', 'service');

        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Update booking status and administrative notes.
     */
    public function updateStatus(Request $request, Booking $booking): RedirectResponse
    {
        $request->validate([
            'status'         => 'required|in:pending,confirmed,completed,cancelled,rejected',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'admin_notes'    => 'nullable|string|max:1000',
        ]);

        $updates = [
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
            'admin_notes'    => $request->admin_notes,
        ];

        if ($request->status === 'completed' && !$booking->completed_at) {
            $updates['completed_at'] = now();
        }

        if ($request->status === 'cancelled' && !$booking->cancelled_at) {
            $updates['cancelled_at'] = now();
            $updates['cancellation_reason'] = $request->admin_notes ?? 'Cancelled by administrator.';
        }

        $booking->update($updates);

        return back()->with('success', "Booking {$booking->booking_number} updated successfully.");
    }
}
