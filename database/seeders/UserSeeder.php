<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create default admin user
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@company.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'two_factor_enabled' => false,
            'is_active' => true,
            'last_activity_at' => now(),
        ]);

        // Create sample IT staff user
        User::create([
            'name' => 'IT Staff Member',
            'email' => 'itstaff@company.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'),
            'role' => 'it_staff',
            'is_active' => true,
            'last_activity_at' => now(),
        ]);
    }
}