<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id','category_item_id', 'quantity','price','status'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
