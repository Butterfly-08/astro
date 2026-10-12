<?php

namespace Tests\Feature;

use App\Models\Astrologer;
use App\Models\Blog;
use App\Models\Horoscope;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_endpoints(): void
    {
        $category = ProductCategory::factory()->create(['status' => 'active', 'name' => 'Gemstones']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status'      => 'active',
            'name'        => 'Blue Sapphire',
            'slug'        => 'blue-sapphire',
            'price'       => 2500,
            'stock'       => 10,
        ]);

        $astrologer = Astrologer::factory()->create([
            'approval_status' => Astrologer::APPROVAL_APPROVED,
            'status'          => 'active',
            'display_name'    => 'Acharya Sharma',
            'email'           => 'acharya.sharma@example.com',
            'slug'            => 'acharya-sharma',
            'chat_rate'       => 25.00,
        ]);

        $service = Service::factory()->create([
            'status' => 'active',
            'name'   => 'Kundali Milan',
            'slug'   => 'kundali-milan',
        ]);

        // Categories API
        $this->getJson('/api/v1/categories')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['name' => 'Gemstones']);

        // Products API
        $this->getJson('/api/v1/products')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['slug' => 'blue-sapphire']);

        // Product Detail API
        $this->getJson('/api/v1/products/blue-sapphire')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Blue Sapphire');

        // Astrologers API
        $this->getJson('/api/v1/astrologers')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['display_name' => 'Acharya Sharma']);

        // Astrologer Detail API
        $this->getJson('/api/v1/astrologers/acharya-sharma')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.display_name', 'Acharya Sharma');

        // Services API
        $this->getJson('/api/v1/services')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['name' => 'Kundali Milan']);
    }

    public function test_horoscopes_and_blogs_api(): void
    {
        Horoscope::create([
            'zodiac_sign'    => 'Aries',
            'horoscope_date' => today(),
            'title'          => 'Aries Daily Forecast',
            'content'        => 'Great fortunes await in creative pursuits.',
            'status'         => 'published',
        ]);

        Blog::create([
            'title'        => 'Understanding Vedic Astrology',
            'slug'         => 'understanding-vedic-astrology',
            'excerpt'      => 'A quick primer on Vedic charts.',
            'body'         => 'The ancient wisdom of the Vedas provides insightful guidance.',
            'status'       => 'published',
            'published_at' => now(),
        ]);

        $this->getJson('/api/v1/horoscopes')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['title' => 'Aries Daily Forecast']);

        $this->getJson('/api/v1/horoscopes/aries')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.zodiac_sign', 'Aries');

        $this->getJson('/api/v1/blogs')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonFragment(['slug' => 'understanding-vedic-astrology']);

        $this->getJson('/api/v1/blogs/understanding-vedic-astrology')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.title', 'Understanding Vedic Astrology');
    }

    public function test_user_registration_login_and_authenticated_profile(): void
    {
        // 1. Register
        $regResponse = $this->postJson('/api/v1/auth/register', [
            'first_name' => 'Aarav',
            'last_name'  => 'Patel',
            'email'      => 'aarav.patel@example.com',
            'password'   => 'SecretPassword123!',
            'country'    => 'India',
        ]);

        $regResponse->assertStatus(201)
            ->assertJsonPath('success', true);

        $token = $regResponse->json('data.token');
        $this->assertNotEmpty($token);

        // 2. Profile with Bearer token
        $this->withToken($token)
            ->getJson('/api/v1/auth/user')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'aarav.patel@example.com');

        // 3. Login
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email'    => 'aarav.patel@example.com',
            'password' => 'SecretPassword123!',
        ]);

        $loginResponse->assertOk()
            ->assertJsonPath('success', true);

        $newToken = $loginResponse->json('data.token');
        $this->assertNotEmpty($newToken);

        // 4. Logout
        $this->withToken($newToken)
            ->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_cart_api_operations(): void
    {
        $category = ProductCategory::factory()->create(['status' => 'active']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'status'      => 'active',
            'price'       => 500,
            'stock'       => 20,
        ]);

        // Add to cart
        $addRes = $this->postJson('/api/v1/cart/items', [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $addRes->assertStatus(201)
            ->assertJsonPath('success', true);

        // View cart
        $this->getJson('/api/v1/cart')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.count', 2);
    }
}
