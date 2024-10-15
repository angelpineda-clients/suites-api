<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::post('/register', [AuthController::class, 'register']);
//Route::post('/login', [AuthController::class, 'login']);

Route::get('service', [ServiceController::class, 'index']);
Route::get('service/{id}', [ServiceController::class, 'show']);

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
});
