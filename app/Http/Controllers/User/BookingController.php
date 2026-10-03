<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\AstrologerReview;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        abort_unless($this->bookingBelongsToCurrentUser($booking), 403, 'Unauthorized access to this booking.');

        $booking->load('astrologer.services', 'service', 'review');

        return view('user.bookings.show', compact('booking'));
    }

    /**
     * Submit a verified review after a completed consultation.
     */
    public function review(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($this->bookingBelongsToCurrentUser($booking), 403);
        abort_unless($booking->status === 'completed', 403, 'Only completed consultations can be reviewed.');

        if ($booking->review()->exists()) {
            return back()->withErrors(['review' => 'You have already reviewed this consultation.']);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'body' => 'required|string|min:10|max:2000',
        ]);

        DB::transaction(function () use ($booking, $validated): void {
            $booking->review()->create([
                'astrologer_id' => $booking->astrologer_id,
                'rating' => $validated['rating'],
                'body' => $validated['body'],
            ]);

            $astrologer = Astrologer::query()
                ->whereKey($booking->astrologer_id)
                ->lockForUpdate()
                ->firstOrFail();
            $previousCount = (int) $astrologer->total_reviews;
            $totalReviews = $previousCount + 1;
            $ratingAverage = (((float) $astrologer->rating_avg * $previousCount) + $validated['rating']) / $totalReviews;

            $astrologer->update([
                'rating_avg' => round($ratingAverage, 2),
                'total_reviews' => $totalReviews,
            ]);
        });

        return back()->with('success', 'Thank you. Your verified review has been published.');
    }

    /**
     * Cancel appointment according to cancellation policy.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($this->bookingBelongsToCurrentUser($booking), 403, 'Unauthorized access to this booking.');

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

    private function bookingBelongsToCurrentUser(Booking $booking): bool
    {
        $userId = Auth::guard('web')->id();

        return $userId !== null && $booking->user()->whereKey($userId)->exists();
    }
}
