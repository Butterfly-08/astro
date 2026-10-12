<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Astrologer;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Referral;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminPanelComprehensiveTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected User $user;
    protected Astrologer $astrologer;
    protected Service $service;
    protected ProductCategory $category;
    protected Product $product;
    protected Wallet $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed platform settings
        Setting::set('referral_enabled', 'true');
        Setting::set('referral_cookie_days', '30');
        Setting::set('referral_attribution_model', 'last_click');
        Setting::set('referral_allow_self_referral', 'false');
        Setting::set('referral_code_prefix', 'ASTRO');
        Setting::set('commission_default_type', 'percent');
        Setting::set('commission_default_value', '10.00');
        Setting::set('commission_hold_days', '7');
        Setting::set('commission_base', 'product_subtotal');
        Setting::set('withdrawal_min_amount', '100.00');
        Setting::set('withdrawal_max_amount', '50000.00');
        Setting::set('withdrawal_fee_type', 'fixed');
        Setting::set('withdrawal_fee_value', '0.00');
        Setting::set('fraud_block_same_ip', 'false');
        Setting::set('fraud_max_clicks_per_minute', '20');

        $this->admin = Admin::factory()->create([
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Secret123!'),
            'status' => 'active',
        ]);

        $this->user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'status' => 'active',
        ]);

        $this->service = Service::create([
            'name' => 'Vedic Astrology Consultation',
            'type' => 'consultation',
            'status' => 'active',
            'sort_order' => 1,
            'is_featured' => true,
        ]);

        $this->category = ProductCategory::create([
            'name' => 'Gemstones',
            'slug' => 'gemstones',
            'status' => 'active',
            'sort_order' => 1,
        ]);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name' => 'Blue Sapphire',
            'slug' => 'blue-sapphire',
            'price' => 5000.00,
            'stock' => 10,
            'status' => 'active',
        ]);

        $this->astrologer = Astrologer::create([
            'user_id' => $this->user->id,
            'display_name' => 'Acharya Sharma',
            'email' => 'acharya@example.com',
            'slug' => 'acharya-sharma',
            'referral_code' => 'ACHARYA10',
            'approval_status' => 'approved',
            'status' => 'active',
            'experience_years' => 12,
            'chat_rate' => 25.00,
            'call_rate' => 35.00,
            'video_rate' => 50.00,
        ]);

        $this->wallet = Wallet::firstOrCreate(
            ['astrologer_id' => $this->astrologer->id],
            [
                'available_balance' => 500.00,
                'pending_balance' => 0.00,
                'held_balance' => 0.00,
                'lifetime_earnings' => 500.00,
                'lifetime_withdrawn' => 0.00,
            ]
        );
    }

    public function test_admin_guest_middleware_protects_routes_and_inactive_admin_blocked(): void
    {
        // 1. Unauthenticated request to protected admin route redirects to admin.login
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error');

        $this->get(route('admin.users.index'))
            ->assertRedirect(route('admin.login'));

        // 2. Inactive admin is logged out and redirected
        $inactiveAdmin = Admin::factory()->create(['status' => 'inactive']);
        $this->actingAs($inactiveAdmin, 'admin')
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error');

        $this->assertGuest('admin');

        // 3. Authenticated admin visiting login page is redirected to dashboard
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.login'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_login_and_logout_actions(): void
    {
        // Test login with valid credentials
        $loginResponse = $this->post(route('admin.login.submit'), [
            'email' => 'superadmin@example.com',
            'password' => 'Secret123!',
        ]);

        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin, 'admin');

        // Test logout
        $logoutResponse = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.logout'));

        $logoutResponse->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_admin_dashboard_renders_successfully_with_metrics(): void
    {
        // Create a pending astrologer to verify dashboard list
        Astrologer::create([
            'user_id' => $this->user->id,
            'display_name' => 'Pending Pandit',
            'email' => 'pending@example.com',
            'slug' => 'pending-pandit',
            'referral_code' => 'PENDING01',
            'approval_status' => 'pending',
            'status' => 'pending',
            'experience_years' => 5,
            'chat_rate' => 20,
            'call_rate' => 30,
            'video_rate' => 40,
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalUsers');
        $response->assertViewHas('totalAstrologers');
        $response->assertViewHas('totalOrders');
        $response->assertViewHas('totalRevenue');
        $response->assertSee('John');
        $response->assertSee('Pending Pandit');
    }

    public function test_admin_user_management_listing_and_filtering(): void
    {
        User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.users.index', ['search' => 'Jane', 'status' => 'inactive']));

        $response->assertOk();
        $response->assertSee('Jane');
        $response->assertDontSee('John Doe');
    }

    public function test_admin_category_management_lifecycle(): void
    {
        $admin = $this->admin;

        // View index & create
        $this->actingAs($admin, 'admin')->get(route('admin.categories.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.categories.create'))->assertOk();

        // Store new category
        $storeResponse = $this->actingAs($admin, 'admin')->post(route('admin.categories.store'), [
            'name' => 'Yantras',
            'description' => 'Sacred geometric energy devices',
            'status' => 'active',
            'sort_order' => 2,
        ]);
        $storeResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', ['name' => 'Yantras']);

        $category = ProductCategory::where('name', 'Yantras')->first();

        // Edit & update
        $this->actingAs($admin, 'admin')->get(route('admin.categories.edit', $category))->assertOk();
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.categories.update', $category), [
            'name' => 'Sacred Yantras',
            'description' => 'Updated description',
            'status' => 'active',
            'sort_order' => 3,
        ]);
        $updateResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', ['name' => 'Sacred Yantras']);

        // Toggle status
        $this->actingAs($admin, 'admin')->post(route('admin.categories.toggle-status', $category));
        $this->assertEquals('inactive', $category->fresh()->status);

        // Delete prevented when category has products
        $preventDeleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $this->category));
        $preventDeleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('product_categories', ['id' => $this->category->id]);

        // Delete allowed when no products
        $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.categories.destroy', $category));
        $deleteResponse->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseMissing('product_categories', ['id' => $category->id]);
    }

    public function test_admin_service_management_lifecycle(): void
    {
        $admin = $this->admin;

        $this->actingAs($admin, 'admin')->get(route('admin.services.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.services.create'))->assertOk();

        // Store service
        $storeResponse = $this->actingAs($admin, 'admin')->post(route('admin.services.store'), [
            'name' => 'Tarot Card Reading',
            'type' => 'consultation',
            'status' => 'active',
            'sort_order' => 2,
        ]);
        $storeResponse->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', ['name' => 'Tarot Card Reading']);

        $service = Service::where('name', 'Tarot Card Reading')->first();

        // Edit & update
        $this->actingAs($admin, 'admin')->get(route('admin.services.edit', $service))->assertOk();
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.services.update', $service), [
            'name' => 'Tarot & Intuitive Reading',
            'type' => 'consultation',
            'status' => 'active',
            'sort_order' => 5,
        ]);
        $updateResponse->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseHas('services', ['name' => 'Tarot & Intuitive Reading']);

        // Toggle status
        $this->actingAs($admin, 'admin')->post(route('admin.services.toggle-status', $service));
        $this->assertEquals('inactive', $service->fresh()->status);

        // Delete service
        $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.services.destroy', $service));
        $deleteResponse->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_admin_product_management_lifecycle(): void
    {
        $admin = $this->admin;

        $this->actingAs($admin, 'admin')->get(route('admin.products.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.products.create'))->assertOk();

        // Store product
        $storeResponse = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
            'category_id' => $this->category->id,
            'name' => 'Yellow Sapphire Pukhraj',
            'sku' => 'SKU-PUKHRAJ-001',
            'price' => 7500.00,
            'stock' => 15,
            'status' => 'active',
        ]);
        $storeResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Yellow Sapphire Pukhraj']);

        $product = Product::where('name', 'Yellow Sapphire Pukhraj')->first();

        // Edit & update
        $this->actingAs($admin, 'admin')->get(route('admin.products.edit', $product))->assertOk();
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.products.update', $product), [
            'category_id' => $this->category->id,
            'name' => 'Certified Yellow Sapphire Pukhraj',
            'sku' => 'SKU-PUKHRAJ-001',
            'price' => 8000.00,
            'stock' => 12,
            'status' => 'active',
        ]);
        $updateResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', ['name' => 'Certified Yellow Sapphire Pukhraj']);

        // Toggle status & featured
        $this->actingAs($admin, 'admin')->post(route('admin.products.toggle-status', $product));
        $this->assertEquals('inactive', $product->fresh()->status);

        $this->actingAs($admin, 'admin')->post(route('admin.products.toggle-featured', $product));
        $this->assertTrue((bool) $product->fresh()->is_featured);

        // Delete product
        $deleteResponse = $this->actingAs($admin, 'admin')->delete(route('admin.products.destroy', $product));
        $deleteResponse->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_astrologer_management_lifecycle(): void
    {
        $admin = $this->admin;

        $this->actingAs($admin, 'admin')->get(route('admin.astrologers.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.astrologers.create'))->assertOk();

        // Create new astrologer profile
        User::factory()->create(['email' => 'panditji@example.com']);
        $storeResponse = $this->actingAs($admin, 'admin')->post(route('admin.astrologers.store'), [
            'display_name' => 'Pandit Rajesh Shastri',
            'email' => 'panditji@example.com',
            'experience_years' => 15,
            'chat_rate' => 30,
            'call_rate' => 45,
            'video_rate' => 60,
            'status' => 'pending',
            'service_ids' => [$this->service->id],
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('astrologers', ['display_name' => 'Pandit Rajesh Shastri']);

        $astrologer = Astrologer::where('display_name', 'Pandit Rajesh Shastri')->first();

        // Show & edit
        $this->actingAs($admin, 'admin')->get(route('admin.astrologers.show', $astrologer))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.astrologers.edit', $astrologer))->assertOk();

        // Update
        $this->actingAs($admin, 'admin')->put(route('admin.astrologers.update', $astrologer), [
            'display_name' => 'Dr. Rajesh Shastri',
            'email' => 'panditji@example.com',
            'experience_years' => 18,
            'chat_rate' => 35,
            'call_rate' => 50,
            'video_rate' => 70,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('astrologers', ['display_name' => 'Dr. Rajesh Shastri']);

        // Approve
        $this->actingAs($admin, 'admin')->post(route('admin.astrologers.approve', $astrologer));
        $this->assertEquals('active', $astrologer->fresh()->status);

        // Reject with reason
        $this->actingAs($admin, 'admin')->post(route('admin.astrologers.reject', $astrologer), [
            'rejection_reason' => 'Invalid credentials documentation.',
        ]);
        $this->assertEquals('rejected', $astrologer->fresh()->status);

        // Toggle featured and availability
        $initialFeatured = (bool) $astrologer->fresh()->is_featured;
        $this->actingAs($admin, 'admin')->post(route('admin.astrologers.toggle-featured', $astrologer));
        $this->assertNotEquals($initialFeatured, (bool) $astrologer->fresh()->is_featured);

        $initialAvailable = (bool) $astrologer->fresh()->is_available;
        $this->actingAs($admin, 'admin')->post(route('admin.astrologers.toggle-availability', $astrologer));
        $this->assertNotEquals($initialAvailable, (bool) $astrologer->fresh()->is_available);

        // Delete
        $this->actingAs($admin, 'admin')->delete(route('admin.astrologers.destroy', $astrologer));
        $this->assertDatabaseMissing('astrologers', ['id' => $astrologer->id]);
    }

    public function test_admin_booking_management_and_slots(): void
    {
        $admin = $this->admin;

        // Slot availability AJAX check
        $this->actingAs($admin, 'admin')
            ->getJson(route('admin.bookings.slots', [
                'astrologer_id' => $this->astrologer->id,
                'date' => today()->addDay()->toDateString(),
                'duration' => 30,
            ]))
            ->assertOk()
            ->assertJsonStructure(['slots']);

        // Create booking for existing user
        $storeResponse = $this->actingAs($admin, 'admin')->post(route('admin.bookings.store'), [
            'user_id' => $this->user->id,
            'astrologer_id' => $this->astrologer->id,
            'service_id' => $this->service->id,
            'booking_date' => today()->addDays(2)->toDateString(),
            'start_time' => '10:00',
            'duration_minutes' => 30,
            'consultation_type' => 'chat',
            'status' => 'pending',
            'payment_status' => 'paid',
            'notes' => 'Admin booking creation test',
        ]);
        $storeResponse->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->user->id,
            'astrologer_id' => $this->astrologer->id,
            'consultation_type' => 'chat',
        ]);

        $booking = Booking::where('user_id', $this->user->id)->first();

        // View booking details
        $this->actingAs($admin, 'admin')->get(route('admin.bookings.show', $booking))->assertOk();

        // Update status to completed
        $updateResponse = $this->actingAs($admin, 'admin')->put(route('admin.bookings.update-status', $booking), [
            'status' => 'completed',
            'payment_status' => 'paid',
            'admin_notes' => 'Consultation successfully delivered.',
        ]);
        $updateResponse->assertRedirect();
        $this->assertEquals('completed', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->completed_at);
    }

    public function test_admin_order_management_and_status_transitions(): void
    {
        $admin = $this->admin;

        $order = Order::create([
            'order_number' => 'ORD-ADM-999',
            'user_id' => $this->user->id,
            'subtotal' => 5000.00,
            'tax_amount' => 0.00,
            'shipping_charge' => 100.00,
            'total_amount' => 5100.00,
            'status' => 'confirmed',
            'payment_status' => 'unpaid',
            'shipping_name' => 'John Doe',
            'shipping_phone' => '9876543210',
            'shipping_address_line1' => '123 Temple Road',
            'shipping_city' => 'Varanasi',
            'shipping_state' => 'UP',
            'shipping_pincode' => '221001',
        ]);

        // View index & details
        $this->actingAs($admin, 'admin')->get(route('admin.orders.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.orders.show', $order))->assertOk();

        // Update shipping status
        $statusResponse = $this->actingAs($admin, 'admin')->put(route('admin.orders.update-status', $order), [
            'status' => 'shipped',
            'carrier_name' => 'BlueDart',
            'tracking_number' => 'BD123456789IN',
            'admin_notes' => 'Dispatched from hub.',
        ]);
        $statusResponse->assertRedirect();
        $this->assertEquals('shipped', $order->fresh()->status);
        $this->assertEquals('BD123456789IN', $order->fresh()->tracking_number);

        // Update payment status
        $paymentResponse = $this->actingAs($admin, 'admin')->put(route('admin.orders.update-payment-status', $order), [
            'payment_status' => 'paid',
            'payment_id' => 'PAY-RZP-8888',
        ]);
        $paymentResponse->assertRedirect();
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('PAY-RZP-8888', $order->fresh()->payment_id);
    }

    public function test_admin_commission_management_approval_and_rejection(): void
    {
        $admin = $this->admin;

        $order = Order::create([
            'order_number' => 'ORD-COMM-101',
            'user_id' => $this->user->id,
            'subtotal' => 2000.00,
            'tax_amount' => 0.00,
            'shipping_charge' => 0.00,
            'total_amount' => 2000.00,
            'status' => 'delivered',
            'payment_status' => 'paid',
            'shipping_name' => 'John Doe',
            'shipping_phone' => '9876543210',
            'shipping_address_line1' => '123 Temple Road',
            'shipping_city' => 'Varanasi',
            'shipping_state' => 'UP',
            'shipping_pincode' => '221001',
        ]);

        $item = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'product_sku' => 'SKU-BLUE-01',
            'quantity' => 1,
            'unit_price' => 2000.00,
            'original_price' => 2000.00,
            'subtotal' => 2000.00,
        ]);

        $commission = Commission::create([
            'commission_number' => 'COM-TEST-001',
            'astrologer_id' => $this->astrologer->id,
            'order_id' => $order->id,
            'order_item_id' => $item->id,
            'product_id' => $this->product->id,
            'order_amount' => 2000.00,
            'commission_type' => 'percentage',
            'commission_rate' => 10.00,
            'commission_amount' => 200.00,
            'status' => Commission::STATUS_PENDING,
        ]);

        // Add pending to wallet
        $this->wallet->update(['pending_balance' => 200.00]);

        // View commissions list and show
        $this->actingAs($admin, 'admin')->get(route('admin.commissions.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.commissions.show', $commission))->assertOk();

        // Approve commission
        $approveResponse = $this->actingAs($admin, 'admin')->post(route('admin.commissions.approve', $commission));
        $approveResponse->assertRedirect();
        $this->assertEquals(Commission::STATUS_APPROVED, $commission->fresh()->status);
        $this->assertNotNull($commission->fresh()->approved_at);

        // Test rejecting a pending commission
        $item2 = OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'product_name' => 'Additional Product',
            'product_sku' => 'SKU-BLUE-02',
            'quantity' => 1,
            'unit_price' => 1000.00,
            'original_price' => 1000.00,
            'subtotal' => 1000.00,
        ]);

        $commission2 = Commission::create([
            'commission_number' => 'COM-TEST-002',
            'astrologer_id' => $this->astrologer->id,
            'order_id' => $order->id,
            'order_item_id' => $item2->id,
            'product_id' => $this->product->id,
            'order_amount' => 1000.00,
            'commission_type' => 'percentage',
            'commission_rate' => 10.00,
            'commission_amount' => 100.00,
            'status' => Commission::STATUS_PENDING,
        ]);

        $rejectResponse = $this->actingAs($admin, 'admin')->post(route('admin.commissions.reject', $commission2), [
            'reason' => 'Customer refunded item.',
        ]);
        $rejectResponse->assertRedirect();
        $this->assertEquals(Commission::STATUS_REJECTED, $commission2->fresh()->status);
    }

    public function test_admin_withdrawal_management_lifecycle(): void
    {
        $admin = $this->admin;

        $withdrawal = Withdrawal::create([
            'withdrawal_number' => 'WTH-TEST-901',
            'astrologer_id' => $this->astrologer->id,
            'wallet_id' => $this->wallet->id,
            'amount' => 200.00,
            'net_amount' => 200.00,
            'fee_amount' => 0.00,
            'payment_method' => 'upi',
            'upi_id' => 'acharya@oksbi',
            'status' => Withdrawal::STATUS_PENDING,
        ]);

        // View index and details
        $this->actingAs($admin, 'admin')->get(route('admin.withdrawals.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.withdrawals.show', $withdrawal))->assertOk();

        // 1. Approve
        $this->actingAs($admin, 'admin')->post(route('admin.withdrawals.approve', $withdrawal), [
            'notes' => 'Approved for payout processing.',
        ])->assertRedirect();
        $this->assertEquals(Withdrawal::STATUS_APPROVED, $withdrawal->fresh()->status);

        // 2. Mark Processing
        $this->actingAs($admin, 'admin')->post(route('admin.withdrawals.processing', $withdrawal))->assertRedirect();
        $this->assertEquals(Withdrawal::STATUS_PROCESSING, $withdrawal->fresh()->status);

        // 3. Mark Paid
        $this->actingAs($admin, 'admin')->post(route('admin.withdrawals.mark-paid', $withdrawal), [
            'transaction_reference' => 'UTR123456789012',
        ])->assertRedirect();
        $this->assertEquals(Withdrawal::STATUS_PAID, $withdrawal->fresh()->status);
        $this->assertEquals('UTR123456789012', $withdrawal->fresh()->transaction_reference);

        // 4. Test Reject flow with held balance release
        $this->wallet->update(['held_balance' => 150.00, 'available_balance' => 350.00]);
        $withdrawalReject = Withdrawal::create([
            'withdrawal_number' => 'WTH-TEST-902',
            'astrologer_id' => $this->astrologer->id,
            'wallet_id' => $this->wallet->id,
            'amount' => 150.00,
            'net_amount' => 150.00,
            'fee_amount' => 0.00,
            'payment_method' => 'bank_transfer',
            'status' => Withdrawal::STATUS_PENDING,
        ]);

        $this->actingAs($admin, 'admin')->post(route('admin.withdrawals.reject', $withdrawalReject), [
            'reason' => 'Invalid bank IFSC code provided.',
        ])->assertRedirect();

        $this->assertEquals(Withdrawal::STATUS_REJECTED, $withdrawalReject->fresh()->status);
        $this->assertEquals(500.00, (float) $this->wallet->fresh()->available_balance);
        $this->assertEquals(0.00, (float) $this->wallet->fresh()->held_balance);
    }

    public function test_admin_wallet_management_and_adjustments(): void
    {
        $admin = $this->admin;

        // View index & transactions
        $this->actingAs($admin, 'admin')->get(route('admin.wallets.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.wallets.transactions'))->assertOk();

        // Manual credit adjustment
        $creditResponse = $this->actingAs($admin, 'admin')->post(route('admin.wallets.adjust', $this->wallet), [
            'action' => 'credit',
            'amount' => 150.00,
            'reason' => 'Goodwill compensation for platform maintenance',
        ]);
        $creditResponse->assertRedirect();
        $this->assertEquals(650.00, (float) $this->wallet->fresh()->available_balance);

        // Manual debit adjustment
        $debitResponse = $this->actingAs($admin, 'admin')->post(route('admin.wallets.adjust', $this->wallet), [
            'action' => 'debit',
            'amount' => 50.00,
            'reason' => 'Correction of duplicate manual bonus',
        ]);
        $debitResponse->assertRedirect();
        $this->assertEquals(600.00, (float) $this->wallet->fresh()->available_balance);
    }

    public function test_admin_referral_management_and_partner_status(): void
    {
        $admin = $this->admin;

        Referral::create([
            'referral_code' => 'ACHARYA10',
            'astrologer_id' => $this->astrologer->id,
            'product_id' => $this->product->id,
            'status' => 'clicked',
            'ip_address' => '127.0.0.1',
            'clicked_at' => now(),
        ]);

        // View referral clicks and partners list
        $this->actingAs($admin, 'admin')->get(route('admin.referrals.index'))->assertOk();
        $this->actingAs($admin, 'admin')->get(route('admin.referrals.partners'))->assertOk();

        // Update partner referral status and code
        $updateResponse = $this->actingAs($admin, 'admin')->post(
            route('admin.referrals.partners.update-status', $this->astrologer),
            [
                'approval_status' => 'approved',
                'referral_code' => 'ACHARYA99',
            ]
        );
        $updateResponse->assertRedirect();
        $this->assertEquals('ACHARYA99', $this->astrologer->fresh()->referral_code);

        // Suspend partner
        $suspendResponse = $this->actingAs($admin, 'admin')->post(
            route('admin.referrals.partners.update-status', $this->astrologer),
            [
                'approval_status' => 'suspended',
                'suspension_reason' => 'Suspicious traffic detected on campaign.',
            ]
        );
        $suspendResponse->assertRedirect();
        $this->assertEquals('suspended', $this->astrologer->fresh()->approval_status);
        $this->assertEquals('Suspicious traffic detected on campaign.', $this->astrologer->fresh()->suspension_reason);
    }

    public function test_admin_platform_settings_management(): void
    {
        $admin = $this->admin;

        // View settings
        $this->actingAs($admin, 'admin')->get(route('admin.settings.index'))->assertOk();

        // Update settings
        $updateResponse = $this->actingAs($admin, 'admin')->post(route('admin.settings.update'), [
            'referral_cookie_days' => 45,
            'referral_attribution_model' => 'last_click',
            'referral_allow_self_referral' => 'false',
            'referral_code_prefix' => 'ASTROVANI',

            'commission_default_type' => 'percent',
            'commission_default_value' => '12.50',
            'commission_hold_days' => 14,
            'commission_base' => 'product_subtotal',

            'withdrawal_min_amount' => '200.00',
            'withdrawal_max_amount' => '75000.00',
            'withdrawal_fee_type' => 'fixed',
            'withdrawal_fee_value' => '10.00',
            'withdrawal_payout_days' => 'Monday,Thursday',

            'fraud_block_same_ip' => 'true',
            'fraud_max_clicks_per_minute' => 30,
        ]);

        $updateResponse->assertRedirect();
        $this->assertEquals('45', Setting::get('referral_cookie_days'));
        $this->assertEquals('ASTROVANI', Setting::get('referral_code_prefix'));
        $this->assertEquals('12.50', Setting::get('commission_default_value'));
        $this->assertEquals('14', Setting::get('commission_hold_days'));
        $this->assertEquals('200.00', Setting::get('withdrawal_min_amount'));
        $this->assertEquals('true', Setting::get('fraud_block_same_ip'));
    }
}
