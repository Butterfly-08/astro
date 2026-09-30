<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Models\Astrologer;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
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
     * Show consultation booking page for an astrologer.
     */
    public function create(Request $request, Astrologer $astrologer): View
    {
        abort_unless($astrologer->status === 'active', 404, 'Astrologer is not currently available for booking.');

        $astrologer->load('services', 'availability');
        $preselectedType = $request->query('type', 'chat');
        $preselectedServiceId = $request->query('service_id');
        $user = Auth::guard('web')->user();

        // Calculate available days of week from astrologer's schedule
        $activeDays = $astrologer->availability
            ->where('is_active', true)
            ->pluck('day_of_week')
            ->toArray();

        return view('bookings.create', compact(
            'astrologer',
            'preselectedType',
            'preselectedServiceId',
            'activeDays',
            'user'
        ));
    }

    /**
     * AJAX endpoint: Fetch available time slots for a given date.
     */
    public function getAvailableSlots(Request $request, Astrologer $astrologer): JsonResponse
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $result = $this->bookingService->getAvailableSlots($astrologer, $request->date);

        return response()->json($result);
    }

    /**
     * Store new consultation booking with double-booking check.
     */
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();
        $astrologer = Astrologer::findOrFail($request->astrologer_id);

        try {
            $booking = $this->bookingService->createBooking($user, $astrologer, $request->validated());

            return redirect()->route('bookings.confirmation', $booking->booking_number)
                ->with('success', 'Your consultation appointment has been scheduled successfully!');
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Unable to complete appointment booking. Please try again or select another slot.');
        }
    }

    /**
     * Show booking confirmation page.
     */
    public function confirmation(string $bookingNumber): View
    {
        $user = Auth::guard('web')->user();
        $booking = Booking::with('astrologer', 'service')
            ->where('booking_number', $bookingNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('bookings.confirmation', compact('booking'));
    }
}
