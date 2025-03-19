<?php

namespace App\Services;

use App\Models\Room;
use App\Models\Season;
use App\Models\Price;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Carbon\Carbon;

class CheckoutService
{


  public function createCheckoutSession($roomId, $initialDate, $finalDate)
  {
    $room = Room::findOrFail($roomId);
    $initialDate = Carbon::parse($initialDate);
    $finalDate = Carbon::parse($finalDate);
    $days = $initialDate->diffInDays($finalDate);

    $prices = [];

    $DOMAIN = env('DOMAIN');

    for ($i = 0; $i < $days; $i++) {
      $currentDate = $initialDate->copy()->addDays($i);
      $season = Season::where('initial_date', '<=', $currentDate)
        ->where('final_date', '>=', $currentDate)
        ->first();

      $price = $season ? Price::where('room_id', $room->id)->where('season_id', $season->id)->first() : null;
      $priceId = $price ? $price->stripe_id : $room->stripe_default_price_id;

      if (!isset($prices[$priceId])) {
        $prices[$priceId] = 0;
      }
      $prices[$priceId]++;
    }

    Stripe::setApiKey(env('STRIPE_SECRET'));

    $lineItems = [];
    foreach ($prices as $priceId => $quantity) {
      $lineItems[] = [
        'price' => $priceId,
        'quantity' => $quantity,
      ];
    }

    $session = Session::create([
      'payment_method_types' => ['card'],
      'currency' => 'mxn',
      'line_items' => $lineItems,
      'mode' => 'payment',
      'return_url' => url($DOMAIN . '/sucess-order?session_id={CHECKOUT_SESSION_ID}'),
      'ui_mode' => 'embedded',
      'billing_address_collection' => 'auto'
    ]);

    return $session;
  }
}
