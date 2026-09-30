<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->enum('type', ['percent', 'flat'])->default('percent');
            $table->decimal('value', 8, 2);                          // % or ₹ amount
            $table->decimal('min_order_value', 10, 2)->default(0);  // min cart total to apply
            $table->decimal('max_discount', 10, 2)->nullable();      // cap for percent coupons
            $table->unsignedInteger('usage_limit')->nullable();      // total allowed uses
            $table->unsignedInteger('used_count')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
