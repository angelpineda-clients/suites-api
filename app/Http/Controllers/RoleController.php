<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class RoleController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'role' => 'string|required'
        ]);

        try {

            $role = Role::create(['name' => $request->get('role')]);

            return response()->json([
                'data' => "Role $role->name created."
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: '.$th->getMessage()
            ]);
        }
    }

    public function index(Request $request)
    {

        try {

            $roles = Role::all();

            return response()->json([
                'data' => $roles
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }

    public function show(string $roleID)
    {

        try {
            $role = Role::findOrFail($roleID);

            return response()->json([
                'data' => $role
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }

    public function update(Request $request, string $roleID)
    {

        $validated = $request->validate([
            'name' => 'string|required'
        ]);

        try {

            $role = Role::findOrFail($roleID);

            $role->update(['name' => $request->get('name')]);

            return response()->json([
                'data' => $role
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }

    public function delete(string $roleID)
    {

        try {
            $role = Role::findOrFail($roleID);

            $role->delete();

            return response()->json([
                'data' => "Role $role->name deleted."
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: ' . $th->getMessage()
            ]);
        }
    }
}
