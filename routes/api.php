<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SeasonController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SizeController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('refresh', [AuthController::class, 'refresh']);

Route::get('room', [RoomController::class, 'index']);
Route::get('search', [RoomController::class, 'search']);
Route::get('room/{id}', [RoomController::class, 'show']);

Route::get('image', [ImageController::class, 'index']);
Route::get('image/{id}', [ImageController::class, 'show']);

Route::post('booking', [BookingController::class, 'store']);
Route::get('booking-unavailable-dates/{roomID}', [BookingController::class, 'unavailableDates']);

Route::group([
  'middleware' => 'api',
], function ($router) {
  Route::post('me', [AuthController::class, 'me']);
  Route::post('login', [AuthController::class, 'login']);
  Route::post('logout', [AuthController::class, 'logout']);

  Route::get('role', [RoleController::class, 'index'])->middleware('role:manager');
  Route::get('role/{role}', [RoleController::class, 'show'])->middleware('role:manager');
  Route::post('role', [RoleController::class, 'create'])->middleware('role:manager');
  Route::put('role/{role}', [RoleController::class, 'update'])->middleware('role:manager');
  Route::delete('role/{role}', [RoleController::class, 'delete'])->middleware('role:manager');

  Route::get('service', [ServiceController::class, 'index'])->middleware(['role:manager|admin']);
  Route::get('service/{id}', [ServiceController::class, 'show'])->middleware(['role:manager|admin']);
  Route::post('service', [ServiceController::class, 'store'])->middleware(['role:manager|admin']);
  Route::put('service/{id}', [ServiceController::class, 'update'])->middleware(['role:manager|admin']);
  ;
  Route::delete('service/{id}', [ServiceController::class, 'delete'])->middleware(['role:manager|admin']);

  Route::get('size', [SizeController::class, 'index'])->middleware(['role:manager|admin']);
  Route::get('size/{id}', [SizeController::class, 'show'])->middleware(['role:manager|admin']);
  Route::post('size', [SizeController::class, 'store'])->middleware(['role:manager|admin']);
  Route::put('size/{id}', [SizeController::class, 'update'])->middleware(['role:manager|admin']);
  Route::delete('size/{id}', [SizeController::class, 'delete'])->middleware(['role:manager|admin']);

  Route::get('floor', [FloorController::class, 'index'])->middleware(['role:manager|admin']);
  Route::get('floor/{id}', [FloorController::class, 'show'])->middleware(['role:manager|admin']);
  Route::post('floor', [FloorController::class, 'store'])->middleware(['role:manager|admin']);
  Route::put('floor/{id}', [FloorController::class, 'update'])->middleware(['role:manager|admin']);
  Route::delete('floor/{id}', [FloorController::class, 'delete'])->middleware(['role:manager|admin']);

  Route::get('season', [SeasonController::class, 'index'])->middleware(['role:manager|admin']);
  Route::get('season/{id}', [SeasonController::class, 'show'])->middleware(['role:manager|admin']);
  Route::post('season', [SeasonController::class, 'store'])->middleware(['role:manager|admin']);
  Route::put('season/{id}', [SeasonController::class, 'update'])->middleware(['role:manager|admin']);
  Route::delete('season/{id}', [SeasonController::class, 'delete'])->middleware(['role:manager|admin']);

  Route::post('room', [RoomController::class, 'store'])->middleware(['role:manager|admin']);
  Route::put('room/{id}', [RoomController::class, 'update'])->middleware(['role:manager|admin']);
  Route::delete('room/{id}', [RoomController::class, 'delete'])->middleware(['role:manager|admin']);

  Route::post('image', [ImageController::class, 'store'])->middleware(['role:manager|admin']);
  Route::put('image/{id}', [ImageController::class, 'update'])->middleware(['role:manager|admin']);
  Route::delete('image/{id}', [ImageController::class, 'delete'])->middleware(['role:manager|admin']);

  Route::get('price', [PriceController::class, 'index'])->middleware(['role:manager|admin']);
  Route::get('price/{id}', [PriceController::class, 'show'])->middleware(['role:manager|admin']);
  Route::post('price', [PriceController::class, 'store'])->middleware(['role:manager|admin']);
  Route::put('price/{id}', [PriceController::class, 'update'])->middleware(['role:manager|admin']);
  Route::delete('price/{id}', [PriceController::class, 'delete'])->middleware(['role:manager|admin']);


  Route::get('booking', [BookingController::class, 'index'])->middleware(['role:manager|admin']);
  Route::get('booking/{id}', [BookingController::class, 'show'])->middleware(['role:manager|admin']);
  Route::put('booking/{id}', [BookingController::class, 'update'])->middleware(['role:manager|admin']);
  Route::put('booking-status/{id}', [BookingController::class, 'updatestatus'])->middleware(['role:manager|admin']);
  Route::delete('booking/{id}', [BookingController::class, 'delete'])->middleware(['role:manager|admin']);

});
