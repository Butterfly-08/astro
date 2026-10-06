<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_orders_list_renders_payment_status_badges(): void
    {
        $admin = Admin::factory()->create(['status' => 'active']);
        $user = User::factory()->create();

        Order::create([
            'user_id' => $user->id,
            'subtotal' => 999,
            'shipping_charge' => 80,
            'total_amount' => 1079,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '9876543210',
            'shipping_address_line1' => '10 Test Street',
            'shipping_city' => 'Chennai',
            'shipping_state' => 'Tamil Nadu',
            'shipping_pincode' => '600001',
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee('Unpaid')
            ->assertSee('Cod');
    }

    public function test_customer_can_place_order_using_the_current_order_schema(): void
    {
        $user = User::factory()->create();
        $category = ProductCategory::create([
            'name' => 'Checkout Products',
            'slug' => 'checkout-products',
            'status' => 'active',
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-001',
            'price' => 1200,
            'sale_price' => 999,
            'stock' => 5,
            'status' => 'active',
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('checkout.place-order'), [
            'shipping_name' => 'Test Customer',
            'shipping_phone' => '9876543210',
            'shipping_address_line1' => '10 Test Street',
            'shipping_address_line2' => 'North Block',
            'shipping_city' => 'Chennai',
            'shipping_state' => 'Tamil Nadu',
            'shipping_pincode' => '600001',
            'shipping_country' => 'India',
            'payment_method' => 'cod',
        ]);

        $order = Order::firstOrFail();

        $response->assertRedirect(route('checkout.confirmation', $order->id));
        $this->assertMatchesRegularExpression('/^ORD-\d{8}-[A-Z0-9]{8}$/', $order->order_number);
        $this->assertSame('Test Customer', $order->shipping_name);
        $this->assertSame(999.0, $order->subtotal);
        $this->assertSame(80.0, $order->shipping_charge);
        $this->assertSame(1079.0, $order->total_amount);
        $this->assertSame('cod', $order->payment_method);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => 'Test Product',
            'product_sku' => 'TEST-001',
            'unit_price' => 999,
            'original_price' => 1200,
            'quantity' => 1,
        ]);
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->get(route('checkout.confirmation', $order->id))
            ->assertOk()
            ->assertSee('Test Customer')
            ->assertSee('₹1,079.00')
            ->assertSee('10 Test Street');
    }
}
