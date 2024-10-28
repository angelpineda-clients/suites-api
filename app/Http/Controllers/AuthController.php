<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth;

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
        'message' => 'error: ' . $th->getMessage()
      ]);
    }
  }

  public function login(Request $request)
  {
    $credentials = request(['email', 'password']);

    if (!$token = auth()->attempt($credentials)) {
      return response()->json(["message" => "Unauthorized"], 401);
    }

    return $this->respondWithToken($token);
  }

  public function logout()
  {
    try {
      auth()->logout(true);

      return response()->json(['message' => 'Successfully logged out']);
    } catch (\Throwable $th) {
      return response()->json([
        'message' => 'Error: ' . $th->getMessage()
      ]);
    }
  }

  public function refresh(Request $request)
  {
    try {
      // Obtener el refresh token desde el cuerpo de la solicitud
      $refreshToken = $request->input('refresh_token');

      // Configura el refresh token en JWTAuth para generar un nuevo access token
      auth()->setToken($refreshToken);
      $newAccessToken = auth()->refresh();

      // Retorna el nuevo access_token en la respuesta
      return $this->respondWithToken($newAccessToken);
    } catch (JWTException $e) {
      // Si ocurre un error, responder con un código de error
      return response()->json(['error' => 'Refresh token inválido o expirado'], 401);
    }

  }


  protected function respondWithToken($token)
  {
    return response()->json([
      'data' => [
        'auth' => [
          'access_token' => $token,
          'token_type' => 'bearer',
          'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
          'refresh_token' => Auth::guard('api')->refresh(),
        ],
        'user' => auth()->user()
      ],
    ]);
  }
}
