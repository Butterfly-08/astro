<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@astrovani.test'],
            [
                'name' => 'AstroVani Master Admin',
                'password' => Hash::make(env('ADMIN_DEV_PASSWORD', 'Password@123')),
                'phone' => '+91 9876543210',
                'status' => 'active',
                'last_login_at' => now(),
            ]
        );
    }
}
