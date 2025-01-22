<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Price;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class PriceController extends Controller
{
  public function store(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'amount' => 'required',
      'room_id' => 'required',
      'season_id' => 'required',
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    $roomID = $request->input('room_id');
    $seasonID = $request->input('season_id');
    $amount = $request->input('amount');

    try {
      $price = Price::create([
        'amount' => $amount,
        'room_id' => $roomID,
        'season_id' => $seasonID
      ]);

      if ($price) {
        $query = Price::query()->where('room_id', $roomID)->with(relations: ['season']);

        $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

        return ApiResponse::success(data: $data, message: "", code: Response::HTTP_CREATED);
      }

    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index(Request $request)
  {

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    $roomID = $request->input('room_id', null);

    try {

      $query = Price::query()->with(relations: ['season']);

      if (isset($roomID)) {
        $query->where(column: 'room_id', operator: $roomID);
      }

      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);
      return ApiResponse::success(data: $data, message: "");

    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(Request $request, string $id)
  {

    try {

      $price = Price::findOrFail(id: $id)->with(relations: ['season']);

      return ApiResponse::success(data: $price, message: "");

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, string $id)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'amount' => 'required',
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    $amount = $request->input(key: 'amount');

    try {

      $price = Price::findOrFail(id: $id);

      if ($price) {
        $price->update([
          'amount' => $amount
        ]);
      }

      $query = Price::query()->where(column: 'room_id', operator: $price->room_id)->with(relations: ['season']);
      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return ApiResponse::success(data: $data, message: "");

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function delete(Request $request, string $id)
  {

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);


    try {

      $price = Price::findOrFail(id: $id);
      $roomID = $price->room_id;

      if ($price) {
        $price->delete();
      }

      $query = Price::query()->where(column: 'room_id', operator: $roomID)->with(relations: ['season']);
      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return ApiResponse::success(data: $data, message: "");

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
