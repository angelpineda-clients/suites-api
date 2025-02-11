<?php

namespace App\Services;

use App\Models\Price;
use DB;


class PriceService
{

  /**
   * Create default price in DB
   * @param mixed $amount
   * @param mixed $roomID
   * @param mixed $product
   * @return array{error: string, success: bool|array{price: TModel, success: bool}}
   */
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

      DB::commit();
      return ['success' => true, 'price' => $price];

    } catch (\Exception $e) {
      DB::rollBack();
      return ['success' => false, 'error' => $e->getMessage()];
    }
  }
}