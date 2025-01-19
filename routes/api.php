<?php

use App\Contexts\User\Infrastructure\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    // Rutas públicas para gestión de usuarios
    Route::post('/users', [UserController::class, 'store']);

    // Rutas User privadas
    Route::middleware('header-auth')->group(function () {
        Route::get('/users/search', [UserController::class, 'search']);
        Route::get('/users/{id}', [UserController::class, 'show']);
        //Route::put('/users/{id}', [UserController::class, 'update']);
        //Route::delete('/users/{id}', [UserController::class, 'destroy']);
        Route::post('/users/{target_user_id}/follow', [UserController::class, 'follow']);
        Route::delete('/users/{target_user_id}/unfollow', [UserController::class, 'unfollow']);
    });
});
