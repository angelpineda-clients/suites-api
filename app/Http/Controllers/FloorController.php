<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class FloorController extends Controller
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
      $floor = Floor::create(attributes: $request->all());

      if ($floor) {
        $all_floors = Floor::all();
        return response()->json(data: [
          'response' => [
            'data' => $all_floors,
            'status' => true
          ]
        ], status: Response::HTTP_CREATED);
      }

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (floor)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index()
  {
    try {
      $all_floors = Floor::all();

      return response()->json(data: [
        'response' => [
          'data' => $all_floors,
          'status' => true,
        ]
      ], status: Response::HTTP_OK);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (floor)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $floor = Floor::findOrFail(id: $id);

      return response()->json(data: [
        'response' => [
          'status' => true,
          'data' => $floor
        ]
      ], status: Response::HTTP_OK);

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (floor)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (floor)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);

    }
  }

  public function update(Request $request, string $id)
  {
    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required',
      'alias' => 'string'
    ]);

    if ($validator->fails()) {
      return response()->json(data: [
        'error' => 'Validation Error',
        'message' => $validator->errors()
      ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    try {

      $floor = Floor::findOrFail(id: $id);

      if ($floor) {
        $floor->update($request->all());

        $all_floors = Floor::all();

        return response()->json(data: [
          'response' => [
            'data' => $all_floors,
            'status' => true
          ]
        ], status: Response::HTTP_OK);
      }

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (floor)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function delete(Request $request, string $id)
  {

    try {

      $floor = Floor::findOrFail(id: $id);

      if ($floor) {
        $floor->delete();

        $all_floors = Floor::all();

        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $all_floors
          ]
        ], status: Response::HTTP_OK);
      }

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (floor)',
      ], status: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error (floor)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
