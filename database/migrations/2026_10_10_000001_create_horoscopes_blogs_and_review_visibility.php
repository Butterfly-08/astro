<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horoscopes', function (Blueprint $table) {
            $table->id();
            $table->string('zodiac_sign', 20);
            $table->date('horoscope_date');
            $table->string('title', 160);
            $table->text('content');
            $table->enum('status', ['draft', 'published'])->default('draft')->index();
            $table->timestamps();

            $table->unique(['zodiac_sign', 'horoscope_date']);
        });

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::table('astrologer_reviews', function (Blueprint $table) {
            $table->boolean('is_visible')->default(true)->index();
        });
    }

    public function down(): void
    {
        Schema::table('astrologer_reviews', function (Blueprint $table) {
            $table->dropColumn('is_visible');
        });

        Schema::dropIfExists('blogs');
        Schema::dropIfExists('horoscopes');
    }
};
