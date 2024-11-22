<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
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
      $user = User::create(attributes: $request->all());

      return ApiResponse::success(data: $user, message: 'User registered', code: Response::HTTP_CREATED);

    } catch (\Exception $e) {

      return ApiResponse::error(message: 'Error trying to register', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);

    }
  }

  public function login(Request $request)
  {
    $credentials = request(key: ['email', 'password']);

    if (!$token = auth()->attempt(credentials: $credentials)) {
      return ApiResponse::error(message: "Wrong credentials", errors: '', code: Response::HTTP_UNAUTHORIZED);

    }

    return $this->respondWithToken(token: $token);
  }

  public function logout()
  {
    try {
      auth()->logout(true);

      return ApiResponse::success(data: 'Successfully logged out');
    } catch (\Exception $e) {
      return ApiResponse::error(message: 'Error trying to logout', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }

  public function refresh(Request $request)
  {
    try {

      $refreshToken = $request->input(key: 'refresh_token');

      // set refresh token to generate new one
      auth()->setToken(token: $refreshToken);
      $newAccessToken = auth()->refresh();

      // Retorn new token
      return $this->respondWithToken(token: $newAccessToken);
    } catch (JWTException $e) {
      return ApiResponse::error(message: 'Error generation refresh token', errors: $e->getMessage(), code: Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }


  protected function respondWithToken($token)
  {
    $data = [
      'auth' => [
        'access_token' => $token,
        'token_type' => 'bearer',
        'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        'refresh_token' => Auth::guard('api')->refresh(),
      ],
      'user' => auth()->user()
    ];

    return ApiResponse::success(data: $data);

  }
}
