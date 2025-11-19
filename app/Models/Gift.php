<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $guarded = [];

    public function wishlistList(){
        return $this->belongsTo(WishlistList::class, 'wishlist_list_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
