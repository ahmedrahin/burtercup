<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemVariant extends Model
{
      protected $casts = [
        'order_item_id' => 'integer',
    ];
}
