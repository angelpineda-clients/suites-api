<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\Payment;
use DB;
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


  public function store($amount, $bookingID)
  {

    if (!$bookingID) {
      return ApiResponse::error('Booking id is required for a payment');
    }

    DB::beginTransaction();

    try {

      $stripe = new \Stripe\StripeClient(config: $this->STRIPE_KEY);


      $payment_intent = $stripe->paymentIntents->create([
        'amount' => $amount,
        'currency' => 'mxn',
        'payment_method_types' => [
          'card',
        ],
      ]);

      $payment = Payment::create([
        'payment_intent' => $payment_intent->id,
        'amount' => $payment_intent->amount,
        'client_secret' => $payment_intent->client_secret,
        'booking_id' => $bookingID
      ]);

      DB::commit();

      return ['payment_info' => $payment->client_secret, 'error' => null];

    } catch (\Exception $e) {
      DB::rollBack();
      return ['payment_info' => null, 'error' => $e->getMessage()];
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
