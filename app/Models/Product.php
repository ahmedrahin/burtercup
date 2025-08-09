<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

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

    public function measurements()
    {
        return $this->hasOne(ProductMeasurement::class);
    }

    public function frameDetails()
    {
        return $this->hasOne(ProductFrameDetails::class);
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

      protected $casts = [
        'user_id' => 'integer',
        'brand_id' => 'integer',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
    ];


}
