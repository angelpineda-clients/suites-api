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
            'alias' => 'string'
        ]);

        try {
            $size = Size::create($request->all());

            return response()->json([
                'data' => $size
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function index()
    {
        try {
            $sizes = Size::all();

            return response()->json([
                'data' => $sizes
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
                'data' => $size
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

            $size->update($request->all());

            return response()->json([
                'data' => $size
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(Request $request, string $id)
    {

        try {

            $size = Size::findOrFail($id);

            $size->delete();

            return response()->json([
                'data' => $size->name . ' deleted'
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
