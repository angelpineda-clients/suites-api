<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class ServiceController extends Controller
{
  public function store(Request $request)
  {

    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required'
    ]);

    if ($validator->fails()) {
      return response()->json(data: [
        'error' => 'Validation Error',
        'message' => $validator->errors()
      ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    try {
      $service = Service::create(attributes: $request->all());

      if ($service) {
        $all_services = Service::all();

        return response()->json(data: [
          'response' => [
            "status" => true,
            "data" => $all_services
          ]
        ], status: Response::HTTP_CREATED);
      }

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function index()
  {
    try {
      $all_services = Service::all();

      return response()->json(data: [
        'response' => [
          "status" => true,
          "data" => $all_services
        ]
      ], status: Response::HTTP_OK);

    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $service = Service::findOrFail(id: $id);

      if ($service) {
        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $service
          ]
        ], status: Response::HTTP_OK);
      }

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (service)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function update(Request $request, string $id)
  {
    $validator = Validator::make(data: $request->all(), rules: [
      'name' => 'string|required'
    ]);

    if ($validator->fails()) {
      return response()->json(data: [
        'error' => 'Validation Error',
        'message' => $validator->errors()
      ], status: Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    try {

      $service = Service::findOrFail($id);

      if ($service) {
        $service->update($request->all());

        $all_services = Service::all();

        return response()->json(data: [
          'response' => [
            "status" => true,
            "data" => $all_services
          ]
        ], status: Response::HTTP_OK);
      }


    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function delete(Request $request, string $id)
  {

    try {

      $service = Service::findOrFail($id);

      if ($service) {
        $service->delete();

        $all_services = Service::all();

        return response()->json(data: [
          'response' => [
            'status' => true,
            'data' => $all_services
          ]
        ], status: Response::HTTP_OK);
      }

    } catch (ModelNotFoundException $e) {
      return response()->json(data: [
        'error' => 'Resource not found (service)',
        'message' => $e->getMessage()
      ], status: Response::HTTP_NOT_FOUND);
    } catch (\Exception $e) {
      return response()->json(data: [
        'error' => 'Not expected error',
        'message' => $e->getMessage()
      ], status: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
