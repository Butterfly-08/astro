<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

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
     * Show the form for creating a new booking by admin.
     */
    public function create(Request $request): View
    {
        $astrologers = Astrologer::active()
            ->with(['services', 'availability' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('display_name')
            ->get();

        $users = User::orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email', 'phone']);
        $services = Service::where('status', 'active')->orderBy('name')->get();

        $preselectedAstrologerId = $request->query('astrologer_id');
        $preselectedUserId       = $request->query('user_id');
        $preselectedServiceId    = $request->query('service_id');
        $preselectedType         = $request->query('type', 'chat');

        return view('admin.bookings.create', compact(
            'astrologers',
            'users',
            'services',
            'preselectedAstrologerId',
            'preselectedUserId',
            'preselectedServiceId',
            'preselectedType'
        ));
    }

    /**
     * AJAX: Get available slots for an astrologer on a given date.
     */
    public function getAvailableSlots(Request $request): JsonResponse
    {
        $request->validate([
            'astrologer_id' => 'required|integer|exists:astrologers,id',
            'date'          => 'required|date',
            'duration'      => 'nullable|integer|in:15,30,45,60',
        ]);

        $astrologer = Astrologer::findOrFail($request->astrologer_id);
        $duration = (int) ($request->duration ?? 30);

        $result = $this->bookingService->getAvailableSlots($astrologer, $request->date, $duration);

        return response()->json($result);
    }

    /**
     * Store an admin-assisted consultation booking.
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'astrologer_id'     => 'required|integer|exists:astrologers,id',
            'service_id'        => 'nullable|integer|exists:services,id',
            'booking_date'      => 'required|date',
            'start_time'        => 'required|date_format:H:i',
            'duration_minutes'  => 'required|integer|in:15,30,45,60',
            'consultation_type' => 'required|string|in:chat,call,video,in_person',
            'status'            => 'required|string|in:pending,confirmed,completed',
            'payment_status'    => 'required|string|in:pending,paid,failed,refunded',
            'custom_amount'     => 'nullable|numeric|min:0',
            'notes'             => 'nullable|string|max:2000',
            'admin_notes'       => 'nullable|string|max:2000',
            'override_conflict' => 'nullable|boolean',
        ];

        if ($request->boolean('is_new_user')) {
            $rules['new_user_first_name'] = 'required|string|max:100';
            $rules['new_user_last_name']  = 'nullable|string|max:100';
            $rules['new_user_email']      = 'required|email|max:255';
            $rules['new_user_phone']      = 'nullable|string|max:20';
        } else {
            $rules['user_id'] = 'required|integer|exists:users,id';
        }

        $validated = $request->validate($rules);

        // Resolve or create User
        if ($request->boolean('is_new_user')) {
            $user = User::firstOrCreate(
                ['email' => $validated['new_user_email']],
                [
                    'first_name' => $validated['new_user_first_name'],
                    'last_name'  => $validated['new_user_last_name'] ?? '',
                    'phone'      => $validated['new_user_phone'] ?? null,
                    'password'   => bcrypt(Str::random(16)),
                    'status'     => 'active',
                ]
            );
        } else {
            $user = User::findOrFail($validated['user_id']);
        }

        $astrologer = Astrologer::findOrFail($validated['astrologer_id']);
        $bookingDate = Carbon::parse($validated['booking_date'])->toDateString();
        $startTime = Carbon::parse($validated['start_time'])->format('H:i:00');
        $duration = (int) $validated['duration_minutes'];
        $endTime = Carbon::parse($startTime)->addMinutes($duration)->format('H:i:00');
        $consultType = $validated['consultation_type'];

        // Double-booking check (unless admin explicitly checked override)
        if (!$request->boolean('override_conflict')) {
            $conflict = Booking::where('astrologer_id', $astrologer->id)
                ->whereDate('booking_date', $bookingDate)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                })
                ->exists();

            if ($conflict) {
                return back()->withInput()->withErrors([
                    'start_time' => 'This slot conflicts with an existing booking for ' . $astrologer->display_name . '. Check "Override Schedule Conflicts" if you want to book anyway.',
                ]);
            }
        }

        // Calculate rate & amount
        $ratePerMinute = match ($consultType) {
            'call'  => $astrologer->call_rate,
            'video' => $astrologer->video_rate,
            default => $astrologer->chat_rate,
        };

        $amount = $request->filled('custom_amount')
            ? (float) $request->custom_amount
            : ($ratePerMinute * $duration);

        $adminNotePrefix = '[Admin Assisted Booking]';
        $adminNotes = $request->filled('admin_notes')
            ? $adminNotePrefix . ' ' . $request->admin_notes
            : $adminNotePrefix . ' Created by ' . (Auth::guard('admin')->user()->name ?? 'Administrator');

        $booking = Booking::create([
            'booking_number'    => Booking::generateBookingNumber(),
            'user_id'           => $user->id,
            'astrologer_id'     => $astrologer->id,
            'service_id'        => $validated['service_id'] ?? null,
            'booking_date'      => $bookingDate,
            'start_time'        => $startTime,
            'end_time'          => $endTime,
            'duration_minutes'  => $duration,
            'consultation_type' => $consultType,
            'rate_per_minute'   => $ratePerMinute,
            'amount'            => $amount,
            'payment_status'    => $validated['payment_status'],
            'status'            => $validated['status'],
            'notes'             => $validated['notes'] ?? null,
            'admin_notes'       => $adminNotes,
            'completed_at'      => $validated['status'] === 'completed' ? now() : null,
        ]);

        return redirect()->route('admin.bookings.show', $booking)
            ->with('success', "Consultation booking #{$booking->booking_number} created successfully for {$user->full_name}.");
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
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'admin_notes'    => 'nullable|string|max:1000',
        ]);

        $updates = [
            'status'         => $request->status,
            'payment_status' => $request->payment_status ?? $booking->payment_status,
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

