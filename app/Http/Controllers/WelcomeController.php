<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        // Lấy giá trị tìm kiếm từ query string
        $search = $request->input('search');
        // Lấy giá trị sắp xếp từ query string
        $sort = $request->input('sort', 'name'); // Default sorting by 'name'
        $order = $request->input('order', 'asc'); // Default ordering by 'asc'

        // Fetch products with search and sorting applied
        $products = Product::with('category')
            ->when($search, function($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%")
                             ->orWhere('description', 'LIKE', "%{$search}%");
            })
            ->orderBy($sort, $order) // Apply sorting
            ->paginate(10);

        // Trả về view 'welcome' với dữ liệu sản phẩm và các tùy chọn tìm kiếm, sắp xếp
        return view('welcome', compact('products', 'search', 'sort', 'order'));
    }
}
