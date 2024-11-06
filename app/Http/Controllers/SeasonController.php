<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
  public function store(Request $request)
  {

    $validated = $request->validate([
      'name' => 'string|required',
      'alias' => 'nullable|string'
    ]);

    try {
      $season = Season::create($request->all());

      if ($season) {

        $all_seasons = Season::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_seasons
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
      $all_seasons = Season::all();

      if ($all_seasons) {
        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_seasons
          ]
        ]);
      }

    } catch (\Throwable $th) {
      throw $th;
    }
  }

  public function show(Request $request, string $id)
  {

    try {
      $season = Season::findOrFail($id);

      return response()->json([
        'response' => [
          'status' => true,
          'data' => $season
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

      $season = Season::findOrFail($id);

      if ($season) {
        $season->update($request->all());

        $all_seasons = Season::all();

        return response()->json([
          'response' => [
            'status' => true,
            'data' => $all_seasons
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

    } catch (\Throwable $th) {
      throw $th;
    }
  }
}
