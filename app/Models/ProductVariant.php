<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $guarded = [];

    // In App\Models\ProductVariant.php



      protected $casts = [
        'product_id' => 'integer',
    ];
}
