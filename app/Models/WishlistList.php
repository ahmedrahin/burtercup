<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistList extends Model
{
    protected $guarded = [];

    public function wishlist(){
        return $this->belongsTo(CharityWishlist::class, 'charity_wishlist_id');
    }

}
