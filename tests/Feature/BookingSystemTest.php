<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Astrologer;
use App\Models\AstrologerAvailability;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected User $userA;
    protected User $userB;
    protected Astrologer $astrologer;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create(['status' => 'active']);

        $this->userA = User::factory()->create([
            'email' => 'usera@example.com',
            'status' => 'active',
        ]);

        $this->userB = User::factory()->create([
            'email' => 'userb@example.com',
            'status' => 'active',
        ]);

        $this->service = Service::create([
            'name' => 'Horoscope Consultation',
            'slug' => 'horoscope-consultation',
            'type' => 'consultation',
            'status' => 'active',
            'sort_order' => 1,
            'is_featured' => true,
        ]);

        $this->astrologer = Astrologer::create([
            'display_name' => 'Dr. Arun',
            'email' => 'dr.arun@astrovani.test',
            'slug' => 'dr-arun',
            'specializations' => 'Vedic Astrology, Horoscope',
            'languages' => 'Hindi, English, Tamil',
            'experience_years' => 18,
            'chat_rate' => 30.00,
            'call_rate' => 45.00,
            'video_rate' => 60.00,
            'status' => 'active',
            'is_available' => true,
        ]);

        $this->astrologer->services()->attach($this->service->id);

        // Configure availability across all 7 days from 09:00 to 18:00
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            AstrologerAvailability::create([
                'astrologer_id' => $this->astrologer->id,
                'day_of_week'   => $day,
                'start_time'    => '09:00:00',
                'end_time'      => '18:00:00',
                'is_active'     => true,
            ]);
        }
    }

    public function test_user_can_view_booking_page_for_active_astrologer(): void
    {
        $response = $this->actingAs($this->userA, 'web')
            ->get(route('bookings.create', $this->astrologer->slug));

        $response->assertStatus(200);
        $response->assertSee('Dr. Arun');
        $response->assertSee('Horoscope Consultation');
    }

    public function test_user_can_fetch_available_slots_via_ajax(): void
    {
        $futureDate = Carbon::now()->addDays(2)->toDateString();

        $response = $this->actingAs($this->userA, 'web')
            ->getJson(route('bookings.slots', [
                'astrologer' => $this->astrologer->slug,
                'date'       => $futureDate,
            ]));

        $response->assertStatus(200);
        $response->assertJson([
            'is_working_day' => true,
        ]);
        $response->assertJsonStructure([
            'is_working_day',
            'slots' => [
                '*' => ['start_time', 'end_time', 'label', 'is_available', 'is_booked'],
            ],
        ]);
    }

    public function test_user_a_can_create_consultation_booking(): void
    {
        $futureDate = Carbon::now()->addDays(3)->toDateString();

        $payload = [
            'astrologer_id'     => $this->astrologer->id,
            'service_id'        => $this->service->id,
            'booking_date'      => $futureDate,
            'start_time'        => '10:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'chat',
            'notes'             => 'Need advice regarding career move.',
        ];

        $response = $this->actingAs($this->userA, 'web')
            ->post(route('bookings.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id'       => $this->userA->id,
            'astrologer_id' => $this->astrologer->id,
            'start_time'    => '10:00:00',
            'end_time'      => '10:30:00',
            'status'        => 'confirmed',
        ]);
    }

    /**
     * Requirement #26 & #100: Double Booking Prevention
     * When User A books 10:00 AM - 10:30 AM on a date,
     * User B attempting the same slot MUST be rejected with:
     * "This time slot is unavailable. Please select another time."
     */
    public function test_double_booking_prevention_rejects_conflicting_slot(): void
    {
        $testDate = Carbon::now()->addDays(7)->toDateString();

        // 1. User A successfully books slot 10:00 to 10:30
        $bookingPayloadA = [
            'astrologer_id'     => $this->astrologer->id,
            'service_id'        => $this->service->id,
            'booking_date'      => $testDate,
            'start_time'        => '10:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'call',
            'notes'             => 'User A appointment.',
        ];

        $responseA = $this->actingAs($this->userA, 'web')
            ->post(route('bookings.store'), $bookingPayloadA);

        $responseA->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id'       => $this->userA->id,
            'astrologer_id' => $this->astrologer->id,
            'start_time'    => '10:00:00',
        ]);

        // 2. User B attempts to book the EXACT SAME slot
        $bookingPayloadB = [
            'astrologer_id'     => $this->astrologer->id,
            'service_id'        => $this->service->id,
            'booking_date'      => $testDate,
            'start_time'        => '10:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'chat',
            'notes'             => 'User B competing booking.',
        ];

        $responseB = $this->actingAs($this->userB, 'web')
            ->from(route('bookings.create', $this->astrologer->slug))
            ->post(route('bookings.store'), $bookingPayloadB);

        // Must redirect back with validation error
        $responseB->assertRedirect(route('bookings.create', $this->astrologer->slug));
        $responseB->assertSessionHasErrors([
            'start_time' => 'This time slot is unavailable. Please select another time.',
        ]);

        // Database must still have only 1 booking for that slot
        $this->assertEquals(
            1,
            Booking::where('astrologer_id', $this->astrologer->id)
                ->where('start_time', '10:00:00')
                ->count()
        );
    }

    public function test_user_cannot_book_outside_working_hours(): void
    {
        $futureDate = Carbon::now()->addDays(4)->toDateString();

        // 06:00 is before working hours (09:00 - 18:00)
        $payload = [
            'astrologer_id'     => $this->astrologer->id,
            'service_id'        => $this->service->id,
            'booking_date'      => $futureDate,
            'start_time'        => '06:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'chat',
        ];

        $response = $this->actingAs($this->userA, 'web')
            ->post(route('bookings.store'), $payload);

        $response->assertSessionHasErrors('start_time');
    }

    public function test_user_can_cancel_future_confirmed_booking(): void
    {
        $booking = Booking::create([
            'booking_number'    => Booking::generateBookingNumber(),
            'user_id'           => $this->userA->id,
            'astrologer_id'     => $this->astrologer->id,
            'service_id'        => $this->service->id,
            'booking_date'      => Carbon::now()->addDays(5)->toDateString(),
            'start_time'        => '11:00:00',
            'end_time'          => '11:30:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'chat',
            'amount'            => 900.00,
            'status'            => 'confirmed',
        ]);

        $response = $this->actingAs($this->userA, 'web')
            ->post(route('user.bookings.cancel', $booking), [
                'cancellation_reason' => 'Schedule conflict, need to reschedule.',
            ]);

        $response->assertRedirect();
        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals('Schedule conflict, need to reschedule.', $booking->fresh()->cancellation_reason);
    }

    public function test_completed_booking_owner_can_submit_one_verified_review(): void
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'user_id' => $this->userA->id,
            'astrologer_id' => $this->astrologer->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::now()->subDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'duration_minutes' => 30,
            'consultation_type' => 'chat',
            'amount' => 900.00,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($this->userA, 'web')
            ->post(route('user.bookings.review', $booking), [
                'rating' => 5,
                'body' => 'The consultation was clear and genuinely helpful.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('astrologer_reviews', [
            'booking_id' => $booking->id,
            'astrologer_id' => $this->astrologer->id,
            'rating' => 5,
        ]);
        $this->assertSame('5.00', $this->astrologer->fresh()->rating_avg);
        $this->assertSame(1, $this->astrologer->fresh()->total_reviews);

        $this->get(route('astrologers.show', $this->astrologer->slug))
            ->assertOk()
            ->assertSee('Verified customer')
            ->assertSee('The consultation was clear and genuinely helpful.');

        $this->actingAs($this->userA, 'web')
            ->post(route('user.bookings.review', $booking), [
                'rating' => 4,
                'body' => 'A second review should not be accepted.',
            ])
            ->assertSessionHasErrors('review');

        $this->assertDatabaseCount('astrologer_reviews', 1);
    }

    public function test_only_booking_owner_can_review_a_completed_booking(): void
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'user_id' => $this->userA->id,
            'astrologer_id' => $this->astrologer->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::now()->subDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'duration_minutes' => 30,
            'consultation_type' => 'chat',
            'amount' => 900.00,
            'status' => 'completed',
        ]);

        $this->actingAs($this->userB, 'web')
            ->post(route('user.bookings.review', $booking), [
                'rating' => 5,
                'body' => 'This user did not attend the consultation.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('astrologer_reviews', 0);
    }

    public function test_incomplete_booking_cannot_be_reviewed(): void
    {
        $booking = Booking::create([
            'booking_number' => Booking::generateBookingNumber(),
            'user_id' => $this->userA->id,
            'astrologer_id' => $this->astrologer->id,
            'service_id' => $this->service->id,
            'booking_date' => Carbon::now()->addDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'duration_minutes' => 30,
            'consultation_type' => 'chat',
            'amount' => 900.00,
            'status' => 'confirmed',
        ]);

        $this->actingAs($this->userA, 'web')
            ->post(route('user.bookings.review', $booking), [
                'rating' => 5,
                'body' => 'This consultation has not happened yet.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('astrologer_reviews', 0);
    }

    public function test_admin_can_view_and_update_booking_status(): void
    {
        $booking = Booking::create([
            'booking_number'    => Booking::generateBookingNumber(),
            'user_id'           => $this->userA->id,
            'astrologer_id'     => $this->astrologer->id,
            'service_id'        => $this->service->id,
            'booking_date'      => Carbon::now()->addDays(1)->toDateString(),
            'start_time'        => '14:00:00',
            'end_time'          => '14:30:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'video',
            'amount'            => 1800.00,
            'status'            => 'confirmed',
        ]);

        // Admin views index
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.bookings.index'))
            ->assertStatus(200)
            ->assertSee($booking->booking_number);

        // Admin updates status to completed
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.bookings.update-status', $booking), [
                'status'         => 'completed',
                'payment_status' => 'paid',
                'admin_notes'    => 'Consultation completed successfully.',
            ]);

        $response->assertRedirect();
        $this->assertEquals('completed', $booking->fresh()->status);
        $this->assertEquals('Consultation completed successfully.', $booking->fresh()->admin_notes);
    }

    public function test_admin_can_view_create_booking_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.bookings.create'));

        $response->assertStatus(200);
        $response->assertSee('Book Consultation on Behalf of Customer');
        $response->assertSee($this->astrologer->display_name);
        $response->assertSee($this->userA->full_name);
    }

    public function test_admin_can_fetch_slots_via_ajax(): void
    {
        $futureDate = Carbon::now()->addDays(2)->toDateString();

        $response = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.bookings.slots', [
                'astrologer_id' => $this->astrologer->id,
                'date'          => $futureDate,
                'duration'      => 30,
            ]));

        $response->assertStatus(200);
        $response->assertJson([
            'is_working_day' => true,
        ]);
        $response->assertJsonStructure([
            'is_working_day',
            'slots' => [
                '*' => ['start_time', 'end_time', 'label', 'is_available', 'is_booked'],
            ],
        ]);
    }

    public function test_admin_can_create_booking_for_existing_user(): void
    {
        $futureDate = Carbon::now()->addDays(4)->toDateString();

        $payload = [
            'is_new_user'       => 0,
            'user_id'           => $this->userA->id,
            'astrologer_id'     => $this->astrologer->id,
            'service_id'        => $this->service->id,
            'booking_date'      => $futureDate,
            'start_time'        => '11:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'call',
            'status'            => 'confirmed',
            'payment_status'    => 'paid',
            'notes'             => 'Customer called support desk for career prediction.',
            'admin_notes'       => 'Phone booking confirmed.',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.bookings.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id'           => $this->userA->id,
            'astrologer_id'     => $this->astrologer->id,
            'start_time'        => '11:00:00',
            'end_time'          => '11:30:00',
            'consultation_type' => 'call',
            'status'            => 'confirmed',
            'payment_status'    => 'paid',
        ]);
    }

    public function test_admin_can_create_booking_and_register_new_user(): void
    {
        $futureDate = Carbon::now()->addDays(5)->toDateString();

        $payload = [
            'is_new_user'          => 1,
            'new_user_first_name'  => 'Sunita',
            'new_user_last_name'   => 'Verma',
            'new_user_email'       => 'sunita.verma@example.com',
            'new_user_phone'       => '9876501234',
            'astrologer_id'        => $this->astrologer->id,
            'booking_date'         => $futureDate,
            'start_time'           => '15:00',
            'duration_minutes'     => 45,
            'consultation_type'    => 'video',
            'status'               => 'confirmed',
            'payment_status'       => 'paid',
            'custom_amount'        => 2500.00,
            'notes'                => 'First time user consultation.',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.bookings.store'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email'      => 'sunita.verma@example.com',
            'first_name' => 'Sunita',
        ]);

        $newUser = User::where('email', 'sunita.verma@example.com')->first();

        $this->assertDatabaseHas('bookings', [
            'user_id'          => $newUser->id,
            'astrologer_id'    => $this->astrologer->id,
            'start_time'       => '15:00:00',
            'end_time'         => '15:45:00',
            'duration_minutes' => 45,
            'amount'           => 2500.00,
        ]);
    }

    public function test_admin_booking_detects_conflict_and_allows_override(): void
    {
        $futureDate = Carbon::now()->addDays(6)->toDateString();

        // 1. Create existing booking at 10:00 - 10:30
        Booking::create([
            'booking_number'    => Booking::generateBookingNumber(),
            'user_id'           => $this->userA->id,
            'astrologer_id'     => $this->astrologer->id,
            'booking_date'      => $futureDate,
            'start_time'        => '10:00:00',
            'end_time'          => '10:30:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'chat',
            'rate_per_minute'   => 30.00,
            'amount'            => 900.00,
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
        ]);

        // 2. Admin attempts to book same slot without override -> fails with validation error
        $payload = [
            'is_new_user'       => 0,
            'user_id'           => $this->userB->id,
            'astrologer_id'     => $this->astrologer->id,
            'booking_date'      => $futureDate,
            'start_time'        => '10:00',
            'duration_minutes'  => 30,
            'consultation_type' => 'chat',
            'status'            => 'confirmed',
            'payment_status'    => 'pending',
            'override_conflict' => 0,
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.bookings.store'), $payload);

        $response->assertSessionHasErrors('start_time');

        // 3. Admin attempts with override_conflict = 1 -> succeeds!
        $payload['override_conflict'] = 1;

        $overrideResponse = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.bookings.store'), $payload);

        $overrideResponse->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id'       => $this->userB->id,
            'astrologer_id' => $this->astrologer->id,
            'start_time'    => '10:00:00',
        ]);
    }
}
