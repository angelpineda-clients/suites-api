<?php

namespace App\Services;

class ProductService
{

  private $STRIPE_KEY;

  public function __construct()
  {
    $this->STRIPE_KEY = env('STRIPE_SECRET');
  }

  public function update($id, $data)
  {
    $stripe = new \Stripe\StripeClient($this->STRIPE_KEY);

    try {
      $isProduct = $stripe->products->update($id, $data);

      return ['success' => true, 'product' => $isProduct];

    } catch (\Exception $e) {
      return ['success' => false, 'errors' => $e->getMessage()];
    }
  }

  public function updateDefaultPrice($productID, $priceID)
  {
    $stripe = new \Stripe\StripeClient($this->STRIPE_KEY);

    try {
      $isProduct = $stripe->products->update($productID, [
        'default_price' => $priceID
      ]);

      return ['success' => true, 'product' => $isProduct];

    } catch (\Exception $e) {
      return ['success' => false, 'errors' => $e->getMessage()];
    }
  }
}