<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'status' => $this->status,
      'user_id' => $this->user_id,
      'course_id' => $this->course_id,
      'snap_url' => $this->snap_url,
      'metadata' => $this->metadata,
      'created_at' => $this->created_at->format('d F y H:m:s'),
      'updated_at' => $this->updated_at->format('d F y H:m:s'),
    ];
  }

  public function with($request)
  {
    return [
      'status' => 'success'
    ];
  }
}
