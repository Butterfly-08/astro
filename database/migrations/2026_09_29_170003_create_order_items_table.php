<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
            $table->string('product_name');          // snapshot at time of order
            $table->string('product_sku', 50);
            $table->unsignedSmallInteger('quantity');
            $table->decimal('unit_price', 10, 2);    // effective price at time of purchase
            $table->decimal('original_price', 10, 2); // regular price (for discount display)
            $table->decimal('subtotal', 10, 2);       // unit_price * quantity
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
