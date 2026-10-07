<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Astrologer;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\CommissionService;
use App\Services\WalletService;
use App\Services\WithdrawalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ReferralCommissionWalletTest extends TestCase
{
    use RefreshDatabase;

    protected User $astrologerUser;
    protected Astrologer $astrologer;
    protected Wallet $wallet;
    protected Admin $admin;
    protected ProductCategory $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed default platform settings
        Setting::set('referral_enabled', 'true');
        Setting::set('referral_cookie_days', '30');
        Setting::set('referral_attribution_model', 'last_click');
        Setting::set('referral_allow_self_referral', 'false');
        Setting::set('commission_default_type', 'percent');
        Setting::set('commission_default_value', '10.00');
        Setting::set('commission_hold_days', '7');
        Setting::set('commission_base', 'product_subtotal');
        Setting::set('withdrawal_min_amount', '100.00');
        Setting::set('withdrawal_max_amount', '50000.00');
        Setting::set('withdrawal_fee_type', 'fixed');
        Setting::set('withdrawal_fee_value', '0.00');

        // Create Admin
        $this->admin = Admin::factory()->create([
            'status' => 'active',
        ]);

        // Create Astrologer User and Profile
        $this->astrologerUser = User::factory()->create([
            'email' => 'astro.guru@example.com',
            'role' => 'astrologer',
        ]);

        $this->astrologer = Astrologer::create([
            'user_id' => $this->astrologerUser->id,
            'display_name' => 'Guru Ji',
            'email' => $this->astrologerUser->email,
            'slug' => 'guru-ji',
            'referral_code' => 'GURU100',
            'approval_status' => 'approved',
            'status' => 'active',
            'chat_rate' => 20,
            'call_rate' => 30,
        ]);

        $this->wallet = Wallet::firstOrCreate(
            ['astrologer_id' => $this->astrologer->id],
            [
                'available_balance' => 0.00,
                'pending_balance' => 0.00,
                'held_balance' => 0.00,
                'lifetime_earnings' => 0.00,
                'lifetime_withdrawn' => 0.00,
            ]
        );

        // Create Category and Product
        $this->category = ProductCategory::create([
            'name' => 'Rudraksha',
            'slug' => 'rudraksha',
            'status' => 'active',
            'commission_rate' => 12.00, // 12% category rate
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => '5 Mukhi Rudraksha',
            'slug' => '5-mukhi-rudraksha',
            'price' => 1000.00,
            'stock_quantity' => 50,
            'status' => 'active',
            'commission_type' => 'percentage',
            'commission_value' => 15.00, // 15% product rate
        ]);
    }

    public function test_referral_click_tracking_sets_cookie_and_stores_referral_record(): void
    {
        $response = $this->get('/ref/GURU100?product=' . $this->product->slug);

        $response->assertStatus(302);
        $response->assertCookie('astro_referral_code', 'GURU100');

        $this->assertDatabaseHas('referrals', [
            'referral_code' => 'GURU100',
            'astrologer_id' => $this->astrologer->id,
            'product_id' => $this->product->id,
            'status' => 'clicked',
        ]);
    }

    public function test_checkout_with_referral_creates_order_and_pending_commission(): void
    {
        $customer = User::factory()->create(['email' => 'buyer@example.com']);

        // Set referral cookie
        $this->withCookie('astro_ref', 'GURU100');

        // Place order directly through CommissionService
        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'user_id' => $customer->id,
            'subtotal' => 1000.00,
            'tax_amount' => 0.00,
            'shipping_charge' => 50.00,
            'total_amount' => 1050.00,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'online',
            'shipping_name' => 'Buyer Name',
            'shipping_phone' => '9876543210',
            'shipping_address_line1' => '123 Test St',
            'shipping_city' => 'New Delhi',
            'shipping_state' => 'Delhi',
            'shipping_pincode' => '110001',
            'referral_code' => 'GURU100',
            'astrologer_id' => $this->astrologer->id,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 1000.00,
            'original_price' => 1000.00,
            'subtotal' => 1000.00,
        ]);

        $referral = Referral::create([
            'referral_code' => 'GURU100',
            'astrologer_id' => $this->astrologer->id,
            'product_id' => $this->product->id,
            'customer_id' => $customer->id,
            'status' => 'clicked',
            'clicked_at' => now(),
        ]);

        $commissionService = app(CommissionService::class);
        $commissions = $commissionService->createForOrder($order, $referral);

        $this->assertCount(1, $commissions);
        $commission = $commissions[0];

        // 15% on 1000 = 150
        $this->assertEquals(150.00, (float) $commission->commission_amount);
        $this->assertEquals('pending', $commission->status);
        $this->assertEquals($this->astrologer->id, $commission->astrologer_id);

        // Verify wallet pending balance was updated
        $this->wallet->refresh();
        $this->assertEquals(150.00, (float) $this->wallet->pending_balance);
        $this->assertEquals(0.00, (float) $this->wallet->available_balance);
    }

    public function test_commission_release_command_moves_matured_pending_to_available_wallet(): void
    {
        $customer = User::factory()->create(['email' => 'buyer2@example.com']);

        $order = Order::create([
            'order_number' => 'ORD-TEST-002',
            'user_id' => $customer->id,
            'subtotal' => 1000.00,
            'tax_amount' => 0.00,
            'shipping_charge' => 0.00,
            'total_amount' => 1000.00,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'payment_method' => 'online',
            'shipping_name' => 'Buyer Two',
            'shipping_phone' => '9876543211',
            'shipping_address_line1' => '456 Test Ave',
            'shipping_city' => 'Mumbai',
            'shipping_state' => 'Maharashtra',
            'shipping_pincode' => '400001',
            'referral_code' => 'GURU100',
            'astrologer_id' => $this->astrologer->id,
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => $this->product->sku,
            'quantity' => 1,
            'unit_price' => 1000.00,
            'original_price' => 1000.00,
            'subtotal' => 1000.00,
        ]);

        $commission = Commission::create([
            'commission_number' => 'COMM-20261007-000001',
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'astrologer_id' => $this->astrologer->id,
            'product_id' => $this->product->id,
            'order_amount' => 1000.00,
            'commission_rate' => 15.00,
            'commission_type' => 'percentage',
            'commission_amount' => 150.00,
            'status' => 'pending',
            'available_at' => now()->subDay(), // Already matured hold period
        ]);

        $this->wallet->update([
            'pending_balance' => 150.00,
            'lifetime_earnings' => 150.00,
        ]);

        // Run release command
        Artisan::call('commission:release');

        $commission->refresh();
        $this->wallet->refresh();

        $this->assertEquals('available', $commission->status);
        $this->assertEquals(150.00, (float) $this->wallet->available_balance);
        $this->assertEquals(0.00, (float) $this->wallet->pending_balance);
        $this->assertEquals(150.00, (float) $this->wallet->lifetime_earnings);

        // Verify ledger entry
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $this->wallet->id,
            'astrologer_id' => $this->astrologer->id,
            'type' => 'commission_credit',
            'amount' => 150.00,
        ]);
    }

    public function test_astrologer_can_request_withdrawal_and_funds_are_held(): void
    {
        // Give available balance
        $this->wallet->update([
            'available_balance' => 500.00,
            'lifetime_earnings' => 500.00,
        ]);

        $response = $this->actingAs($this->astrologerUser)
            ->post(route('astrologer.withdrawals.store'), [
                'amount' => 300.00,
                'payment_method' => 'upi',
                'upi_id' => 'guruji@okhdfcbank',
            ]);

        $response->assertRedirect(route('astrologer.withdrawals.index'));
        $response->assertSessionHas('success');

        $this->wallet->refresh();
        $this->assertEquals(200.00, (float) $this->wallet->available_balance);
        $this->assertEquals(300.00, (float) $this->wallet->held_balance);

        $this->assertDatabaseHas('withdrawals', [
            'astrologer_id' => $this->astrologer->id,
            'wallet_id' => $this->wallet->id,
            'amount' => 300.00,
            'status' => 'pending',
            'upi_id' => 'guruji@okhdfcbank',
        ]);
    }

    public function test_admin_can_approve_and_mark_withdrawal_paid(): void
    {
        $this->wallet->update([
            'available_balance' => 200.00,
            'held_balance' => 300.00,
            'lifetime_earnings' => 500.00,
            'lifetime_withdrawn' => 0.00,
        ]);

        $withdrawal = Withdrawal::create([
            'withdrawal_number' => 'WD-20261007-000001',
            'astrologer_id' => $this->astrologer->id,
            'wallet_id' => $this->wallet->id,
            'amount' => 300.00,
            'fee' => 0.00,
            'net_amount' => 300.00,
            'payment_method' => 'upi',
            'upi_id' => 'guruji@okhdfcbank',
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Admin approves
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.withdrawals.approve', $withdrawal->id), [
                'notes' => 'Verified KYC and UPI.',
            ]);

        $response->assertSessionHas('success');
        $withdrawal->refresh();
        $this->assertEquals('approved', $withdrawal->status);

        // Admin marks paid
        $responsePaid = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.withdrawals.mark-paid', $withdrawal->id), [
                'transaction_reference' => 'UTR1234567890',
                'notes' => 'Disbursed via bank portal.',
            ]);

        $responsePaid->assertSessionHas('success');
        $withdrawal->refresh();
        $this->wallet->refresh();

        $this->assertEquals('paid', $withdrawal->status);
        $this->assertEquals('UTR1234567890', $withdrawal->transaction_reference);
        $this->assertEquals(0.00, (float) $this->wallet->held_balance);
        $this->assertEquals(200.00, (float) $this->wallet->available_balance);
        $this->assertEquals(300.00, (float) $this->wallet->lifetime_withdrawn);

        // Verify debit ledger
        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $this->wallet->id,
            'type' => 'withdrawal_paid',
            'amount' => 300.00,
        ]);
    }

    public function test_astrologer_can_cancel_pending_withdrawal_and_held_funds_are_restored(): void
    {
        $this->wallet->update([
            'available_balance' => 200.00,
            'held_balance' => 300.00,
            'lifetime_earnings' => 500.00,
        ]);

        $withdrawal = Withdrawal::create([
            'withdrawal_number' => 'WD-20261007-000002',
            'astrologer_id' => $this->astrologer->id,
            'wallet_id' => $this->wallet->id,
            'amount' => 300.00,
            'fee' => 0.00,
            'net_amount' => 300.00,
            'payment_method' => 'upi',
            'upi_id' => 'guruji@okhdfcbank',
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($this->astrologerUser)
            ->post(route('astrologer.withdrawals.cancel', $withdrawal->id));

        $response->assertRedirect(route('astrologer.withdrawals.index'));
        $withdrawal->refresh();
        $this->wallet->refresh();

        $this->assertEquals('cancelled', $withdrawal->status);
        $this->assertEquals(500.00, (float) $this->wallet->available_balance);
        $this->assertEquals(0.00, (float) $this->wallet->held_balance);
    }
}
