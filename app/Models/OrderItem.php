<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'cart_item_id',
        'product_id',
        'category_item_id',
        'name',
        'quantity',
        'price',
        'total_price',
    ];

    // Quan hệ với Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Giả sử bạn đang lấy tất cả các OrderItem
    //$orderItems = OrderItem::with('product')->get();    
    // Quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
