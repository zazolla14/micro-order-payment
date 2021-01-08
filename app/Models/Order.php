<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
  use HasFactory;

  protected $fillable = ['status', 'user_id', 'course_id', 'snap_url', 'metadata'];
  protected $casts = [
    'metadata' => 'array'
  ];
}
