<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductEcommerceTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected ProductCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = Admin::factory()->create(['status' => 'active']);

        $this->category = ProductCategory::create([
            'name'        => 'Certified Gemstones',
            'slug'        => 'gemstones',
            'description' => '100% natural, untreated, lab-certified planetary gemstones.',
            'status'      => 'active',
            'sort_order'  => 1,
            'is_featured' => true,
        ]);
    }

    public function test_public_can_view_shop_index_page(): void
    {
        $product = Product::create([
            'category_id'       => $this->category->id,
            'name'              => 'Natural Yellow Sapphire',
            'slug'              => 'natural-yellow-sapphire',
            'sku'               => 'AV-GEM-001',
            'price'             => 14500.00,
            'sale_price'        => 12999.00,
            'stock'             => 10,
            'status'            => 'active',
            'is_featured'       => true,
            'short_description' => 'Genuine Pukhraj stone for Jupiter blessings.',
        ]);

        $response = $this->get(route('shop.index'));

        $response->assertStatus(200);
        $response->assertSee('Natural Yellow Sapphire');
        $response->assertSee('Certified Gemstones');
    }

    public function test_public_can_filter_products_by_category(): void
    {
        $rudrakshaCategory = ProductCategory::create([
            'name'        => 'Sacred Rudraksha',
            'slug'        => 'rudraksha',
            'description' => 'Himalayan Rudraksha beads.',
            'status'      => 'active',
        ]);

        $gemstone = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Natural Blue Sapphire',
            'slug'        => 'natural-blue-sapphire',
            'price'       => 18000.00,
            'stock'       => 5,
            'status'      => 'active',
        ]);

        $rudraksha = Product::create([
            'category_id' => $rudrakshaCategory->id,
            'name'        => 'Five Mukhi Rudraksha Mala',
            'slug'        => 'five-mukhi-rudraksha-mala',
            'price'       => 1500.00,
            'stock'       => 25,
            'status'      => 'active',
        ]);

        // Filter by gemstones
        $response = $this->get(route('shop.index', ['category' => 'gemstones']));
        $response->assertStatus(200);
        $response->assertSee('Natural Blue Sapphire');
        $response->assertDontSee('Five Mukhi Rudraksha Mala');

        // Direct category route
        $catResponse = $this->get(route('shop.category', 'rudraksha'));
        $catResponse->assertStatus(200);
        $catResponse->assertSee('Five Mukhi Rudraksha Mala');
        $catResponse->assertDontSee('Natural Blue Sapphire');
    }

    public function test_public_can_search_products(): void
    {
        $productMatch = Product::create([
            'category_id'       => $this->category->id,
            'name'              => 'Energized Sri Yantra Copper Plate',
            'slug'              => 'energized-sri-yantra-copper-plate',
            'price'             => 2100.00,
            'stock'             => 12,
            'status'            => 'active',
            'short_description' => 'Consecrated sacred geometry for prosperity.',
        ]);

        $otherProduct = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Red Coral Moonga Ring',
            'slug'        => 'red-coral-moonga-ring',
            'price'       => 8500.00,
            'stock'       => 8,
            'status'      => 'active',
        ]);

        $response = $this->get(route('shop.index', ['search' => 'Yantra']));
        $response->assertStatus(200);
        $response->assertSee('Energized Sri Yantra Copper Plate');
        $response->assertDontSee('Red Coral Moonga Ring');
    }

    public function test_public_can_view_product_detail_page(): void
    {
        $product = Product::create([
            'category_id'       => $this->category->id,
            'name'              => 'Natural Emerald Gemstone',
            'slug'              => 'natural-emerald-gemstone',
            'sku'               => 'AV-GEM-003',
            'price'             => 9500.00,
            'sale_price'        => 8499.00,
            'stock'             => 6,
            'status'            => 'active',
            'description'       => 'Flawless green panna gemstone with lab certification.',
            'short_description' => 'Mercury planet stone.',
        ]);

        $response = $this->get(route('shop.product.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('Natural Emerald Gemstone');
        $response->assertSee('8,499');
        $response->assertSee('Add to Cart');
    }

    public function test_public_cannot_view_inactive_product(): void
    {
        $inactive = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Draft Hidden Product',
            'slug'        => 'draft-hidden-product',
            'price'       => 500.00,
            'stock'       => 10,
            'status'      => 'inactive',
        ]);

        $response = $this->get(route('shop.product.show', $inactive->slug));
        $response->assertStatus(404);
    }

    public function test_admin_can_view_products_management_page(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.products.index'));
        $response->assertStatus(200);
        $response->assertSee('Product Catalog');
    }

    public function test_admin_can_create_new_product_with_validation(): void
    {
        $payload = [
            'category_id'       => $this->category->id,
            'name'              => 'Seven Chakra Healing Bracelet',
            'sku'               => 'AV-BRAC-007',
            'price'             => 899.00,
            'sale_price'        => 699.00,
            'stock'             => 40,
            'status'            => 'active',
            'is_featured'       => 1,
            'short_description' => 'Authentic volcanic lava stone and chakra crystals.',
            'description'       => 'Align all seven energy centers with this natural stone bracelet.',
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.store'), $payload);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'name'        => 'Seven Chakra Healing Bracelet',
            'category_id' => $this->category->id,
            'price'       => 899.00,
            'sale_price'  => 699.00,
            'stock'       => 40,
            'status'      => 'active',
        ]);
    }

    public function test_admin_can_update_product_and_toggle_status(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Initial Product Name',
            'slug'        => 'initial-product-name',
            'price'       => 1200.00,
            'stock'       => 15,
            'status'      => 'active',
            'is_featured' => false,
        ]);

        // Toggle featured
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.toggle-featured', $product));
        $this->assertTrue($product->fresh()->is_featured);

        // Toggle status
        $this->actingAs($this->admin, 'admin')
            ->post(route('admin.products.toggle-status', $product));
        $this->assertEquals('inactive', $product->fresh()->status);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Product To Delete',
            'slug'        => 'product-to-delete',
            'price'       => 750.00,
            'stock'       => 5,
            'status'      => 'active',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_manage_categories(): void
    {
        // 1. View categories
        $this->actingAs($this->admin, 'admin')
            ->get(route('admin.categories.index'))
            ->assertStatus(200)
            ->assertSee('Certified Gemstones');

        // 2. Create category
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.categories.store'), [
                'name'        => 'Spiritual Incense & Dhoop',
                'description' => 'Pure natural organic herbs and temple fragrances.',
                'icon'        => 'bi bi-fire',
                'sort_order'  => 6,
                'status'      => 'active',
                'is_featured' => 1,
            ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Spiritual Incense & Dhoop',
        ]);
    }
}
