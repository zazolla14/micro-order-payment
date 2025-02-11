<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentLogsTable extends Migration
{
  public function up()
  {
    Schema::create('payment_logs', function (Blueprint $table) {
      $table->id();
      $table->string('status');
      $table->string('payment_type');
      $table->json('raw_response');
      $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('payment_logs');
  }
}
