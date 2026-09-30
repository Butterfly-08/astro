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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number', 32)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('astrologer_id')->constrained('astrologers')->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('duration_minutes')->default(30);
            
            $table->enum('consultation_type', ['chat', 'call', 'video', 'in_person'])->default('chat');
            $table->decimal('rate_per_minute', 8, 2)->default(0.00);
            $table->decimal('amount', 10, 2)->default(0.00);
            
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'rejected'])->default('confirmed');
            
            $table->text('notes')->nullable()->comment('Customer birth details, concerns or questions');
            $table->text('admin_notes')->nullable()->comment('Internal administrator notes');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();

            // Indexes for fast querying and double-booking conflict detection
            $table->index(['astrologer_id', 'booking_date', 'start_time'], 'idx_astro_date_start');
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['booking_date', 'status'], 'idx_date_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
