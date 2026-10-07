<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add referral attribution fields to the existing orders table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Referral attribution
            $table->string('referral_code', 20)
                  ->nullable()
                  ->after('admin_notes')
                  ->index();

            $table->unsignedBigInteger('referrer_astrologer_id')
                  ->nullable()
                  ->after('referral_code')
                  ->index();

            $table->foreign('referrer_astrologer_id')
                  ->references('id')
                  ->on('astrologers')
                  ->nullOnDelete();

            // Commission lifecycle status for this order
            $table->enum('commission_status', [
                'none',
                'pending',
                'available',
                'cancelled',
                'paid',
            ])->default('none')->after('referrer_astrologer_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['referrer_astrologer_id']);
            $table->dropColumn([
                'referral_code',
                'referrer_astrologer_id',
                'commission_status',
            ]);
        });
    }
};
