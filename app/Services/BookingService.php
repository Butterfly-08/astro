<?php

namespace App\Services;

use App\Models\Astrologer;
use App\Models\AstrologerAvailability;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Get available consultation time slots for an astrologer on a given date.
     */
    public function getAvailableSlots(Astrologer $astrologer, string $dateString, int $slotDuration = 30): array
    {
        $date = Carbon::parse($dateString);
        $dayOfWeek = strtolower($date->format('l'));

        // 1. Fetch astrologer's schedule for this day of week
        $availability = $astrologer->availability()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$availability) {
            return [
                'is_working_day' => false,
                'message'        => "{$astrologer->display_name} is not available on {$date->format('l')}s.",
                'slots'          => [],
            ];
        }

        // 2. Fetch all existing active bookings on that date
        $existingBookings = Booking::where('astrologer_id', $astrologer->id)
            ->whereDate('booking_date', $date->toDateString())
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['start_time', 'end_time']);

        // 3. Generate slots between working hours
        $slots = [];
        $startTime = Carbon::parse($date->toDateString() . ' ' . $availability->start_time);
        $endTime = Carbon::parse($date->toDateString() . ' ' . $availability->end_time);
        $now = Carbon::now();

        while ($startTime->copy()->addMinutes($slotDuration)->lte($endTime)) {
            $slotEnd = $startTime->copy()->addMinutes($slotDuration);
            $slotStartStr = $startTime->format('H:i');
            $slotEndStr = $slotEnd->format('H:i');

            // Check if slot has already passed today
            $isPast = $date->isToday() && $startTime->lte($now);

            // Check if slot overlaps with an existing booking
            $isBooked = $existingBookings->contains(function ($booking) use ($slotStartStr, $slotEndStr) {
                $bStart = substr($booking->start_time, 0, 5);
                $bEnd   = substr($booking->end_time, 0, 5);
                // Overlap condition: slotStart < bEnd AND slotEnd > bStart
                return ($slotStartStr < $bEnd && $slotEndStr > $bStart);
            });

            // Period of day classification
            $hour = (int) $startTime->format('G');
            $period = $hour < 12 ? 'morning' : ($hour < 17 ? 'afternoon' : 'evening');

            $slots[] = [
                'start_time'   => $slotStartStr,
                'end_time'     => $slotEndStr,
                'label'        => $startTime->format('h:i A') . ' – ' . $slotEnd->format('h:i A'),
                'is_available' => (!$isPast && !$isBooked),
                'is_past'      => $isPast,
                'is_booked'    => $isBooked,
                'period'       => $period,
            ];

            $startTime->addMinutes($slotDuration);
        }

        return [
            'is_working_day' => true,
            'message'        => null,
            'slots'          => $slots,
        ];
    }

    /**
     * Create an appointment with double-booking prevention inside a DB transaction.
     */
    public function createBooking(User $user, Astrologer $astrologer, array $data): Booking
    {
        return DB::transaction(function () use ($user, $astrologer, $data) {
            $bookingDate = Carbon::parse($data['booking_date'])->toDateString();
            $startTime   = Carbon::parse($data['start_time'])->format('H:i:00');
            $duration    = (int) ($data['duration_minutes'] ?? 30);
            $endTime     = Carbon::parse($startTime)->addMinutes($duration)->format('H:i:00');
            $consultType = $data['consultation_type'] ?? 'chat';

            // 1. Double Booking Check with Exclusive Row Locking
            $hasConflict = Booking::where('astrologer_id', $astrologer->id)
                ->whereDate('booking_date', $bookingDate)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                          ->where('end_time', '>', $startTime);
                })
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw ValidationException::withMessages([
                    'start_time' => 'This time slot is unavailable. Please select another time.',
                ]);
            }

            // 2. Validate astrologer availability schedule
            $dayOfWeek = strtolower(Carbon::parse($bookingDate)->format('l'));
            $isScheduled = AstrologerAvailability::where('astrologer_id', $astrologer->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->where('start_time', '<=', $startTime)
                ->where('end_time', '>=', $endTime)
                ->exists();

            if (!$isScheduled) {
                throw ValidationException::withMessages([
                    'start_time' => "The selected time falls outside {$astrologer->display_name}'s working hours.",
                ]);
            }

            // 3. Determine consultation rate
            $ratePerMinute = match ($consultType) {
                'call'  => $astrologer->call_rate,
                'video' => $astrologer->video_rate,
                default => $astrologer->chat_rate,
            };

            $amount = $ratePerMinute * $duration;

            // 4. Create the booking record
            $booking = Booking::create([
                'booking_number'    => Booking::generateBookingNumber(),
                'user_id'           => $user->id,
                'astrologer_id'     => $astrologer->id,
                'service_id'        => $data['service_id'] ?? null,
                'booking_date'      => $bookingDate,
                'start_time'        => $startTime,
                'end_time'          => $endTime,
                'duration_minutes'  => $duration,
                'consultation_type' => $consultType,
                'rate_per_minute'   => $ratePerMinute,
                'amount'            => $amount,
                'payment_status'    => 'pending',
                'status'            => 'confirmed', // Confirmed appointment ready for consultation
                'notes'             => $data['notes'] ?? null,
            ]);

            return $booking;
        });
    }

    /**
     * Cancel an existing booking with validation.
     */
    public function cancelBooking(Booking $booking, string $reason, string $cancelledBy = 'user'): Booking
    {
        if (!$booking->canBeCancelled() && $cancelledBy === 'user') {
            throw ValidationException::withMessages([
                'booking' => 'This booking cannot be cancelled because it is already completed, cancelled, or in the past.',
            ]);
        }

        $booking->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $reason,
            'cancelled_at'        => now(),
        ]);

        return $booking;
    }
}
