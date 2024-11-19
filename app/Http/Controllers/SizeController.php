<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Size;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class SizeController extends Controller
{
  public function store(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'required|string|max:255',
      'alias' => 'nullable|string'
    ]);

    if ($validator->fails()) {
      return ApiResponse::error(message: 'Validation error', errors: $validator->errors());
    }

    $page = $request->input(key: 'page', default: 1);
    $per_page = $request->input(key: 'per_page', default: 10);

    try {
      $size = Size::create(attributes: $request->all());

      if ($size) {

        $query = Size::query();

        $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

        return ApiResponse::success(data: $data, message: '', code: Response::HTTP_CREATED);
      }

    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index(Request $request)
  {

    $page = $request->input(key: 'page', default: 1);
    $search = $request->input(key: 'search', default: '');
    $column = $request->input(key: 'column', default: 'name');
    $per_page = $request->input(key: 'per_page', default: 10);

    try {
      $query = Size::query();

      if ($search) {
        $query->where(column: $column, operator: 'like', value: '%' . $search . '%');
      }

      $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

      return ApiResponse::success(data: $data);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $data = Size::findOrFail(id: $id);

      return ApiResponse::success(data: $data);

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
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

      $size = Size::findOrFail(id: $id);

      if ($size) {
        $size->update($request->all());

        $query = Size::query();

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

      $size = Size::findOrFail(id: $id);

      if ($size) {

        $size->delete();

        $query = Size::query();

        $data = $this->paginateData(query: $query, perPage: $per_page, page: $page);

        return ApiResponse::success(data: $data);
      }

    } catch (ModelNotFoundException $e) {

      return ApiResponse::error(message: 'Resource not found', errors: $e->getMessage(), code: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Not expected error', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
