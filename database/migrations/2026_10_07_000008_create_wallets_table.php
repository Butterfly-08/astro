<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Astrologer wallets — one wallet per astrologer.
 * Balances are maintained here for performance.
 * wallet_transactions is the authoritative ledger.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('astrologer_id')
                  ->unique()  // one wallet per astrologer
                  ->constrained('astrologers')
                  ->cascadeOnDelete();

            // Available = can be withdrawn right now
            $table->decimal('available_balance', 14, 2)->default(0);

            // Pending = commission not yet past hold period
            $table->decimal('pending_balance', 14, 2)->default(0);

            // Held = amount locked during an active withdrawal request
            $table->decimal('held_balance', 14, 2)->default(0);

            // Historical totals
            $table->decimal('lifetime_earnings', 14, 2)->default(0);
            $table->decimal('lifetime_withdrawn', 14, 2)->default(0);

            $table->string('currency', 3)->default('INR');

            $table->enum('status', ['active', 'suspended', 'frozen'])->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
