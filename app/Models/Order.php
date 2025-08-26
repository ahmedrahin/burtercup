<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

     public function delivery()
    {
        return $this->belongsTo(DeliveryOption::class, 'delivery_option_id' );
    }

      protected $casts = [
        'user_id' => 'integer',
    ];
}
