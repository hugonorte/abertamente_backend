<?php

namespace Database\Factories;

use App\Models\Footnote;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Footnote>
 */
class FootnoteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $post = Post::factory()->create();

        return [
            'description' => $this->faker->sentence(10),
            'post_id' => $post->getKey(),
        ];
    }
}
