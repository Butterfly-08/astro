<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\AstrologerReview;
use App\Models\Booking;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class BookingApiController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * Get available consultation slots for an astrologer.
     */
    public function availableSlots(string $identifier, Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $astrologer = Astrologer::approved()
            ->active()
            ->where(function ($query) use ($identifier) {
                if (is_numeric($identifier)) {
                    $query->where('id', (int) $identifier);
                }
                $query->orWhere('slug', $identifier);
            })
            ->first();

        if (!$astrologer) {
            return response()->json([
                'success' => false,
                'message' => 'Astrologer not found or unavailable.',
            ], 404);
        }

        $result = $this->bookingService->getAvailableSlots($astrologer, $request->input('date'));

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);
    }

    /**
     * List authenticated user's bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = $user->bookings()->with(['astrologer:id,display_name,slug,profile_image', 'service:id,name,icon', 'review']);

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'upcoming') {
                $query->upcoming();
            } else {
                $query->where('status', $status);
            }
        }

        $perPage = min(50, max(1, (int) $request->input('per_page', 10)));
        $bookings = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $bookings->items(),
            'meta'    => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'per_page'     => $bookings->perPage(),
                'total'        => $bookings->total(),
            ],
        ]);
    }

    /**
     * Show single booking details.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = $request->user();

        $booking = Booking::with(['astrologer', 'service', 'review'])
            ->where('user_id', $user->id)
            ->where(function ($q) use ($id) {
                if (is_numeric($id)) {
                    $q->where('id', (int) $id);
                }
                $q->orWhere('booking_number', $id);
            })
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $booking,
        ]);
    }

    /**
     * Book consultation session.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'astrologer_id'     => ['required', 'integer', 'exists:astrologers,id'],
            'service_id'        => ['nullable', 'integer', 'exists:services,id'],
            'booking_date'      => ['required', 'date', 'after_or_equal:today'],
            'start_time'        => ['required', 'string'],
            'duration_minutes'  => ['nullable', 'integer', 'min:15', 'max:180'],
            'consultation_type' => ['nullable', Rule::in(['chat', 'call', 'video'])],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ]);

        $astrologer = Astrologer::approved()->active()->findOrFail($validated['astrologer_id']);
        $user = $request->user();

        try {
            $booking = $this->bookingService->createBooking($user, $astrologer, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Booking placed successfully.',
                'data'    => $booking->load(['astrologer:id,display_name,slug', 'service:id,name']),
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel booking.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $booking = Booking::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => "Cannot cancel a booking with status: {$booking->status}",
            ], 422);
        }

        $validated = $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $validated['cancellation_reason'] ?? 'Cancelled by customer',
            'cancelled_at'        => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.',
            'data'    => $booking->fresh(),
        ]);
    }

    /**
     * Submit review for completed booking.
     */
    public function storeReview(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $booking = Booking::where('user_id', $user->id)
            ->where('id', $id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        if ($booking->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'You can only review completed consultations.',
            ], 422);
        }

        if ($booking->review()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted a review for this booking.',
            ], 422);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body'   => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $review = AstrologerReview::create([
            'booking_id'    => $booking->id,
            'astrologer_id' => $booking->astrologer_id,
            'rating'        => $validated['rating'],
            'body'          => $validated['body'],
            'is_visible'    => true,
        ]);

        // Recalculate astrologer rating
        $astrologer = $booking->astrologer;
        if ($astrologer) {
            $avg = $astrologer->reviews()->where('is_visible', true)->avg('rating') ?: 0;
            $count = $astrologer->reviews()->where('is_visible', true)->count();
            $astrologer->update([
                'rating_avg'    => round($avg, 2),
                'total_reviews' => $count,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.',
            'data'    => $review,
        ], 201);
    }
}
