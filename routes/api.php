<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'orders'], function () {
  Route::get('', [OrderController::class, 'index']);
  Route::post('', [OrderController::class, 'store']);
});

Route::post('webhook', WebhookController::class);
