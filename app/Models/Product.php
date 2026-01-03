<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'short_description',
        'long_description',
        'cost_price',
        'selling_price',
        'stock',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function favoriteProducts()
    {
        return $this->hasMany(FavoriteProduct::class);
    }
    
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
