<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price','quantity' ,'image','category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
        public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    // Accessor for formatted price
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . ' VNĐ';
    }
        public function ratings()
    {
        return $this->hasMany(Rating::class);
    }
    /**
     * Tính trung bình đánh giá của sản phẩm.
     *
     * @return float
     */
    public function averageRating()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }
}

