<?php

namespace Database\Factories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'role' => UserRole::User->value,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * 3. (RECOMENDADO) Adicionar um "state" para criar Admins facilmente.
     * * Agora você pode fazer: User::factory()->admin()->create()
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Admin->value,
        ]);
    }

    /**
     * 4. (RECOMENDADO) Adicionar um "state" para criar Editors facilmente.
     *
     * Agora você pode fazer: User::factory()->editor()->create()
     */
    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::Editor->value,
        ]);
    }
}
