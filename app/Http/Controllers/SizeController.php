<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
  public function store(Request $request)
  {

    $validated = $request->validate([
      'name' => 'string|required',
      'alias' => 'nullable|string'
    ]);

    try {
      $size = Size::create($request->all());

      if ($size) {

        $all_sizes = Size::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_sizes
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
      $all_sizes = Size::all();

      return response()->json([
        'response' => [
          'status' => true,
          'data' => $all_sizes
        ]
      ]);

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $size = Size::findOrFail($id);

      return response()->json([
        'response' => [
          'response' => true,
          'data' => $size
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

      $size = Size::findOrFail($id);

      if ($size) {
        $size->update($request->all());

        $all_sizes = Size::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $size
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

      $size = Size::findOrFail($id);

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

    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
