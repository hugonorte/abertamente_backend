<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => env('SEED_USER_FIRST_NAME'),
            'last_name' => env('SEED_USER_LAST_NAME'),
            'role' => env('SEED_USER_ROLE'),
            'email' => env('SEED_USER_EMAIL'),
            'password' => env('SEED_USER_PASSWORD')
        ]);
    }
}
