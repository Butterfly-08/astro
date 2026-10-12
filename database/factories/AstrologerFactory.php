<?php

namespace Database\Factories;

use App\Models\Astrologer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Astrologer>
 */
class AstrologerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        return [
            'display_name'    => $name,
            'email'           => fake()->unique()->safeEmail(),
            'phone'           => fake()->phoneNumber(),
            'slug'            => fake()->unique()->slug(),
            'status'          => 'active',
            'approval_status' => Astrologer::APPROVAL_APPROVED,
            'chat_rate'       => 20.00,
            'call_rate'       => 30.00,
            'video_rate'      => 40.00,
            'rating_avg'      => 4.8,
            'total_reviews'   => 12,
            'is_available'    => true,
        ];
    }
}
