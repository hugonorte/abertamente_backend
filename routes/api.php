<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    // Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
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
    //Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
});


// ===================================================================
// 🛡️ Rotas Protegidas (Exigem um token de autenticação válido)
// ===================================================================
Route::middleware('auth:api')->group(function () {
    //Route::post('/auth/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
    Route::resource('users', UserController::class);
});
