<?php

namespace Database\Factories;

use App\Models\Author;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->unique()->slug(),
            'tldr' => $this->faker->sentence(10),
            'content' => $this->faker->paragraphs(3, true),
            'image_path' => '/images/' . $this->faker->word() . '.jpg',
            'author_id' => Author::factory(),
            'category_id' => Category::factory(),
            'published_at' => $this->faker->dateTimeBetween('-1 years'),
            'status' => $this->faker->randomElement(['published', 'draft']),
        ];
    }
}
