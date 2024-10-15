<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function store(Request $request){

        $validated = $request->validate([
            'name' => 'string|required'
        ]);

        try {
            $service = Service::create($request->all());

            return response()->json([
                'data' => $service
            ]);
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function index()
    {
        try {
            $services = Service::all();

            return response()->json([
                'data' => $services
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
                'data' => $service
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

        try{
            
            $service = Service::findOrFail($id);

            $service->name = $request->get('name');

            $service->update();

            return response()->json([
                'data' => $service
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function delete(Request $request, string $id)
    {

        try {

            $service = Service::findOrFail($id);

            $service->delete();

            return response()->json([
                'data' => $service->name . ' deleted'
            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
