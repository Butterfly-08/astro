<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Display listing of customer's bookings.
     */
    public function index(Request $request): View
    {
        $user = Auth::guard('web')->user();
        $query = Booking::with('astrologer', 'service')
            ->where('user_id', $user->id)
            ->latest('booking_date');

        // Status filter
        if ($request->filled('status')) {
            if ($request->status === 'upcoming') {
                $query->upcoming();
            } else {
                $query->where('status', $request->status);
            }
        }

        $bookings = $query->paginate(10)->withQueryString();

        $stats = [
            'total'     => Booking::where('user_id', $user->id)->count(),
            'upcoming'  => Booking::where('user_id', $user->id)->upcoming()->count(),
            'completed' => Booking::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Booking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        return view('user.bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show booking details for a customer.
     */
    public function show(Booking $booking): View
    {
        abort_unless($booking->user_id === Auth::guard('web')->id(), 403, 'Unauthorized access to this booking.');

        $booking->load('astrologer.services', 'service');

        return view('user.bookings.show', compact('booking'));
    }

    /**
     * Cancel appointment according to cancellation policy.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === Auth::guard('web')->id(), 403, 'Unauthorized access to this booking.');

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ], [
            'cancellation_reason.required' => 'Please provide a reason for cancelling this appointment.',
        ]);

        try {
            $this->bookingService->cancelBooking($booking, $request->cancellation_reason, 'user');

            return back()->with('success', 'Your consultation appointment has been cancelled successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }
}
