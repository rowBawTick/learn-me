<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Chris',
            'email' => 'chris@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_tutor' => true,
        ]);

        // Create tutor user
        User::create([
            'name' => 'Master Tutor',
            'email' => 'tutor@example.com',
            'password' => Hash::make('password'),
            'is_tutor' => true,
            'is_admin' => false,
        ]);

        // Create a regular users
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'is_tutor' => false,
            'is_admin' => false,
        ]);

        // Create additional random users
        User::factory(5)->create();
    }
}
