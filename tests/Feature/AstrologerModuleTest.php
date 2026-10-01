<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Astrologer;
use App\Models\AstrologerAvailability;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AstrologerModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create([
            'status' => 'active',
        ]);

        $this->service = Service::create([
            'name' => 'Vedic Astrology Reading',
            'slug' => 'vedic-astrology-reading',
            'description' => 'Detailed birth chart analysis using ancient Vedic principles.',
            'type' => 'consultation',
            'status' => 'active',
            'sort_order' => 1,
            'is_featured' => true,
        ]);
    }

    public function test_public_astrologers_listing_is_accessible(): void
    {
        Astrologer::create([
            'display_name' => 'Acharya Raman',
            'email' => 'raman@example.com',
            'slug' => 'acharya-raman',
            'specializations' => 'Vedic Astrology, Kundli',
            'languages' => 'Hindi, English',
            'experience_years' => 15,
            'chat_rate' => 25.00,
            'call_rate' => 35.00,
            'status' => 'active',
            'is_available' => true,
        ]);

        $response = $this->get(route('astrologers.index'));

        $response->assertStatus(200);
        $response->assertSee('Acharya Raman');
    }

    public function test_public_can_search_astrologers_by_name_or_specialization(): void
    {
        $match = Astrologer::create([
            'display_name' => 'Dr. Sunita Sharma',
            'email' => 'sunita@example.com',
            'slug' => 'dr-sunita-sharma',
            'specializations' => 'Tarot, Numerology',
            'languages' => 'English',
            'experience_years' => 10,
            'chat_rate' => 20.00,
            'call_rate' => 30.00,
            'status' => 'active',
            'is_available' => true,
        ]);

        $other = Astrologer::create([
            'display_name' => 'Pandit Devraj',
            'email' => 'devraj@example.com',
            'slug' => 'pandit-devraj',
            'specializations' => 'Vedic',
            'languages' => 'Hindi',
            'experience_years' => 8,
            'chat_rate' => 15.00,
            'call_rate' => 25.00,
            'status' => 'active',
            'is_available' => true,
        ]);

        $response = $this->get(route('astrologers.index', ['search' => 'Sunita']));
        $response->assertStatus(200);
        $response->assertSee('Dr. Sunita Sharma');
        $response->assertDontSee('Pandit Devraj');
    }

    public function test_public_can_view_active_astrologer_profile(): void
    {
        $astrologer = Astrologer::create([
            'display_name' => 'Acharya Raman',
            'email' => 'raman@example.com',
            'slug' => 'acharya-raman',
            'specializations' => 'Vedic Astrology',
            'languages' => 'Hindi, English',
            'experience_years' => 15,
            'chat_rate' => 25.00,
            'call_rate' => 35.00,
            'status' => 'active',
            'is_available' => true,
        ]);

        $astrologer->services()->attach($this->service->id);

        $response = $this->get(route('astrologers.show', $astrologer->slug));
        $response->assertStatus(200);
        $response->assertSee('Acharya Raman');
        $response->assertSee('Vedic Astrology Reading');
    }

    public function test_pending_astrologer_is_not_visible_on_public_profile(): void
    {
        $pending = Astrologer::create([
            'display_name' => 'Pending Astrologer',
            'email' => 'pending@example.com',
            'slug' => 'pending-astrologer',
            'specializations' => 'Vedic',
            'languages' => 'Hindi',
            'experience_years' => 2,
            'chat_rate' => 10.00,
            'call_rate' => 20.00,
            'status' => 'pending',
        ]);

        $response = $this->get(route('astrologers.show', $pending->slug));
        $response->assertStatus(404);
    }

    public function test_admin_can_view_astrologers_index(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.astrologers.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_new_astrologer(): void
    {
        $payload = [
            'display_name' => 'New Astrologer Guru',
            'email' => 'guru@astrovani.test',
            'phone' => '+91 99999 88888',
            'bio' => 'Experienced scholar in Vedic literature and astrology.',
            'short_bio' => 'Vedic astrologer with 12+ years experience.',
            'specializations' => 'Vedic, Palmistry',
            'languages' => 'Hindi, English, Sanskrit',
            'experience_years' => 12,
            'education' => 'MA in Sanskrit Astrology',
            'chat_rate' => 30.00,
            'call_rate' => 45.00,
            'video_rate' => 60.00,
            'status' => 'active',
            'is_featured' => 1,
            'is_available' => 1,
            'service_ids' => [$this->service->id],
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.astrologers.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('astrologers', [
            'display_name' => 'New Astrologer Guru',
            'email' => 'guru@astrovani.test',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_approve_pending_astrologer(): void
    {
        $astrologer = Astrologer::create([
            'display_name' => 'Awaiting Approval',
            'email' => 'awaiting@example.com',
            'slug' => 'awaiting-approval',
            'specializations' => 'Numerology',
            'languages' => 'English',
            'experience_years' => 5,
            'chat_rate' => 15.00,
            'call_rate' => 25.00,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.astrologers.approve', $astrologer));

        $response->assertRedirect();
        $this->assertDatabaseHas('astrologers', [
            'id' => $astrologer->id,
            'status' => 'active',
            'approved_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_reject_astrologer_with_reason(): void
    {
        $astrologer = Astrologer::create([
            'display_name' => 'Applicant Astrologer',
            'email' => 'applicant@example.com',
            'slug' => 'applicant-astrologer',
            'specializations' => 'Tarot',
            'languages' => 'Hindi',
            'experience_years' => 1,
            'chat_rate' => 10.00,
            'call_rate' => 15.00,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.astrologers.reject', $astrologer), [
                'rejection_reason' => 'Incomplete qualification certificates.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('astrologers', [
            'id' => $astrologer->id,
            'status' => 'rejected',
            'rejection_reason' => 'Incomplete qualification certificates.',
        ]);
    }

    public function test_admin_can_toggle_astrologer_availability_and_featured(): void
    {
        $astrologer = Astrologer::create([
            'display_name' => 'Toggle Astrologer',
            'email' => 'toggle@example.com',
            'slug' => 'toggle-astrologer',
            'specializations' => 'Vedic',
            'languages' => 'Hindi',
            'experience_years' => 7,
            'chat_rate' => 20.00,
            'call_rate' => 30.00,
            'status' => 'active',
            'is_available' => false,
            'is_featured' => false,
        ]);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.astrologers.toggle-availability', $astrologer));
        $this->assertTrue($astrologer->fresh()->is_available);

        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.astrologers.toggle-featured', $astrologer));
        $this->assertTrue($astrologer->fresh()->is_featured);
    }
}
