<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class SeasonController extends Controller
{
  public function store(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required',
      'alias' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json(data: [
        'error' => 'Validation Error',
        'message' => $validator->errors()
      ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    try {
      $season = Season::create(attributes: $request->all());

      if ($season) {

        $query = Season::query();

        $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

        return response()->json(data: [
          $data
        ], status: Response::HTTP_CREATED);
      }

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expeted error (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
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

      return response()->json(data: [
        $data
      ], status: Response::HTTP_OK);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expeted error (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $data = Season::findOrFail(id: $id);

      return response()->json(data: [
        $data
      ], status: Response::HTTP_OK);

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expeted error (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, string $id)
  {
    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required',
      'alias' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return response()->json(data: [
        'error' => 'Validation Error',
        'message' => $validator->errors()
      ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    try {

      $season = Season::findOrFail(id: $id);

      if ($season) {
        $season->update($request->all());

        $query = Season::query();

        $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

        return response()->json(data: [
          $data
        ], status: Response::HTTP_OK);
      }

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expeted error (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
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

        return response()->json(data: [
          $data
        ], status: Response::HTTP_OK);
      }

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expeted error (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
