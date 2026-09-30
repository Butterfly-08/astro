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
        Schema::create('astrologers', function (Blueprint $table) {
            $table->id();
            $table->string('display_name', 120);
            $table->string('email')->unique();
            $table->string('phone', 25)->nullable();
            $table->string('slug')->unique();
            $table->string('profile_image')->nullable();
            $table->text('bio')->nullable();
            $table->text('short_bio')->nullable();

            // Professional Details
            $table->string('specializations')->nullable()->comment('Comma-separated: vedic,tarot,numerology');
            $table->string('languages')->nullable()->comment('Comma-separated: Hindi,English');
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('education')->nullable();

            // Consultation Rates (per minute in INR)
            $table->decimal('chat_rate', 8, 2)->default(0);
            $table->decimal('call_rate', 8, 2)->default(0);
            $table->decimal('video_rate', 8, 2)->default(0);

            // Stats (denormalized for performance)
            $table->decimal('rating_avg', 3, 2)->default(0.00);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedInteger('total_consultations')->default(0);

            // Status & Admin Control
            $table->enum('status', ['pending', 'active', 'inactive', 'rejected'])->default('pending')->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_available')->default(false);
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('astrologers');
    }
};
