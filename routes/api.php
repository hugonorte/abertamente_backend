<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BibliographicReferenceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FootnoteController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
// Removido 'use Illuminate\Support\Facades\Request;' pois não está sendo usado
use Illuminate\Support\Facades\Route;

// ===================================================================
// 🚪 Rota Públicas (Não exigem autenticação)
// ===================================================================
Route::group([
    'middleware' => 'api',
], function ($router) {

    // Autenticação
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Recursos públicos
    Route::resource('author', AuthorController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('post', PostController::class);
    Route::resource('bibliographic_reference', BibliographicReferenceController::class);
    Route::resource('footnote', FootnoteController::class);
    Route::resource('user', UserController::class);

    Route::get('/posts', function () {
        $posts = [
            ['id' => 1, 'title' => 'Post 1: Nuxt e Laravel funcionam!'],
            ['id' => 2, 'title' => 'Post 2: Configurando o CORS'],
            ['id' => 3, 'title' => 'Post 3: Sucesso na Integração!'],
        ];
        return response()->json($posts);
    });
});


// ===================================================================
// 🛡️ Rotas Protegidas (Exigem um token de autenticação válido)
// ===================================================================
Route::group([
    'middleware' => 'auth:api',
    // 'prefix' => 'auth' // Você pode descomentar isso se quiser que as rotas sejam /auth/logout, /auth/me, etc.
], function ($router) {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
