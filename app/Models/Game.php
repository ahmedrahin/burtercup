<?php

namespace App\Models;

use App\Http\Controllers\Web\Backend\Game\VersusCategoryController;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $guarded = [];

    public function category(){
        return $this->belongsTo(GameCategory::class, 'game_category_id');
    }

    public function options(){
        return $this->hasMany(GameOption::class);
    }
}
