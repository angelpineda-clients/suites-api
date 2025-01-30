<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class StripeWebhookController extends Controller
{
  public function handlePayment(Request $request)
  {
    Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

    $event = $request->all();

    try {
      // Recibir el evento de Stripe
      $event = $request->all();

      // Log para pruebas
      Log::info('Evento de Stripe recibido:', $event);

      switch ($event['type']) {
        case 'payment_intent.processing': // status: 1
          $paymentIntentId = $event['data']['object']['id'] ?? null;

          $payment = Payment::where('payment_intent', $paymentIntentId)->first();

          $payment->update([
            'status' => 1
          ]);
          break;

        case 'payment_intent.succeeded': // status 2
          $paymentIntentId = $event['data']['object']['id'] ?? null;

          $payment = Payment::where('payment_intent', $paymentIntentId)->first();

          $payment->update([
            'status' => 2
          ]);

          break;

        case 'payment_intent.canceled': // status: 3
          $paymentIntentId = $event['data']['object']['id'] ?? null;

          $payment = Payment::where('payment_intent', $paymentIntentId)->first();

          $payment->update([
            'status' => 3
          ]);
          break;

        case 'payment_intent.requires_action': // status 4
          $paymentIntentId = $event['data']['object']['id'] ?? null;

          $payment = Payment::where('payment_intent', $paymentIntentId)->first();

          $payment->update([
            'status' => 4
          ]);

          break;

        default:
          Log::info('Evento sin manejar:', ['type' => $event['type']]);
      }

      return response()->json(['message' => 'Webhook recibido'], 200);
    } catch (\Exception $e) {
      Log::error('Error en webhook: ' . $e->getMessage());
      return response()->json(['error' => 'Webhook falló'], 400);
    }


  }
}
