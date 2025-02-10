<?php

namespace App\Services;

use App\Helpers\ApiResponse;
use App\Helpers\ParseValues;
use App\Models\Price;
use DB;
use Request;
use Symfony\Component\HttpFoundation\Response;
use Validator;

class PriceService
{

  private $STRIPE_KEY;

  public function __construct()
  {
    $this->STRIPE_KEY = env('STRIPE_SK_TEST');
  }

  public function createDefault($amount, $roomID, $product)
  {

    DB::beginTransaction();
    try {

      $price = Price::create([
        'amount' => $amount,
        'room_id' => $roomID,
        'stripe_id' => $product->default_price,
        'is_default' => true
      ]);

      if ($price) {

        return ['success' => false, 'error' => 'No se logro crear el precio'];
      }

      DB::commit();
      return ['success' => true, 'price' => $price];

    } catch (\Exception $e) {
      DB::rollBack();
      return ['success' => false, 'error' => $e->getMessage()];
    }
  }
}