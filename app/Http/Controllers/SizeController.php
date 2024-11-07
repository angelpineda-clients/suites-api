<?php

namespace App\Http\Controllers;

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
      return response()->json(data: [
        'error' => 'Validation Error',
        'message' => $validator->errors()
      ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    try {
      $size = Size::create(attributes: $request->all());

      if ($size) {

        $all_sizes = Size::all();

        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $all_sizes
          ]
        ], status: Response::HTTP_CREATED);
      }

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (size)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index()
  {
    try {
      $all_sizes = Size::all();

      return response()->json(data: [
        'response' => [
          'status' => true,
          'data' => $all_sizes
        ]
      ], status: Response::HTTP_OK);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (size)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $size = Size::findOrFail(id: $id);

      return response()->json([
        'response' => [
          'response' => true,
          'data' => $size
        ]
      ]);

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (size)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (size)',
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

    try {

      $size = Size::findOrFail(id: $id);

      if ($size) {
        $size->update($request->all());

        $all_sizes = Size::all();

        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $all_sizes
          ]
        ], status: Response::HTTP_OK);
      }

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (size)'
      ], status: Response::HTTP_NOT_FOUND);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (size)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);

    }
  }

  public function delete(Request $request, string $id)
  {

    try {

      $size = Size::findOrFail(id: $id);

      if ($size) {

        $size->delete();

        $all_sizes = Size::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_sizes
          ]
        ]);
      }

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (size)'
      ], status: Response::HTTP_NOT_FOUND);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (size)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);

    }
  }
}
