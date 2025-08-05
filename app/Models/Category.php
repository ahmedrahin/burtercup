<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

      protected $casts = [
        'parent_id' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(){
        return  $this->hasMany(Product::class);
    }

    public function subcategoryProducts()
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }

}
