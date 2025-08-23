<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = ['name', 'category_key', 'slug', 'image'];

    public function getCategoryNameAttribute()
    {
        return config('categories')[$this->category_key]['name'] ?? null;
    }
}
