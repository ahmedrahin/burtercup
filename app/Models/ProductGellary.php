<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductGellary extends Model
{
    protected $guarded = [];

      protected $casts = [
        'product_id' => 'integer',
    ];
}
