<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Search product suggestions
    public function searchSuggestions(Request $request)
    {
        $query = $request->input('query');

        // Search for products based on the keyword
        $products = Product::where('name', 'LIKE', '%' . $query . '%')
            ->limit(5) // Limit results
            ->get(['name']); // Only get the name field

        return response()->json($products); // Return JSON response
    }

    // Display a list of all products
    public function index(Request $request)
    {
        $query = Product::with('category');

        // If there's a search keyword, add the search condition
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function ($query) use ($search) {
                      $query->where('name', 'LIKE', "%{$search}%");
                  });
        }

        $products = $query->get(); // Get all products after applying search

        return view('admin.products.index', compact('products'));
    }

    // Show product details
    public function details($id)
    {
        // Load all relationships: category, ratings, comments, and user of each comment
        $product = Product::with([
            'category',           // Relationship with Category
            'ratings',            // Relationship with Rating
            'comments.user'       // Relationship with Comment and User of each Comment
        ])->findOrFail($id);
        
        return view('products.details', compact('product'));
    }

    // Show create product form
    public function create()
    {
        $categories = Category::all(); // Get all categories from the database
        return view('admin.products.create', compact('categories')); // Pass $categories to the view
    }

    // app/Http/Controllers/ProductController.php
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'category_id' => 'nullable|exists:categories,id',
        'manufacture_date' => 'required|date', // Validate manufacture_date
        'expiry_date' => 'required|date|after:manufacture_date', // Validate expiry_date
        // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Handle image upload
    $imageName = null;
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
    }

    // Create new product
    Product::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'category_id' => $request->category_id,
        'image' => $imageName,
        'manufacture_date' => $request->manufacture_date, // Store manufacture_date
        'expiry_date' => $request->expiry_date, // Store expiry_date
    ]);

    return redirect()->route('admin.products.index')
                     ->with('success', 'Product created successfully.');
}

public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'quantity' => 'required|integer|min:1',
        'category_id' => 'required|integer|exists:categories,id',
        'expiry_date' => 'required|date|after:manufacture_date', // Validate expiry_date
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Prepare the data to be updated
    $data = [
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'quantity' => $request->quantity,
        'category_id' => $request->category_id,
        'expiry_date' => $request->expiry_date, // Update expiry_date
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

    // Show product details for admin
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    

    
// Show edit form for a product
public function edit(Product $product)
{
    $categories = Category::all(); // Get all categories for the edit form
    return view('admin.products.edit', compact('product', 'categories'));
}
    // Delete a product
    public function destroy(Product $product)
    {
        // Delete old image if exists
        $this->deleteOldImage($product->image);
        $product->delete();

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product deleted successfully.');
    }

    // Show product details for normal users
    public function show_normal(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    // Handle image upload
    private function handleImageUpload($image)
    {
        if ($image) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            return $imageName;
        }
        return null;
    }

    // Delete old image if exists
    private function deleteOldImage($imageName)
    {
        if ($imageName) {
            $oldImagePath = public_path('images/' . $imageName);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }
}
