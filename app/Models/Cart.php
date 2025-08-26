<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $guarded = [];

    public function product(){
        return  $this->belongsTo(Product::class);
    }

    public function user(){
        return  $this->belongsTo(User::class);
    }

    public function cartOptions()
    {
        return $this->hasOne(CartOptions::class, 'cart_id');
    }

      protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer'
    ];
}
