<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Withdrawal requests table.
 * Every withdrawal goes through: pending → approved → processing → paid
 * or pending → rejected (with fund release).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();

            // Human-readable: WD-20261007-000001
            $table->string('withdrawal_number', 30)->unique();

            $table->foreignId('astrologer_id')
                  ->constrained('astrologers')
                  ->cascadeOnDelete();

            $table->foreignId('wallet_id')
                  ->constrained('wallets')
                  ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);           // requested amount
            $table->decimal('fee', 10, 2)->default(0);  // platform fee
            $table->decimal('net_amount', 12, 2);       // amount - fee

            // Payment details
            $table->enum('payment_method', ['upi', 'bank_transfer'])->default('upi');

            // UPI
            $table->string('upi_id', 100)->nullable();

            // Bank
            $table->string('account_holder_name', 120)->nullable();
            $table->string('account_number_masked', 20)->nullable(); // e.g. XXXXXX1234
            $table->string('account_number_encrypted')->nullable();  // encrypted full number
            $table->string('bank_name', 100)->nullable();
            $table->string('ifsc_code', 15)->nullable();

            // Status flow
            $table->enum('status', [
                'pending',
                'under_review',
                'approved',
                'processing',
                'paid',
                'rejected',
                'cancelled',
                'failed',
            ])->default('pending')->index();

            // Timestamps for each stage
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->text('rejection_reason')->nullable();

            // Payout reference (UTR, transaction ref)
            $table->string('transaction_reference', 100)->nullable();

            // Admin who handled this withdrawal
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('admins')
                  ->nullOnDelete();

            $table->text('admin_notes')->nullable();

            $table->timestamps();

            $table->index('astrologer_id');
            $table->index(['status', 'requested_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
    }
};
