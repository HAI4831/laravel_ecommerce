<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function searchSuggestions(Request $request)
    {
        $query = $request->input('query');
        
        // Tìm kiếm sản phẩm dựa trên từ khóa
        $products = Product::where('name', 'LIKE', '%' . $query . '%')
            ->limit(5) // Giới hạn kết quả
            ->get(['name']); // Chỉ lấy trường name

        return response()->json($products); // Trả về JSON
    }
        // Hiển thị danh sách tất cả các sản phẩm
        public function index(Request $request)
    {
        $query = Product::with('category');

        // Nếu có từ khóa tìm kiếm, thêm điều kiện tìm kiếm
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhereHas('category', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%");
                });
        }

        $products = $query->get(); // Lấy tất cả sản phẩm sau khi áp dụng tìm kiếm

        return view('admin.products.index', compact('products'));
    }
    public function details($id){
         // Tải tất cả các mối quan hệ: category, ratings, comments và user của từng comment
        $product = Product::with([
            'category',           // Mối quan hệ với Category
            'ratings',            // Mối quan hệ với Rating
            'comments.user'       // Mối quan hệ với Comment và User của mỗi Comment
        ])->findOrFail($id);
        
        return view('products.details',compact('product'));
    }


    public function create()
    {
        $categories = Category::all(); // Lấy tất cả danh mục từ cơ sở dữ liệu
        // dd($categories);
        return view('admin.products.create', compact('categories')); // Truyền biến $categories vào view
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'nullable|exists:categories,id',
        ]);
        // Khởi tạo biến cho tên hình ảnh
        $imageName = null;

        // Handle file upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
        }

        // Product::create($request->all());
        // Tạo sản phẩm mới và lưu vào database
        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
            'image' => $imageName, // Lưu tên hình ảnh vào cơ sở dữ liệu
        ]);

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        // dd($product);
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all(); // Lấy tất cả danh mục để hiển thị trong form chỉnh sửa
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'category_id' => 'required|integer|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate image upload
        ]);
    
        // Prepare the data to be updated
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'category_id' => $request->category_id,
        ];
    
        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                $oldImagePath = public_path('images/' . $product->image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $data['image'] = $imageName; // Update image name
        }
    
        // Update the product with new data
        $product->update($data);
    
        return redirect()->route('admin.products.index')
                         ->with('success', 'Product updated successfully.');
    }
    

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product deleted successfully.');
    }
    //Người bình thường
public function show_normal(Product $product)
{
// Trả về view cho người dùng bình thường
return view('products.show', compact('product'));
}
}
