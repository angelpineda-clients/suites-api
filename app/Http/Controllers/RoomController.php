<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;
use Str;

class RoomController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string|required',
            'price' => 'required'
        ]) ;

        try {
            $room = Room::create($request->all());

            return response()->json([
                'data' => $room
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function index()
    {
        try {
            $rooms = Room::with('size', 'floor')->get();

            return response()->json([
                'data' => $rooms
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show(string $id)
    {
        
        try{
            $room = Room::findOrFail($id);

            return response()->json([
                'data' => $room
            ]);

        }catch(\Throwable $th){
            throw $th;
        }
    }

    public function update(Request $request, string $id)
    {
        try{
            $room = Room::findOrFail(id: $id);

            $room->update($request->all());

            return response()->json([
                'data' => $room
            ]);

        }catch(\Throwable $th){
            throw $th;
        }
    }

    public function delete(string $id)
    {
        try {
            $room = Room::findOrFail($id);

            return response()->json([
                'data' => $room
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
