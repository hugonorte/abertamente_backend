<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedUserRoleValue = env('SEED_USER_ROLE', UserRole::User->value);
        $role = UserRole::tryFrom($seedUserRoleValue);
        if (!$role) {
            $role = UserRole::User;
        }
        $factory = User::factory();

        if ($role === UserRole::Admin) {
            $factory = $factory->admin();
        } elseif ($role === UserRole::Editor) {
            $factory = $factory->editor();
        }

        $userData = [
            'first_name' => env('SEED_USER_FIRST_NAME', 'Admin'),
            'last_name' => env('SEED_USER_LAST_NAME', 'User'),
            'email' => env('SEED_USER_EMAIL', 'admin@example.com'),
            'password' => env('SEED_USER_PASSWORD')
        ];

        $factory->create($userData);
    }
}
