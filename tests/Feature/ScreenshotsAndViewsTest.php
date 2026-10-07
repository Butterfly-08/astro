<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Astrologer;
use App\Models\Commission;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreenshotsAndViewsTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected User $astrologerUser;
    protected Astrologer $astrologer;
    protected Wallet $wallet;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed settings
        Setting::set('referral_enabled', 'true');
        Setting::set('referral_cookie_days', '30');
        Setting::set('referral_attribution_model', 'last_click');
        Setting::set('commission_default_type', 'percent');
        Setting::set('commission_default_value', '10.00');
        Setting::set('withdrawal_min_amount', '100.00');

        $this->admin = Admin::factory()->create(['status' => 'active']);

        $this->astrologerUser = User::factory()->create([
            'email' => 'anjali@astrovani.test',
            'role' => 'astrologer',
        ]);

        $this->astrologer = Astrologer::create([
            'user_id' => $this->astrologerUser->id,
            'display_name' => 'Anjali Sharma',
            'email' => $this->astrologerUser->email,
            'slug' => 'anjali-sharma',
            'referral_code' => 'ANJALI123',
            'approval_status' => 'approved',
            'status' => 'active',
            'chat_rate' => 20,
            'call_rate' => 25,
            'experience_years' => 12,
            'specializations' => 'Vedic Astrology, Tarot Reading',
        ]);

        $this->wallet = Wallet::firstOrCreate(
            ['astrologer_id' => $this->astrologer->id],
            [
                'available_balance' => 3250.00,
                'pending_balance' => 750.00,
                'held_balance' => 500.00,
                'lifetime_earnings' => 6500.00,
                'lifetime_withdrawn' => 2000.00,
            ]
        );

        $category = ProductCategory::create([
            'name' => 'Rudraksha',
            'slug' => 'rudraksha',
            'status' => 'active',
            'commission_type' => 'percentage',
            'commission_value' => 12.00,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => '5 Mukhi Rudraksha',
            'slug' => '5-mukhi-rudraksha',
            'price' => 1500.00,
            'stock_quantity' => 25,
            'status' => 'active',
            'commission_type' => 'percentage',
            'commission_value' => 15.00,
            'referral_enabled' => true,
        ]);
    }

    public function test_storefront_and_public_screens_render_successfully(): void
    {
        // 1. Home
        $this->get('/')->assertStatus(200);

        // 2. Shop Catalog
        $this->get('/shop')->assertStatus(200)->assertSee('5 Mukhi Rudraksha');

        // 3. Product Details
        $this->get('/shop/product/5-mukhi-rudraksha')->assertStatus(200)->assertSee('5 Mukhi Rudraksha');

        // 4. Referral Landing Redirect
        $response = $this->get('/ref/ANJALI123');
        $response->assertStatus(302);
        $response->assertCookie('astro_referral_code', 'ANJALI123');
    }

    public function test_astrologer_portal_screens_render_successfully(): void
    {
        // Login page
        $this->get('/astrologer/login')->assertStatus(200);

        // Authenticated Astrologer screens
        $acting = $this->actingAs($this->astrologerUser);

        // Dashboard
        $acting->get(route('astrologer.dashboard'))
            ->assertStatus(200)
            ->assertSee('Anjali Sharma')
            ->assertSee('3,250.00');

        // Wallet & Ledger
        $acting->get(route('astrologer.wallet.index'))
            ->assertStatus(200)
            ->assertSee('Double-Entry Wallet Ledger')
            ->assertSee('3,250.00');

        // Referral Links & Code Generator
        $acting->get(route('astrologer.referrals.index'))
            ->assertStatus(200)
            ->assertSee('ANJALI123');

        // Commissions History
        $acting->get(route('astrologer.commissions.index'))
            ->assertStatus(200);

        // Withdrawals
        $acting->get(route('astrologer.withdrawals.index'))
            ->assertStatus(200)
            ->assertSee('Submit Withdrawal Request');

        // Analytics
        $acting->get(route('astrologer.analytics.index'))
            ->assertStatus(200);

        // Profile
        $acting->get(route('astrologer.profile.index'))
            ->assertStatus(200)
            ->assertSee('Anjali Sharma');
    }

    public function test_admin_control_panel_screens_render_successfully(): void
    {
        // Login page
        $this->get(route('admin.login'))->assertStatus(200);

        // Authenticated Admin screens
        $adminActing = $this->actingAs($this->admin, 'admin');

        // Dashboard
        $adminActing->get(route('admin.dashboard'))
            ->assertStatus(200);

        // Commissions List
        $adminActing->get(route('admin.commissions.index'))
            ->assertStatus(200);

        // Withdrawals List
        $adminActing->get(route('admin.withdrawals.index'))
            ->assertStatus(200);

        // Wallets List
        $adminActing->get(route('admin.wallets.index'))
            ->assertStatus(200)
            ->assertSee('Anjali Sharma');

        // Wallet Ledger Transactions
        $adminActing->get(route('admin.wallets.transactions'))
            ->assertStatus(200);

        // Referral Traffic
        $adminActing->get(route('admin.referrals.index'))
            ->assertStatus(200);

        // Referral Partners
        $adminActing->get(route('admin.referrals.partners'))
            ->assertStatus(200)
            ->assertSee('ANJALI123');

        // Referral Settings
        $adminActing->get(route('admin.settings.index'))
            ->assertStatus(200)
            ->assertSee('Referral Tracking Configuration');
    }
}
