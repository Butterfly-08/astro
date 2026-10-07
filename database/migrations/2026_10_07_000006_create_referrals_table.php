<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Referral click-tracking table.
 * Every referral link click (and eventual conversion) is stored here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            // Who owns this referral
            $table->foreignId('astrologer_id')
                  ->constrained('astrologers')
                  ->cascadeOnDelete();

            $table->string('referral_code', 20)->index();

            // Customer info (may be null for anonymous clicks)
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->string('session_id', 128)->nullable()->index();

            // The product that was linked (null = general referral link)
            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete();

            // Set when a successful order is placed
            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained('orders')
                  ->nullOnDelete();

            // Tracking metadata
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('converted_at')->nullable();

            $table->enum('status', [
                'clicked',
                'converted',
                'cancelled',
                'expired',
                'invalid',
            ])->default('clicked')->index();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('astrologer_id');
            $table->index('customer_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
