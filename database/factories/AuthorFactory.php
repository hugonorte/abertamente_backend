<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'bio' => fake()->jobTitle(),
            'main_title' => fake()->jobTitle(),
            'preferred_social_network' => 'Twitter',
            'preferred_social_network_username' => '@'. fake()->name()
        ];
    }
}
