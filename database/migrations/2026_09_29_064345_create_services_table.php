<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('slug')->unique();
            $table->string('icon', 60)->nullable()->comment('Bootstrap icon class e.g. bi-stars');
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->enum('type', ['consultation', 'product', 'both'])->default('consultation');
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
