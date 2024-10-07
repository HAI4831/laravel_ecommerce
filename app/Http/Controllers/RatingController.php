<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Lưu đánh giá mới.
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $product = Product::findOrFail($productId);

        // Kiểm tra xem người dùng đã đánh giá sản phẩm này chưa
        $existingRating = Rating::where('product_id', $product->id)
                                ->where('user_id', Auth::id())
                                ->first();

        if ($existingRating) {
            // Cập nhật đánh giá nếu đã tồn tại
            $existingRating->update([
                'rating' => $request->input('rating'),
            ]);
        } else {
            // Tạo mới đánh giá
            Rating::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'rating' => $request->input('rating'),
            ]);
        }

        return redirect()->route('products.details', $product->id)->with('success', 'Đánh giá của bạn đã được lưu!');
    }
}
