<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $guarded = [];

    public function wishlistList(){
        return $this->belongsTo(WishlistList::class, 'wishlist_list_id');
    }

}
