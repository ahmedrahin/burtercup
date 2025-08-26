<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    public function order(){
        return $this->belongsTo(Order::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }

    public function cartOptions()
    {
        return $this->hasOne(CartOptions::class, 'order_item_id');
    }

      protected $casts = [
        'order_id' => 'integer',
        'product_id' => 'integer'
    ];
}
