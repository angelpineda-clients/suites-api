<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SizeController;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/register', [AuthController::class, 'register']);
//Route::post('/login', [AuthController::class, 'login']);

Route::get('service', [ServiceController::class, 'index']);
Route::get('service/{id}', [ServiceController::class, 'show']);

Route::get('size', [SizeController::class, 'index']);
Route::get('size/{id}', [SizeController::class, 'show']);

Route::get('floor', [FloorController::class, 'index']);
Route::get('floor/{id}', [FloorController::class, 'show']);

Route::get('season', [SeasonController::class, 'index']);
Route::get('season/{id}', [SeasonController::class, 'show']);

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('role', [RoleController::class, 'index'])->middleware('role:manager');
    Route::get('role/{role}', [RoleController::class, 'show'])->middleware('role:manager');
    Route::post('role', [RoleController::class, 'create'])->middleware('role:manager');
    Route::put('role/{role}', [RoleController::class, 'update'])->middleware('role:manager');
    Route::delete('role/{role}', [RoleController::class, 'delete'])->middleware('role:manager');

    
    Route::post('service', [ServiceController::class, 'store'])->middleware(['role:manager|admin']);
    Route::put('service/{id}', [ServiceController::class, 'update'])->middleware(['role:manager|admin']);;
    Route::delete('service/{id}', [ServiceController::class, 'delete'])->middleware(['role:manager|admin']);

    Route::post('size', [SizeController::class, 'store'])->middleware(['role:manager|admin']);
    Route::put('size/{id}', [SizeController::class, 'update'])->middleware(['role:manager|admin']);
    Route::delete('size/{id}', [SizeController::class, 'delete'])->middleware(['role:manager|admin']);

    Route::post('floor', [FloorController::class, 'store'])->middleware(['role:manager|admin']);
    Route::put('floor/{id}', [FloorController::class, 'update'])->middleware(['role:manager|admin']);
    Route::delete('floor/{id}', [FloorController::class, 'delete'])->middleware(['role:manager|admin']);

    Route::post('season', [SeasonController::class, 'store'])->middleware(['role:manager|admin']);
    Route::put('season/{id}', [SeasonController::class, 'update'])->middleware(['role:manager|admin']);
    Route::delete('season/{id}', [SeasonController::class, 'delete'])->middleware(['role:manager|admin']);
});
