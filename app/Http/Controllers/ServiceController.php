<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
  public function store(Request $request)
  {

    $validated = $request->validate([
      'name' => 'string|required'
    ]);

    try {
      $service = Service::create($request->all());
      $all_services = Service::all();

      if ($service) {
        return response()->json([
          'response' => [
            "status" => true,
            "data" => $all_services
          ]
        ]);
      }



    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function index()
  {
    try {
      $all_services = Service::all();

      return response()->json([
        'response' => [
          "status" => true,
          "data" => $all_services
        ]
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $service = Service::findOrFail($id);

      return response()->json([
        'response' => $service
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function update(Request $request, string $id)
  {
    $validated = $request->validate([
      'name' => 'string|required'
    ]);

    try {

      $service = Service::findOrFail($id);

      if ($service) {
        $service->update($request->all());

        $all_services = Service::all();

        return response()->json([
          'response' => [
            "status" => true,
            "data" => $all_services
          ]
        ]);
      }


    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function delete(Request $request, string $id)
  {

    try {

      $service = Service::findOrFail($id);

      if ($service) {
        $service->delete();

        $all_services = Service::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_services
          ]
        ]);
      }

    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
