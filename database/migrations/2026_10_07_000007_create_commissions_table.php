<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Commission records — one per order_item when a referral exists.
 * Statuses: pending → available → paid | rejected | cancelled
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();

            // Human-readable reference: COM-20261007-000001
            $table->string('commission_number', 30)->unique();

            $table->foreignId('astrologer_id')
                  ->constrained('astrologers')
                  ->cascadeOnDelete();

            // The customer who made the purchase
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            $table->foreignId('order_item_id')
                  ->constrained('order_items')
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->nullable()
                  ->constrained('products')
                  ->nullOnDelete();

            // Link back to the referral click record
            $table->foreignId('referral_id')
                  ->nullable()
                  ->constrained('referrals')
                  ->nullOnDelete();

            // Snapshot of rates at commission creation time
            $table->enum('commission_type', ['percentage', 'fixed']);
            $table->decimal('commission_rate', 10, 2);   // actual rate used
            $table->decimal('order_amount', 12, 2);       // commissionable amount
            $table->decimal('commission_amount', 12, 2);  // final commission in INR

            // Status flow
            $table->enum('status', [
                'pending',
                'approved',
                'available',
                'rejected',
                'cancelled',
                'paid',
            ])->default('pending')->index();

            // When commission becomes withdrawable (after hold period)
            $table->timestamp('available_at')->nullable()->index();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();

            $table->text('rejection_reason')->nullable();

            // Admin who took action
            $table->foreignId('action_by')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();

            $table->index(['astrologer_id', 'status']);
            $table->index(['order_id', 'order_item_id']);

            // Prevent duplicate commission for the same order_item
            $table->unique(['order_item_id', 'astrologer_id'], 'uq_commission_item_astrologer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
