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

      return response()->json([
        'data' => $floor
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function index()
  {
    try {
      $floors = Floor::all();

      return response()->json([
        'data' => $floors
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
        'data' => $floor
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

      $floor->update($request->all());

      return response()->json([
        'data' => $floor
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function delete(Request $request, string $id)
  {

    try {

      $floor = Floor::findOrFail($id);

      $floor->delete();

      return response()->json([
        'data' => $floor->name . ' deleted'
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
