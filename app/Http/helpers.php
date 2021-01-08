<?php

use Illuminate\Support\Facades\Http;

function createMyCourse($params)
{
  $url = env('URL_SERVICE_COURSES') . 'api/my-courses/premium';
  try {
    $response = Http::post($url, $params);
    $data = $response->json();
    $data['http_code'] = $response->getStatusCode();
    return $data;
  } catch (\Throwable $th) {
    return serviceNotAvailable();
  }
}

function serviceNotAvailable()
{
  return [
    'status' => 'error',
    'http_code' => 500,
    'message' => 'service course unavailable'
  ];
}
