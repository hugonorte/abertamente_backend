<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BibliographicReferenceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FootnoteController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    // Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

//Route::resource('users', UserController::class);
// ===================================================================
// 🚪 Rota Públicas (Não exigem autenticação)
// ===================================================================
Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::resource('author', AuthorController::class);
    Route::resource('category', CategoryController::class);
    Route::get('post-summary', [PostController::class, 'postSummary']);
    Route::resource('post', PostController::class);
    Route::resource('bibliographic_reference', BibliographicReferenceController::class);
    Route::get('bibliographic_reference/post/{post}', [BibliographicReferenceController::class, 'showByPostId']);
    Route::get('footnote/post/{post}', [FootnoteController::class, 'showByPostId']);
    Route::resource('footnote', FootnoteController::class);
    Route::resource('user', UserController::class);

    //Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
});


// ===================================================================
// 🛡️ Rotas Protegidas (Exigem um token de autenticação válido)
// ===================================================================
Route::middleware('auth:api')->group(function () {
    //Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
