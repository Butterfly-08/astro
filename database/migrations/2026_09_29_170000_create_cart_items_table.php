<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->nullable()->index();   // guest cart
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // logged-in cart
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->timestamps();

            // Prevent duplicate entries per session/user + product
            $table->unique(['session_id', 'product_id']);
            $table->unique(['user_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
