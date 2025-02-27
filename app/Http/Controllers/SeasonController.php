<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Season;
use App\Services\SeasonService;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class SeasonController extends Controller
{

  protected $seasonService;

  public function __construct(SeasonService $seasonService)
  {
    $this->seasonService = $seasonService;
  }

  public function store(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required',
      'alias' => 'string|nullable',
      'initial_date' => 'required|date|before:final_date',
      'final_date' => 'required|date|after:initial_date',
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $initial_date = $request->input('initial_date');
    $final_date = $request->input('final_date');
    // pagination
    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    try {

      $query = Season::query();

      $overlap = $this->seasonService->checkOverlap($query, $initial_date, $final_date);

      if ($overlap) {
        return ApiResponse::error(message: 'Duplicated date', errors: "One or more dates has conflict with a season.", code: Response::HTTP_CONFLICT);
      }

      $season = Season::create(attributes: $request->all());

      if ($season) {

        $query = Season::query();


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
    $search = $request->input(key: 'search', default: '');
    $column = $request->input(key: 'column', default: 'name');
    $per_page = $request->input(key: 'per_page', default: 10);

    try {
      $query = Season::query();

      if ($search) {
        $query->where(column: $column, operator: 'like', value: '%' . $search . '%');
      }

      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return ApiResponse::success(data: $data);

    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $data = Season::findOrFail(id: $id);

      return ApiResponse::success(data: $data);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found ', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, string $id)
  {
    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required',
      'alias' => 'string|nullable',
      'initial_date' => 'required|date|before:final_date',
      'final_date' => 'required|date|after:initial_date',
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $initial_date = $request->input('initial_date');
    $final_date = $request->input('final_date');
    // pagination
    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    try {

      $query = Season::query()->where(column: 'id', operator: "!=", value: $id);

      $overlap = $this->seasonService->checkOverlap($query, $initial_date, $final_date);

      if ($overlap) {
        return ApiResponse::error(message: 'Duplicated date', errors: "One or more dates has conflict with a season.", code: Response::HTTP_CONFLICT);
      }

      $season = Season::findOrFail(id: $id);

      if ($season) {
        $query = Season::query();
        $season->update($request->all());

        $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

        return ApiResponse::success(data: $data);
      }

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function delete(Request $request, string $id)
  {
    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    try {

      $season = Season::findOrFail(id: $id);

      if ($season) {
        $season->delete();

        $query = Season::query();
        $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

        return ApiResponse::success(data: $data);
      }

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function allDates(Request $request)
  {
    $seasonID = $request->query(key: 'season_id');
    try {
      $seasons = [];

      if (isset($seasonID)) {
        $seasons = Season::query()->where('id', '!=', $seasonID)->get();
      } else {
        $seasons = Season::all();
      }

      $takenDates = [];

      foreach ($seasons as $season) {
        $period = CarbonPeriod::create(
          $season->initial_date,
          $season->final_date
        );

        foreach ($period as $date) {
          array_push($takenDates, $date->toDateString());
        }
      }

      sort($takenDates);

      return response()->json($takenDates);
    } catch (\Exception $e) {
      return ApiResponse::error(message: 'Not expected error ', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
