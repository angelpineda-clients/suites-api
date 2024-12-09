<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Payment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

  private $STRIPE_KEY;

  public function __construct()
  {
    $this->STRIPE_KEY = env('STRIPE_SK_TEST');
  }


  public function store($amount, $bookingID): JsonResponse
  {

    if (!$bookingID) {
      return ApiResponse::error('Booking id is required for a payment');
    }

    try {

      $stripe = new \Stripe\StripeClient(config: $this->STRIPE_KEY);


      /* $payment_intent = $stripe->paymentIntents->create([
        'amount' => $amount,
        'currency' => 'mxn',
        'payment_method_types' => [
          'bancontact',
          'card',
          'eps',
          'giropay',
          'ideal',
          'p24',
          'sepa_debit',
        ],
      ]); */

      $payment = Payment::create([
        'payment_intent' => 'dummy_id', //$payment_intent->id,
        'amount' => '6000', //$payment_intent->amount,
        'client_secret' => 'client_secret', //$payment_intent->client_secret,
        'booking_id' => $bookingID
      ]);

      return $payment->client_secret;

    } catch (\Exception $e) {
      return ApiResponse::error(message: 'Unexpected error', errors: $e->getMessage());
    }
  }

  public function index()
  {
    try {
      $payments = Payment::get();

      return ApiResponse::success($payments);
    } catch (\Exception $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    }
  }

  public function show(string $id)
  {
    try {
      $payment = Payment::findOrFail($id);

      return ApiResponse::success($payment);
    } catch (ModelNotFoundException $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    } catch (\Exception $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    }
  }


  public function update(Request $request, string $id)
  {
    try {
      $payment = Payment::findOrFail($id);

      $payment->update([
        'status' => $request->status
      ]);

      return ApiResponse::success($payment);
    } catch (ModelNotFoundException $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    } catch (\Exception $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    }
  }

  public function delete(string $id)
  {
    try {
      $payment = Payment::findOrFail($id);

      $payment->delete();

      return ApiResponse::success(data: [], message: 'Payment deleted');
    } catch (ModelNotFoundException $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    } catch (\Exception $e) {
      return ApiResponse::error('Unexpected error', $e->getMessage());
    }

  }
}
