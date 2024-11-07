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

    try {
      $season = Season::create(attributes: $request->all());

      if ($season) {

        $all_seasons = Season::all();

        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $all_seasons
          ]
        ], status: Response::HTTP_CREATED);
      }

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expeted error (season)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index()
  {
    try {
      $all_seasons = Season::all();

      if ($all_seasons) {
        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $all_seasons
          ]
        ], status: Response::HTTP_OK);
      }

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
      $season = Season::findOrFail(id: $id);

      if ($season) {
        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $season
          ]
        ]);
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

      $season = Season::findOrFail(id: $id);

      if ($season) {
        $season->update($request->all());

        $all_seasons = Season::all();

        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $all_seasons
          ]
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

    try {

      $season = Season::findOrFail($id);

      if ($season) {
        $season->delete();

        $all_seasons = Season::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_seasons
          ]
        ]);
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
