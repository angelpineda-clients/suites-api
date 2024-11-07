<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use Illuminate\Http\Request;

class FloorController extends Controller
{
  public function store(Request $request)
  {

    $validated = $request->validate([
      'name' => 'string|required',
      'alias' => 'nullable|string'
    ]);

    try {
      $floor = Floor::create($request->all());

      if ($floor) {
        $all_floors = Floor::all();
        return response()->json([
          'response' => [
            'data' => $all_floors,
            'status' => true
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
      $all_floors = Floor::all();

      return response()->json([
        'response' => [
          'data' => $all_floors,
          'status' => true,
        ]
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $floor = Floor::findOrFail($id);

      return response()->json([
        'response' => [
          'status' => true,
          'data' => $floor
        ]
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function update(Request $request, string $id)
  {
    $validated = $request->validate([
      'name' => 'string|required',
      'alias' => 'string'
    ]);

    try {

      $floor = Floor::findOrFail($id);

      if ($floor) {
        $floor->update($request->all());

        $all_floors = Floor::all();

        return response()->json([
          'response' => [
            'data' => $all_floors,
            'status' => true
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

      $floor = Floor::findOrFail($id);

      if ($floor) {
        $floor->delete();

        $all_floors = Floor::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_floors
          ]
        ]);
      }

    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
