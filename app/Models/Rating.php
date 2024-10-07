<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
    ];

    /**
     * Mối quan hệ với User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    /**
 * Lấy nhãn tương ứng với số sao đánh giá.
 *
 * @param int $rating
 * @return string
 */
public static function getRatingLabel($rating)
{
    switch ($rating) {
        case 1:
            return 'Rất tệ';
        case 2:
            return 'Tệ';
        case 3:
            return 'Trung bình';
        case 4:
            return 'Tốt';
        case 5:
            return 'Tuyệt vời';
        default:
            return '';
    }
}
}
