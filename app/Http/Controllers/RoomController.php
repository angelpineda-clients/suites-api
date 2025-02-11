<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\ParseValues;
use App\Models\Image;
use App\Models\Room;
use App\Services\PriceService;
use App\Services\RoomService;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use CloudinaryLabs\CloudinaryLaravel\MediaAlly;
use DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;


class RoomController extends Controller
{
  private const RELATIONS = ['floor', 'size', 'services', 'images'];
  private $STRIPE_KEY;
  protected $priceService;
  protected $roomService;

  use MediaAlly;

  public function __construct(RoomService $roomService, PriceService $priceService)
  {
    $this->roomService = $roomService;
    $this->priceService = $priceService;
    $this->STRIPE_KEY = env('STRIPE_SK_TEST');
  }

  public function store(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required',
      'price' => 'required',
      'images' => 'nullable|array',
      'images.*' => 'image|mimes:jpeg,png,gif|max:2048'
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    $services = $request->input(key: 'services');
    $files = $request->file(key: 'images');

    $isProduct = null;

    $stripe = new \Stripe\StripeClient($this->STRIPE_KEY);

    DB::beginTransaction();
    try {

      $attributes = $request->except(keys: ['services', 'images', 'page', 'per_page']);

      // setup product data with price.
      $productData = [
        'name' => $attributes['name'],
        'description' => isset($attributes['description']) ? $attributes['description'] : $attributes['name'],
        'default_price_data' => ['currency' => 'mxn', 'unit_amount' => ParseValues::priceToCents($attributes['price'])]
      ];

      $isProduct = $stripe->products->create($productData);

      if (!$isProduct) {
        return ApiResponse::error('Error al crear el producto');
      }

      // create room
      $room = Room::create([
        ...$attributes,
        'stripe_product_id' => $isProduct->id,
        'stripe_default_price_id' => $isProduct->default_price
      ]);

      // create default price
      $isDefaultPrice = $this->priceService->createDefault($attributes['price'], $room->id, product: $isProduct);

      // sync services
      if (isset($services)) {
        $room->services()->sync($services);
      }

      // upload images
      if (isset($files)) {

        foreach ($files as $file) {
          $cloudinary_result = Cloudinary::upload(file: $file->getRealPath());

          if ($cloudinary_result) {
            $image = new Image();
            $image->url = $cloudinary_result->getSecurePath();
            $image->public_id = $cloudinary_result->getPublicId();
            $image->save();

            $room->images()->attach($image->id);
          }
        }
      }

      DB::commit();

      $query = Room::query()->where('active', true)->with(relations: self::RELATIONS);
      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return ApiResponse::success(data: $data, message: "", code: Response::HTTP_CREATED);

    } catch (\Exception $e) {
      DB::rollBack();

      if ($isProduct) {
        $stripe->products->update($isProduct->id, ['active' => false]);
      }

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index(Request $request)
  {
    $page = $request->input(key: 'page', default: 1);
    $search = $request->input(key: 'search', default: '');
    $column = $request->input(key: 'column', default: 'name');
    $per_page = $request->input(key: 'per_page', default: 10);

    try {

      $query = Room::query()->with(relations: self::RELATIONS);

      if ($search) {
        $query->where(column: $column, operator: 'like', value: '%' . $search . '%');
      }

      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return ApiResponse::success(data: $data);

    } catch (\Exception $e) {
      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(string $id)
  {

    try {
      $data = Room::where(column: 'id', operator: $id)->with(relations: self::RELATIONS)->first();

      return ApiResponse::success(data: $data);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, string $id)
  {

    $stripe = new \Stripe\StripeClient($this->STRIPE_KEY);

    DB::beginTransaction();
    try {
      $room = Room::findOrFail(id: $id);

      $attributes = $request->except(keys: 'services');
      $services = $request->input(key: 'services');

      $room->update(attributes: $attributes);

      if (isset($services)) {
        $room->services()->sync($services);
      }

      $wasUpdated = $stripe->products->update($room['stripe_product_id'], [
        'name' => $room['name'],
        'description' => $room['description']
      ]);


      DB::commit();
      $query = Room::query()->where('id', $id)->with(relations: self::RELATIONS)->first();

      return ApiResponse::success(data: $query);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      DB::rollBack();
      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function delete(Request $request, string $id)
  {

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    $stripe = new \Stripe\StripeClient($this->STRIPE_KEY);

    DB::beginTransaction();
    try {
      $room = Room::findOrFail(id: $id);

      $room->delete();

      $wasDeleted = $stripe->products->update($room['stripe_product_id'], [
        'active' => false
      ]);

      DB::commit();
      $query = Room::query()->where('active', true)->with(relations: self::RELATIONS);
      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return ApiResponse::success($data);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      DB::rollBack();

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function search(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'check_in' => 'required|date',
      'check_out' => 'required|date',
      'adults' => 'required|numeric',
      'children' => 'numeric'
    ]);

    if ($validator->fails()) {
      return ApiResponse::error('Validator error', $validator->errors());
    }

    $checkIn = $request->input('check_in');
    $checkOut = $request->input('check_out');
    $adults = $request->input('adults');
    $children = $request->input('children', 0);
    $page = $request->query('page', 1);
    $pageSize = $request->query('per_page', 10);

    $people = $adults + $children;

    try {
      $rooms = $this->roomService->searchRoomAvailability($people, $checkIn, $checkOut)->with(self::RELATIONS);

      $data = $this->paginateData(query: $rooms, page: $page, perPage: $pageSize);

      return ApiResponse::success($data);
    } catch (\Exception $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    }
  }

  public function getPricesWithSeason(string $id)
  {
    try {
      $prices = Room::where('id', $id)->select('id', 'name', 'price')->with([
        'prices' => function ($query) {
          $query->select('id', 'room_id', 'amount', 'season_id');
        },
        'prices.season' => function ($query) {
          $query->select('id', 'name', 'alias', 'initial_date', 'final_date');
        }
      ])->first();

      return ApiResponse::success($prices);


    } catch (\Exception $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    }
  }
}
