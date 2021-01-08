<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\PaymentLog;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
  public function __invoke(Request $request)
  {
    $data = $request->all();

    $orderId = $data['order_id']; //* {id}-random string(5) => 1-sHajE
    $signatureKey = $data['signature_key'];
    $mySignatureKey = hash('sha512', $orderId . $data['status_code'] . $data['gross_amount'] . env('MIDTRANS_SERVER_KEY'));

    if ($signatureKey !== $mySignatureKey) {
      return response()->json([
        'status' => 'error',
        'message' => 'invalid signature key'
      ], 400);
    }

    $realOrderId = explode('-', $orderId);
    $order = Order::find($realOrderId[0]);
    if (!$order) {
      return response()->json([
        'status' => 'error',
        'message' => 'order id not found'
      ], 404);
    }

    if ($order->status === 'success') {
      return response()->json([
        'status' => 'error',
        'message' => 'operation not permitted' // u can't change the status field
      ], 405);
    }

    $transactionStatus = $data['transaction_status'];
    $fraudStatus = $data['fraud_status'];
    $paymentType = $data['payment_type'];

    if ($transactionStatus == 'capture') {
      if ($fraudStatus == 'challenge') {
        $order->status = 'challenge';
      } else if ($fraudStatus == 'accept') {
        $order->status = 'success';
      }
    } else if ($transactionStatus == 'settlement') {
      $order->status = 'success';
    } else if (
      $transactionStatus == 'cancel' ||
      $transactionStatus == 'deny' ||
      $transactionStatus == 'expire'
    ) {
      $order->status = 'failture';
    } else if ($transactionStatus == 'pending') {
      $order->status = 'pending';
    }

    $order->save();
    PaymentLog::create([
      'status' => $transactionStatus,
      'payment_type' => $paymentType,
      'raw_response' => json_encode($data), // all data from midtrans
      'order_id' => $realOrderId[0]
    ]);

    if ($order->status === 'success') {
      createMyCourse([
        'course_id' => $order->course_id,
        'user_id' => $order->user_id
      ]);
    }

    return new OrderResource($order);
  }
}
