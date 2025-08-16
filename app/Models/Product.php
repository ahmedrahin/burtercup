<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function tags()
    {
        return $this->hasMany(PorductTag::class);
    }

    public function variants(){
        return $this->hasMany(ProductVariant::class);
    }

    public function gellary_images(){
        return $this->hasMany(ProductGellary::class);
    }

    public function reviews(){
        return $this->hasMany(ProductReview::class);
    }

    public function wishlists(){
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productSizes()
    {
        return $this->hasMany(ProductSize::class);
    }

      protected $casts = [
        'user_id' => 'integer',
        'brand_id' => 'integer',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
    ];


}
