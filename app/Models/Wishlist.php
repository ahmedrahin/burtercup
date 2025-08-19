<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $guarded = [];

    public function product(){
        return  $this->belongsTo(Product::class);
    }

    public function user(){
        return  $this->belongsTo(User::class);
    }

      protected $casts = [
        'user_id' => 'integer',
        'product_id' => 'integer'
    ];
}
