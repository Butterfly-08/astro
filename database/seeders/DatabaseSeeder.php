<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed Administrator
        $this->call(AdminSeeder::class);

        // Seed Sample Customer for testing
        User::updateOrCreate(
            ['email' => 'user@astrovani.test'],
            [
                'first_name' => 'Rahul',
                'last_name' => 'Sharma',
                'phone' => '+91 9123456780',
                'password' => Hash::make('Password@123'),
                'date_of_birth' => '1995-08-15',
                'gender' => 'male',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'country' => 'India',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Phase 2: Services & Astrologers
        $this->call(ServiceSeeder::class);
        $this->call(AstrologerSeeder::class);

        // Phase 3: Bookings
        $this->call(BookingSeeder::class);

        // Phase 4: E-Commerce Product Categories & Products
        $this->call(ProductCategorySeeder::class);
        $this->call(ProductSeeder::class);
    }
}
