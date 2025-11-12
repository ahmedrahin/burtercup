<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CharityWishlist extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(WishlistCategory::class, 'category_id');
    }

}
