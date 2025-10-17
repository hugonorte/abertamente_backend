<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Exception;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // /**
    //  * Create a new AuthController instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login']]);
    // }

    /**
     * Get a JWT via given credentials.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        $refreshToken = $request->cookie('refresh_token');

        if (!$refreshToken) {
            return response()->json(['error' => 'Refresh token not found'], 401);
        }


        try {
            Cookie::forget('refresh_token');
            $payload = JWTAuth::getJWTProvider()->decode($refreshToken);

            // Verificar se é um refresh token
            if ($payload['type'] !== 'refresh') {
                return response()->json(['error' => 'Invalid token type'], 401);
            }

            // Buscar usuário
            $user = User::find($payload['sub']);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            // Gerar novo access token com claim única
            $newAccessToken = auth('api')
                ->claims([
                    'jti' => (string) Str::uuid(), // identificador único
                    'refreshed_at' => microtime(true) // opcional: ajuda no debug
                ])
                ->login($user);

            // Gerar novo refresh token também (boa prática)
            $newRefreshToken = $this->generateRefreshToken();

            return response()->json([
                'access_token' => $newAccessToken,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ])->withCookie($this->refreshCookie($newRefreshToken));

        } catch (Exception $e) {
            return response()->json(['error' => 'Invalid refresh token'], 401);
        }
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        // Gera o access token (JWT padrão)
        $accessToken = $token;

        // Gera o refresh token (pode ser um JWT com TTL maior ou um token customizado)
        $refreshToken = $this->generateRefreshToken();

        return response()->json([
            'access_token' => $accessToken,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ])->withCookie($this->refreshCookie($refreshToken));
    }

    /**
     * Gera um refresh token único
     */
    private function generateRefreshToken()
    {
        $userId = auth('api')->user()->id;

        $refreshPayload = [
            'sub' => $userId,
            'type' => 'refresh',
            'jti' => (string) Str::uuid(), // sempre único
            'exp' => time() + (60 * 60 * 24 * 7) // 7 dias
        ];

        return JWTAuth::getJWTProvider()->encode($refreshPayload);
    }

    private function refreshCookie($refreshToken): \Symfony\Component\HttpFoundation\Cookie
    {
        return Cookie::make(
            'refresh_token',
            $refreshToken,
            config('jwt.refresh_ttl'), // TTL em minutos
            '/', // path
            null, // domain
            false, // secure (HTTPS only)
            false, // httpOnly
            false, // raw
            'lax' // sameSite
        );
    }
}
