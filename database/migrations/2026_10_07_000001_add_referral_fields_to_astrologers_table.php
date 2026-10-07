<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add referral-system fields to the existing astrologers table.
 * We use addColumn so the original migration is untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('astrologers', function (Blueprint $table) {
            // Link to users table for authentication
            $table->foreignId('user_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('users')
                  ->nullOnDelete();

            // Referral code — unique, uppercase, e.g. ASTRO7F92
            $table->string('referral_code', 20)
                  ->nullable()
                  ->unique()
                  ->after('user_id');

            // Approval workflow
            $table->enum('approval_status', ['pending', 'approved', 'rejected', 'suspended'])
                  ->default('pending')
                  ->after('status')
                  ->index();

            $table->text('suspension_reason')->nullable()->after('approval_status');

            $table->index('referral_code');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('astrologers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'referral_code',
                'approval_status',
                'suspension_reason',
            ]);
        });
    }
};
