<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
  private function getMidtransSnapUrl($params)
  {
    \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_PRODUCTION');
    \Midtrans\Config::$is3ds = (bool) env('MIDTRANS_3DS');
    $snapUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
    return $snapUrl;
  }

  public function index()
  {
    $user_id = request()->input('user_id');
    $orders = Order::query();
    $orders->when($user_id, function ($query) use ($user_id) {
      return $query->where('user_id', $user_id);
    });

    return OrderResource::collection($orders->get());
  }

  public function store(Request $request)
  {
    //! user dan course dikirim dari api/courses/{id}
    $user = $request->input('user');
    $course = $request->input('course');

    $order = Order::create([
      // 'user_id' => $user->id, //Trying to get property 'id' of non-object karena $user berisi data bukan objek tetapi berisi array
      'user_id' => $user['id'],
      'course_id' => $course['id'],
    ]);

    $midtransParams = [
      'transaction_details' => [
        'order_id' => $order->id . '-' . Str::random(5),
        'groos_amount' => $course['price']
      ],
      'item_details' => [
        [
          "id" => $course['id'],
          "price" => $course['price'],
          "quantity" => 1,
          "name" => $course['name'],
          "brand" => "TODO LEARN",
          "category" => "Fullstack Web Development",
        ]
      ],
      'customer_details' => [
        "first_name" => $user['name'],
        "email" => $user['email'],
      ]
    ];
    $midtransSnapUrl = $this->getMidtransSnapUrl($midtransParams);
    $order->snap_url = $midtransSnapUrl;
    $order->metadata = [
      'course_id' => $course['id'],
      'course_name' => $course['name'],
      'course_thumbnail' => $course['thumbnail'],
      'course_price' => $course['price'],
      'course_level' => $course['level'],
    ];
    $order->save();

    return new OrderResource($order);
  }

  public function show($id)
  {
    //
  }

  public function update(Request $request, $id)
  {
    //
  }

  public function destroy($id)
  {
    //
  }
}
