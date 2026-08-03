<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GithubWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Definir um secret falso para o teste
        Config::set('services.github.webhook_secret', 'my_super_secret');
    }

    public function test_rejects_webhook_without_secret()
    {
        $response = $this->postJson('/api/webhooks/github/deploy-status', [
            'post_id' => 1,
            'build_status' => 'success',
            'desired_status' => 'published',
        ]);

        $response->assertStatus(401);
    }

    public function test_rejects_webhook_with_invalid_secret()
    {
        $response = $this->postJson('/api/webhooks/github/deploy-status', [
            'post_id' => 1,
            'build_status' => 'success',
            'desired_status' => 'published',
        ], [
            'X-Webhook-Secret' => 'wrong_secret'
        ]);

        $response->assertStatus(401);
    }

    public function test_accepts_webhook_and_updates_post_status_on_success()
    {
        $post = Post::factory()->create([
            'status' => PostStatus::PUBLISHING
        ]);

        $response = $this->postJson('/api/webhooks/github/deploy-status', [
            'post_id' => $post->id,
            'build_status' => 'success',
            'desired_status' => 'published',
        ], [
            'X-Webhook-Secret' => 'my_super_secret'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'published'
        ]);
    }

    public function test_accepts_webhook_and_updates_post_status_on_error()
    {
        $post = Post::factory()->create([
            'status' => PostStatus::PUBLISHING
        ]);

        $response = $this->postJson('/api/webhooks/github/deploy-status', [
            'post_id' => $post->id,
            'build_status' => 'error',
            'desired_status' => 'published',
        ], [
            'X-Webhook-Secret' => 'my_super_secret'
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'status' => 'error'
        ]);
    }
}
