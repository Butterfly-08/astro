<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add commission snapshot fields to the existing order_items table.
 * Commission data is snapshotted at order time to prevent historical drift.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('commission_type', ['percentage', 'fixed'])
                  ->nullable()
                  ->after('subtotal');

            $table->decimal('commission_value', 10, 2)
                  ->nullable()
                  ->after('commission_type');

            $table->decimal('commission_amount', 12, 2)
                  ->default(0)
                  ->after('commission_value');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'commission_type',
                'commission_value',
                'commission_amount',
            ]);
        });
    }
};
