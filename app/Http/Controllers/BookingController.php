<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Validator;

class BookingController extends Controller
{

  protected $bookingService;

  public function __construct(BookingService $bookingService)
  {
    $this->bookingService = $bookingService;
  }

  public function store(Request $request)
  {
    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'required',
      'last_name' => 'required',
      'email' => 'required|email',
      'phone_number' => 'required',
      'check_in' => 'required|date',
      'check_out' => 'required|date',
      'room_id' => 'required',
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    /**
     * validar rango de fechas
     * crear payment_intent
     * crear booking
     * regresar client_secret
     */

    $roomID = $request->input('room_id');
    $initialDate = $request->input('check_in');
    $finalDate = $request->input('check_out');

    try {

      // todo: create service

      $querySearchBooking = Booking::query()
        ->where('room_id', $roomID);

      $overlap = $this->bookingService->checkOverlap($querySearchBooking, $initialDate, $finalDate);



      if ($overlap) {
        return ApiResponse::error('Duplicated booking');
      }

      $booking = Booking::create($request->all());

      return ApiResponse::success(data: $booking, message: 'Todo ok');
    } catch (\Exception $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    }
  }
}
