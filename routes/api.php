<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/register', [AuthController::class, 'register']);
//Route::post('/login', [AuthController::class, 'login']);

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);
});
