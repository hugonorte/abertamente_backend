<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GithubWebhookController extends Controller
{
    /**
     * Handle the incoming webhook from GitHub Actions.
     */
    public function handle(Request $request): JsonResponse
    {
        $secret = config('services.github.webhook_secret', env('GITHUB_WEBHOOK_SECRET'));

        // Autenticação simples através de token no Header
        $providedSecret = $request->header('X-Webhook-Secret') ?? $request->input('secret');

        if (empty($secret) || $secret !== $providedSecret) {
            Log::warning('Tentativa de acesso não autorizado ao webhook do GitHub.', [
                'ip' => $request->ip()
            ]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'build_status' => 'required|string|in:success,error',
            'desired_status' => 'required|string', // ex: 'published', 'unpublished', etc.
        ]);

        $post = Post::findOrFail($validated['post_id']);

        if ($validated['build_status'] === 'success') {
            $post->status = PostStatus::tryFrom($validated['desired_status']) ?? PostStatus::ERROR;
        } else {
            $post->status = PostStatus::ERROR;
        }

        $post->save();

        Log::info("Webhook recebido: Status do Post ID {$post->id} atualizado para {$post->status->value}");

        return response()->json(['message' => 'Post status updated successfully', 'post' => $post]);
    }
}
