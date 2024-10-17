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
            $room = Room::create([
                'name' => $request['name'],
                'description' => $request['description'],
                'capacity' => $request['capacity'],
                'beds' => $request['beds'],
                'price' => $request['price'],
                'size_id' => $request['size_id'],
                'floor_id' => $request['floor_id'],
            ]);

            $services = $request->input('services[]');

            $room->services()->sync($services);

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
            $rooms = Room::with('size', 'floor', 'services')->get();

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
            $room = Room::where('id',$id)->with('size','floor', 'services')->first();

            if (!$room) {
                return response()->json([
                    'message' => "Results not found for id: $id"
                ]);
            }

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
            $room = Room::where('id', $id)->with('size', 'floor', 'services')->first();

            $room->update([
                'name' => $request['name'],
                'description' => $request['description'],
                'capacity' => $request['capacity'],
                'beds' => $request['beds'],
                'price' => $request['price'],
                'size_id' => $request['size_id'],
                'floor_id' => $request['floor_id'],
            ]);

            $services = $request->input('services[]');

            $room->services()->sync($services);

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

            $room->delete();

            return response()->json([
                'data' => $room
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
