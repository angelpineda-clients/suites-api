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
            'alias' => 'string'
        ]);

        try {
            $season = Season::create($request->all());

            return response()->json([
                'data' => $season
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function index()
    {
        try {
            $seasons = Season::all();

            return response()->json([
                'data' => $seasons
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(Request $request, string $id)
    {

        try {
            $season = Season::findOrFail($id);

            return response()->json([
                'data' => $season
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

            $season->update($request->all());

            return response()->json([
                'data' => $season
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(Request $request, string $id)
    {

        try {

            $season = Season::findOrFail($id);

            $season->delete();

            return response()->json([
                'data' => $season->name . ' deleted'
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
