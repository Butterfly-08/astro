<?php

namespace Database\Seeders;

use App\Models\Astrologer;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            return;
        }

        $astrologers = Astrologer::active()->take(4)->get();
        if ($astrologers->isEmpty()) {
            return;
        }

        $sampleBookings = [
            [
                'booking_number'    => Booking::generateBookingNumber(),
                'user_id'           => $user->id,
                'astrologer_id'     => $astrologers[0]->id,
                'service_id'        => $astrologers[0]->services->first()?->id,
                'booking_date'      => Carbon::tomorrow()->toDateString(),
                'start_time'        => '10:00:00',
                'end_time'          => '10:30:00',
                'duration_minutes'  => 30,
                'consultation_type' => 'video',
                'rate_per_minute'   => $astrologers[0]->video_rate,
                'amount'            => $astrologers[0]->video_rate * 30,
                'payment_status'    => 'paid',
                'status'            => 'confirmed',
                'notes'             => 'Birth: 15-Aug-1995, 08:30 AM, Bengaluru. Looking for Kundli matching guidance.',
            ],
            [
                'booking_number'    => Booking::generateBookingNumber(),
                'user_id'           => $user->id,
                'astrologer_id'     => $astrologers[1]->id,
                'service_id'        => $astrologers[1]->services->first()?->id,
                'booking_date'      => Carbon::now()->addDays(3)->toDateString(),
                'start_time'        => '14:00:00',
                'end_time'          => '14:30:00',
                'duration_minutes'  => 30,
                'consultation_type' => 'call',
                'rate_per_minute'   => $astrologers[1]->call_rate,
                'amount'            => $astrologers[1]->call_rate * 30,
                'payment_status'    => 'pending',
                'status'            => 'confirmed',
                'notes'             => 'Need advice on business expansion and auspicious time (Muhurat).',
            ],
            [
                'booking_number'    => Booking::generateBookingNumber(),
                'user_id'           => $user->id,
                'astrologer_id'     => $astrologers[2]->id ?? $astrologers[0]->id,
                'service_id'        => $astrologers[2]->services->first()?->id ?? null,
                'booking_date'      => Carbon::yesterday()->toDateString(),
                'start_time'        => '16:00:00',
                'end_time'          => '16:30:00',
                'duration_minutes'  => 30,
                'consultation_type' => 'chat',
                'rate_per_minute'   => $astrologers[2]->chat_rate ?? 20.00,
                'amount'            => ($astrologers[2]->chat_rate ?? 20.00) * 30,
                'payment_status'    => 'paid',
                'status'            => 'completed',
                'completed_at'      => Carbon::yesterday()->setTime(16, 30),
                'notes'             => 'Career transition advice and gemstone recommendation.',
            ],
        ];

        foreach ($sampleBookings as $bData) {
            Booking::firstOrCreate(
                ['booking_number' => $bData['booking_number']],
                $bData
            );
        }

        $this->command->info('✓ ' . count($sampleBookings) . ' sample bookings seeded.');
    }
}
