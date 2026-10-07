<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit log — immutable record of all significant admin/financial actions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // Who performed the action (nullable = system action)
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('user_type', 30)->nullable(); // 'admin' | 'astrologer' | 'customer'

            $table->string('action', 100)->index(); // e.g. 'withdrawal.approved'
            $table->string('description', 500)->nullable();

            // The model that was affected
            $table->string('model_type', 100)->nullable(); // e.g. 'App\Models\Withdrawal'
            $table->unsignedBigInteger('model_id')->nullable();

            // Snapshot of changes
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Request context
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent()->index();

            // Audit logs are immutable — no updated_at needed
            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
