<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Specify the fields that can be mass assigned
    protected $fillable = [
        'name', 
        'description', 
        'price',
        'quantity',
        'image',
        'category_id',
        'manufacture_date',
        'expiry_date'
    ];
    // Cast the dates to Carbon instances
    protected $casts = [
        'manufacture_date' => 'date',
        'expiry_date' => 'date',
    ];
    
        
    // Relationship to the Category model
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship to the Comment model
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Accessor for formatted price
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.') . ' VNĐ';
    }

    // Relationship to the Rating model
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Calculate the average rating of the product.
     *
     * @return float
     */
    public function averageRating()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    /**
     * Check if the product is expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now()->format('Y-m-d');
    }
    
    /**
     * Check if stock is low (less than 5 items).
     *
     * @return bool
     */
    public function isLowStock()
    {
        return $this->quantity < 5;
    }
}
