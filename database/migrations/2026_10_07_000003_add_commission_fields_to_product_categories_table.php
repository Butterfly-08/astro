<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add commission fields to the product_categories table.
 * Priority: product commission > category commission > global default.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->enum('commission_type', ['percentage', 'fixed'])
                  ->nullable()
                  ->after('is_featured');

            $table->decimal('commission_value', 10, 2)
                  ->nullable()
                  ->after('commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value']);
        });
    }
};
