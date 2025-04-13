<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at'=>now(),
            'role' => 'admin',
            'password' => Hash::make('Pass@123'),
        ]);

        User::factory()->create([
            'name' => 'Student',
            'email'=> 'student@gmail.com',
            'email_verified_at'=>now(),
            'role' => 'student',
            'password' => Hash::make('itrustgod'),

        ]);

        User::factory()->create([
            'name' => 'teacher',
            'email' => 'teacher@gmail.com',
            'email_verified_at'=>now(),
            'role' => 'teacher',
            'password' => Hash::make('teacher123'),

        ]);
    }
}
