<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    protected $guarded = [];

    public function donations()
    {
        return $this->hasMany(UserDonation::class);
    }

    public function volunteer()
    {
        return $this->hasMany(Volunteer::class);
    }
}
