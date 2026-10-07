<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add commission/referral fields to the existing products table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('referral_enabled')
                  ->default(true)
                  ->after('is_featured');

            // commission_type: percentage | fixed
            $table->enum('commission_type', ['percentage', 'fixed'])
                  ->nullable()
                  ->after('referral_enabled');

            // e.g. 10.00 for 10% or 150.00 for ₹150 fixed
            $table->decimal('commission_value', 10, 2)
                  ->nullable()
                  ->after('commission_type');

            // optional cap: max commission per item regardless of commission_value
            $table->decimal('commission_cap', 10, 2)
                  ->nullable()
                  ->after('commission_value');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'referral_enabled',
                'commission_type',
                'commission_value',
                'commission_cap',
            ]);
        });
    }
};
