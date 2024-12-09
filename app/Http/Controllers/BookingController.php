<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Helpers\ApiResponse;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * //validar rango de fechas
     * //crear booking
     * crear payment_intent
     * regresar client_secret
     */

    $roomID = $request->input(key: 'room_id');
    $initialDate = $request->input(key: 'check_in');
    $finalDate = $request->input(key: 'check_out');

    try {

      // todo: agregar rollbacks

      $querySearchBooking = Booking::query()->where(column: 'room_id', operator: $roomID);

      $overlap = $this->bookingService->checkOverlap(query: $querySearchBooking, initialDate: $initialDate, finalDate: $finalDate);

      if ($overlap) {
        return ApiResponse::error('Duplicated booking dates');
      }

      $booking = Booking::create($request->all());

      return ApiResponse::success(data: $booking, message: 'Todo ok');
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index(Request $request)
  {
    $page = $request->query(key: 'page', default: 1);
    $perPage = $request->query(key: 'per_page', default: 10);
    $status = $request->query(key: 'status', default: "0");
    $search = $request->query(key: 'search', default: '');

    try {

      $query = Booking::query()->where(column: 'status', operator: $status);

      if ($search) {
        $query->where(column: 'name', operator: $search);
      }

      $data = $this->paginateData(query: $query, perPage: $perPage, page: $page);

      return ApiResponse::success(data: $data);

    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(string $id)
  {
    try {
      $booking = Booking::findOrFail($id);

      return ApiResponse::success($booking);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(string $id)
  {
    try {
      $booking = Booking::findOrFail(id: $id);

      return ApiResponse::success(data: $booking);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function delete(string $id)
  {
    try {
      $booking = Booking::findOrFail($id);

      return ApiResponse::success($booking);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

}
