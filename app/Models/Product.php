<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'sku',
        'category_id',
        'brand_id',
        'model_number',
        'slug',
        'status',
        'price',
        'discount_price',
        'stock_quantity',
        'stock_status',
        'reorder_level',
        'front_view_image',
        'back_view_image',
        'side_view_image',
        'video',
        'short_description',
        'user_id',

    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }




}
