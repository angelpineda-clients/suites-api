<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|unique:users',
            'password' => 'required|confirmed',
        ]);

        try {
            $user = User::create($request->all());

            return response()->json([
                'data' => $user
            ]);

        } catch (\Throwable $th) {
            
            return response()->json([
                'message' => 'error: '. $th->getMessage()
            ]);
        }
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(["message" => "Unauthorized"], 401);
        }

        return response()->json([
            'data' => [
                'access_token' => $token,
                'user' => auth()->user()
            ],
        ]);
    }

    public function me()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'Not authorized'
                ], 401);
            }

            return response()->json($user);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: '. $th->getMessage()
            ],500);
        }
    }

    public function logout()
    {
        try {
            auth()->logout(true);

            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Error: '. $th->getMessage()
            ]);
        }
    }
}
