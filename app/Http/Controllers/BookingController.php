<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Helpers\ApiResponse;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use App\Services\SeasonService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class BookingController extends Controller
{
  private const RELATIONS = ['payment'];
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
      'check_in' => 'required|date', // TODO: should be equal or greater than today.
      'check_out' => 'required|date',
      'room_id' => 'required',
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $roomID = $request->input(key: 'room_id');
    $startDate = $request->input(key: 'check_in');
    $endDate = $request->input(key: 'check_out');

    DB::beginTransaction();

    try {

      $querySearchBooking = Booking::query()->where(column: 'room_id', operator: $roomID);


      $overlap = $this->bookingService->checkOverlap(query: $querySearchBooking, startDate: $startDate, endDate: $endDate);

      if ($overlap) {
        return ApiResponse::error(message: 'Duplicated booking dates');
      }

      $room = Room::findOrFail(id: $roomID);

      $total = $this->bookingService->roomPricesBySeason(roomId: $room->id, initialDate: $startDate, finalDate: $endDate, basePrice: $room->price);

      $booking = Booking::create(attributes: $request->all());

      $payment = new PaymentController();

      $paymentObject = $payment->store(amount: $total, bookingID: $booking->id);

      if (!$paymentObject['success']) {
        return ApiResponse::error('Unexpected error', $paymentObject['error']);
      }

      DB::commit();
      return ApiResponse::success(data: $paymentObject['payment_info']);

    } catch (\Exception $e) {

      DB::rollBack();

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index(Request $request)
  {
    $page = $request->query(key: 'page', default: 1);
    $perPage = $request->query(key: 'per_page', default: 10);
    $status = $request->query(key: 'status', default: '');
    $name = $request->query(key: 'name', default: '');
    $lastName = $request->query(key: 'last_name', default: '');

    try {

      $query = Booking::query()->with(relations: self::RELATIONS);

      if ($status != '' && $name != '' && $lastName != '') {
        $query = Booking::query()->where(column: [
          ['status', $status],
          ['name', 'like', '%' . $name . '%'],
          ['last_name', 'like', '%' . $lastName . '%']
        ])->with(relations: self::RELATIONS);
      } else if ($status == '' && $name != '' || $lastName != '') {
        $query = Booking::query()->where([
          ['name', 'like', '%' . $name . '%'],
          ['last_name', 'like', '%' . $lastName . '%']
        ])->with(relations: self::RELATIONS);
      } else if ($status != '') {
        $query = Booking::query()->where('status', $status)->with(relations: self::RELATIONS);
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
      $booking = Booking::where('id', $id)->with(relations: self::RELATIONS)->firstOrFail();

      return ApiResponse::success(data: $booking);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, string $id)
  {
    try {
      $booking = Booking::findOrFail(id: $id);

      $data = $request->except('check_in', 'check_out', 'room_id', 'status');

      $booking->update($data);


      return ApiResponse::success(data: $booking);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function updateStatus(Request $request, string $id)
  {

    $status = $request->input(key: 'status');

    try {
      $booking = Booking::findOrFail(id: $id);


      $booking->update([
        'status' => $status
      ]);

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

      $booking->delete();

      return ApiResponse::success(['booking deleted']);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function unavailableDates(Request $request, string $roomID)
  {

    $today = Carbon::today();

    try {
      $bookingsByRoom = Booking::where('room_id', $roomID)->where('check_in', '>=', $today)->orWhere(function ($query) use ($today, $roomID) {
        $query->where('check_in', '<=', $today)->where('check_out', '>=', $today)
          ->where('room_id', $roomID);
      })->get();

      // rest a day for checkout
      foreach ($bookingsByRoom as $booking) {
        $checkOut = new Carbon($booking->check_out);
        $booking->check_out = $checkOut->subDay()->toDateString();
      }

      $takenDates = [];

      foreach ($bookingsByRoom as $booking) {
        $period = CarbonPeriod::create(
          $booking->check_in,
          $booking->check_out
        );

        foreach ($period as $date) {
          array_push($takenDates, $date->toDateString());
        }
      }

      sort($takenDates);

      return response()->json($takenDates);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

}
