<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Wallet ledger / audit trail.
 * Every credit, debit, or adjustment is recorded here.
 * NEVER modify a wallet balance without creating a ledger entry.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('wallet_id')
                  ->constrained('wallets')
                  ->cascadeOnDelete();

            $table->foreignId('astrologer_id')
                  ->constrained('astrologers')
                  ->cascadeOnDelete();

            $table->enum('type', [
                'commission_credit',     // commission becomes available → credited
                'commission_reversal',   // refund/cancel reverses a credit
                'withdrawal_hold',       // funds held when withdrawal requested
                'withdrawal_release',    // hold released when withdrawal rejected
                'withdrawal_paid',       // hold finalised when withdrawal paid
                'bonus_credit',          // admin manually adds bonus
                'manual_adjustment',     // admin manual add/subtract
                'refund_adjustment',     // partial refund adjustment
            ])->index();

            // Reference to the related model (commission, withdrawal, etc.)
            $table->string('reference_type', 60)->nullable(); // e.g. "Commission"
            $table->unsignedBigInteger('reference_id')->nullable();

            // Unique reference prevents double-crediting
            $table->string('reference_key', 80)->unique();   // e.g. COMMISSION-123

            $table->decimal('amount', 14, 2);                // always positive
            $table->decimal('balance_before', 14, 2);        // available_balance before
            $table->decimal('balance_after', 14, 2);         // available_balance after

            $table->text('description')->nullable();

            $table->enum('status', ['completed', 'failed', 'reversed'])->default('completed');

            // Admin who triggered manual entries
            $table->foreignId('admin_id')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();

            $table->index(['wallet_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
