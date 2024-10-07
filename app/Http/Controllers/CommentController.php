<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Lưu bình luận mới.
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'comment' => 'required|string|max:1000',
        ]);

        $product = Product::findOrFail($productId);

        Comment::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'comment' => $request->input('comment'),
        ]);

        return redirect()->route('products.details', $product->id)->with('success', 'Bình luận của bạn đã được đăng thành công!');
    }
}
