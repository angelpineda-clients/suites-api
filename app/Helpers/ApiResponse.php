<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse
{
  public static function success($data, $message = '', $code = Response::HTTP_OK)
  {
    return response()->json(data: [
      'success' => true,
      'data' => $data,
      'message' => $message,
    ], status: $code);
  }

  public static function error($message, $errors = [], $code = Response::HTTP_BAD_REQUEST)
  {
    return response()->json(data: [
      'success' => false,
      'message' => $message,
      'errors' => $errors,
    ], status: $code);
  }
}