<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Astrologer;
use App\Models\AstrologerReview;
use App\Models\Blog;
use App\Models\Booking;
use App\Models\Horoscope;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_publish_a_horoscope_for_the_public_page(): void
    {
        $admin = Admin::factory()->create(['status' => 'active']);

        $response = $this->actingAs($admin, 'admin')->post(route('admin.horoscopes.store'), [
            'zodiac_sign' => 'Aries',
            'horoscope_date' => today()->toDateString(),
            'title' => 'A day for new beginnings',
            'content' => 'Take a thoughtful step toward the opportunity in front of you.',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.horoscopes.index'));
        $this->assertDatabaseHas('horoscopes', ['zodiac_sign' => 'Aries', 'status' => 'published']);

        $this->get(route('horoscopes.index'))->assertOk()->assertSee('A day for new beginnings');
    }

    public function test_horoscope_rejects_duplicate_sign_and_date(): void
    {
        $admin = Admin::factory()->create(['status' => 'active']);
        Horoscope::create([
            'zodiac_sign' => 'Taurus',
            'horoscope_date' => today(),
            'title' => 'First reading',
            'content' => 'A sufficiently long horoscope reading for the day.',
            'status' => 'draft',
        ]);

        $this->actingAs($admin, 'admin')
            ->from(route('admin.horoscopes.create'))
            ->post(route('admin.horoscopes.store'), [
                'zodiac_sign' => 'Taurus',
                'horoscope_date' => today()->toDateString(),
                'title' => 'Duplicate reading',
                'content' => 'Another sufficiently long horoscope reading for the day.',
                'status' => 'published',
            ])
            ->assertRedirect(route('admin.horoscopes.create'))
            ->assertSessionHasErrors('zodiac_sign');
    }

    public function test_admin_can_publish_blog_and_public_pages_hide_drafts(): void
    {
        $admin = Admin::factory()->create(['status' => 'active']);

        $this->actingAs($admin, 'admin')->post(route('admin.blogs.store'), [
            'title' => 'Understanding the Moon',
            'slug' => '',
            'excerpt' => 'A short introduction to lunar influence.',
            'body' => 'The Moon has long played an important role in astrology and reflection.',
            'status' => 'published',
        ])->assertRedirect(route('admin.blogs.index'));

        $published = Blog::query()->where('title', 'Understanding the Moon')->firstOrFail();
        $this->assertSame('understanding-the-moon', $published->slug);
        $this->get(route('blogs.index'))->assertOk()->assertSee('Understanding the Moon');
        $this->get(route('blogs.show', $published->slug))->assertOk()->assertSee($published->body);

        Blog::create([
            'title' => 'Private draft',
            'slug' => 'private-draft',
            'body' => 'This unpublished article must not be public.',
            'status' => 'draft',
        ]);

        $this->get(route('blogs.index'))->assertDontSee('Private draft');
        $this->get(route('blogs.show', 'private-draft'))->assertNotFound();
    }

    public function test_admin_can_hide_a_review_and_public_profile_stops_showing_it(): void
    {
        $admin = Admin::factory()->create(['status' => 'active']);
        $user = User::factory()->create();
        $astrologer = Astrologer::create([
            'display_name' => 'Review Astrologer',
            'email' => 'review-astrologer@example.com',
            'slug' => 'review-astrologer',
            'status' => 'active',
        ]);
        $booking = Booking::create([
            'booking_number' => 'BK-REVIEW-0001',
            'user_id' => $user->id,
            'astrologer_id' => $astrologer->id,
            'booking_date' => today(),
            'start_time' => '10:00:00',
            'end_time' => '10:30:00',
            'status' => 'completed',
        ]);
        $review = AstrologerReview::create([
            'booking_id' => $booking->id,
            'astrologer_id' => $astrologer->id,
            'rating' => 4,
            'body' => 'A helpful and thoughtful consultation.',
        ]);
        $astrologer->update(['rating_avg' => 4, 'total_reviews' => 1]);

        $this->actingAs($admin, 'admin')
            ->patch(route('admin.reviews.visibility', $review))
            ->assertRedirect();

        $this->assertDatabaseHas('astrologer_reviews', ['id' => $review->id, 'is_visible' => false]);
        $this->assertDatabaseHas('astrologers', ['id' => $astrologer->id, 'rating_avg' => 0, 'total_reviews' => 0]);
        $this->get(route('astrologers.show', $astrologer->slug))
            ->assertOk()
            ->assertDontSee('A helpful and thoughtful consultation.');
    }

    public function test_content_management_routes_require_an_admin_session(): void
    {
        $this->get(route('admin.horoscopes.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.blogs.index'))->assertRedirect(route('admin.login'));
        $this->get(route('admin.reviews.index'))->assertRedirect(route('admin.login'));
    }
}
