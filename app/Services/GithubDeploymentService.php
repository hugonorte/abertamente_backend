<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GithubDeploymentService
{
    /**
     * Dispara um evento repository_dispatch para o repositório do frontend
     * no GitHub Actions.
     *
     * @param int $postId
     * @param string $desiredStatus
     * @return bool Retorna verdadeiro se a requisição foi bem-sucedida
     */
    public function triggerFrontendDeployment(int $postId, string $desiredStatus): bool
    {
        $token = config('services.github.post_status_webhook_token');
        
        if (empty($token)) {
            Log::error('GitHub Deployment falhou: POST_STATUS_WEBHOOK_GITHUB_TOKEN não está definido.');
            return false;
        }

        $owner = 'hugonorte';
        $repo = 'abertamente_frontend';
        $url = "https://api.github.com/repos/{$owner}/{$repo}/dispatches";

        $response = Http::withHeaders([
            'Accept'               => 'application/vnd.github.v3+json',
            'Authorization'        => "token {$token}",
            'X-GitHub-Api-Version' => '2022-11-28',
        ])->post($url, [
            'event_type' => 'post_status_changed',
            'client_payload' => [
                'post_id' => $postId,
                'desired_status' => $desiredStatus,
            ],
        ]);

        if ($response->successful()) {
            Log::info("GitHub Deployment acionado com sucesso para o Post ID {$postId}.");
            return true;
        }

        Log::error("Falha ao acionar GitHub Deployment para o Post ID {$postId}.", [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return false;
    }
}
