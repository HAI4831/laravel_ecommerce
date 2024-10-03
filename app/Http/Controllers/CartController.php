<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()// Danh sách đơn hàng
    {
        // Kiểm tra nếu có giỏ hàng trong session, nếu không, nạp từ DB
        if (!session()->has('carts')) {
            //tìm cart dựa vào user_id giá trị là auth->id(id người dùng đã đăng nhập)->lastest(cái mới nhất)
            $carts = Cart::with('items.product')->where('user_id', auth()->id())->latest()->first();
            if ($carts) {
                $cartItems = $carts->items->mapWithKeys(function ($item) {
                    //item là cartItem
                    // kiểm tra thông tin lưu cho thanh toán
                    return [$item->id => [
                        "cart_item_id" => $item->id,
                        "cart_id" => $item->cart_id,
                        "product_id" => $item->product_id,
                        "name" => $item->product->name,
                        "quantity" => $item->quantity,
                        "price" => $item->product->price,
                        "image" => $item->product->image,
                        "category" => $item->product->category->name
                    ]];
                });
                session()->put('carts', $cartItems);
            } else {
                session()->put('carts', []);
            }
        }

        $carts = session()->get('carts', []);
        return view('carts.index', compact('carts'));
    }
    // Thêm sản phẩm vào giỏ hàng
    public function store(Request $request,Product $product)
    { 
        //Cart.index.blade.php truyền vào product_id với tham số nhận product , frameW sử dụng model binding route tìm db trả về product hợp lệ id nếu không thấy trả về null 
        if (!$product) {
            return redirect()->route('carts.index')->with('error', 'Product not found.');
        }


        //nhận danh sách cartItem từ session
        $cartSession = collect(session()->get('carts', []));


        // Lấy số lượng hiện tại từ request (quantityCurrent)   
        // Kiểm tra và tính toán số lượng yêu cầu
        //số lượng yêu cầu
        $quantity = $request->input('quantity', 1);
        //số lượng sản phẩm đã yêu cầu trước đó
        $quantityCurrent = $request->input('quantityCurrent', 0);
        //nếu người dùng nhập vào giá trị nhỏ hơn số lượng đã yêu cầu trước đó($quantity < $quantityCurrent) ->$requestedQuantity=$quantity ngược lại $quantity > $quantityCurrent thì $quantity=$quantity - $quantityCurrent
        $requestedQuantity = ($quantity < $quantityCurrent) ? $quantity : $quantity - $quantityCurrent;

        //lấy cartItem trùng với ProductId yêu cầu để cập nhật số lượng nếu k có tạo mới
        //#note carItem trong session có nhiều thông tin hơn so với khi lưu vì cần để hiển thị lên trang ,còn lưu trong db cần giảm thuộc tính ,tối ưu lưu trữ
        $cartItemByProductId = null;
        $cartItemId=null;
        foreach ($cartSession as $cartItemId => $cartItem) {
            // Lấy giá trị của phần tử đầu tiên trong mảng $cart
            if ($cartItem['product_id'] === $product->id) {
                $cartItemByProductId = $cartItem;
                break;
            }
        }

         // sản phẩm đã có sẽ cập nhật số lượng k tạo mới toàn bộ 
         if ($cartItemByProductId) {

             // Kiểm tra tổng số lượng yêu cầu + số lượng đã yêu cầu trong cart trước đó có vượt quá số lượng còn lại của sản phẩm được yêu cầu dựa trên id không
            if ($cartItemByProductId['quantity'] + $requestedQuantity > $product->quantity) {
                return redirect()->route('carts.index')->with('message', 'Số lượng yêu cầu vượt quá số lượng còn lại của sản phẩm.');
            }
            $cartItemByProductId['quantity'] += $requestedQuantity;
            $cartItemByProductId['price'] = $product->price*$requestedQuantity; // Cập nhật giá nếu cần
            $cartSession->put($cartItemId, $cartItemByProductId);
         }  else {
            //Tạo mới cartId cho người dùng sở hữu tương ứng
            if (auth()->check()) {
                $carts = Cart::updateOrCreate(
                ['user_id' => auth()->id()]
            );
        }
            //Ngược lại id sản phẩm yêu cầu add cart là chưa tồn tại trong session thì tạo mới yêu cầu
            $cart = Cart::where('user_id', auth()->id())->latest()->first();//nhận cart dựa trên user_id
            //tạo mới cartItem
            $cartItem =
                [
                    "cart_id" => $cart->id,
                    "product_id" => $product->id,
                    "quantity" => $requestedQuantity,
                    "price" => $product->price*$requestedQuantity,
                ];

            $createdCartItem=$cart->items()->create($cartItem);
            // thêm thuộc tính cần hiển thị lên trang
            $cartItem = array_merge($cartItem, [
                'name' => $product->name, 
                'image' => $product->image,
                'category' => $product->category->name,
            ]);
            $cartSession->put($createdCartItem->id,$cartItem);
        }
        session()->put('carts', $cartSession->all());

        return redirect()->route('carts.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
        // return response()->json(['success' => true, 'message' => 'Giỏ hàng đã được cập nhật.']);
    }
    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $carts = Cart::find($id);
        if ($carts) {
            return response()->json(['data' => $carts]);
        }
        return response()->json(['message' => 'Order not found'], 404);
    }
        public function edit(string $id)
    {
        // Tìm giỏ hàng dựa vào ID
        $carts = Cart::findOrFail($id);

        // Lấy tất cả các sản phẩm để người dùng có thể thay đổi sản phẩm trong giỏ hàng
        $products = Product::all();

        // Hiển thị view form chỉnh sửa giỏ hàng
        return view('carts.edit', compact('carts', 'products'));
    }
    // Cập nhật giỏ hàng
    public function update(Request $request)
    {
        // Ghi log dữ liệu nhận được từ request và product
        Log::info('Request data_1:00:', ['request' => $request->all()]);
        Log::info('Product data:', ['product' => $product]);
        // dd($product->name,$product->quantity);
        if (!$product) {
            return redirect()->route('carts.index')->with('error', 'Product not found.');
        }

        $carts = collect(session()->get('carts', []));
        $requestedQuantity = $request->input('quantity', 1);

         // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
        if ($carts->has($product->id)) {
            $item = $carts->get($product->id);
            $currentCartQuantity = $item['quantity'];
            $item['price'] = $product->price;
        } else {
            $currentCartQuantity = 0;
        }

        // Kiểm tra tổng số lượng yêu cầu có vượt quá số lượng còn lại của sản phẩm không
        if ($currentCartQuantity + $requestedQuantity > $product->quantity) {
            return redirect()->route('carts.index')->with('message', 'Số lượng yêu cầu vượt quá số lượng còn lại của sản phẩm.');
        }

        if ($carts->has($product->id)) {
            $item = $carts->get($product->id);
            $item['quantity']++;
            $carts->put($product->id, $item);
        } else {
            $carts->put($product->id, [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "category" => $product->category->name
            ]);
        }

        // $product->quantity -= $requestedQuantity;
        $product->save();
        session()->put('carts', $carts->all());

        // Save cart to database when creating an order
        $this->saveCartToDatabase();

        // return redirect()->route('carts.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng.');
        return response()->json(['success' => true, 'message' => 'Giỏ hàng đã được cập nhật.']);
    }
    // Xoá sản phẩm khỏi giỏ hàng
    public function destroy($id)
    {
        if (is_null($id) || !is_string($id)) {
            return redirect()->route('carts.index')->with('error', 'Sản phẩm không hợp lệ.');
        }

        $carts = session()->get('carts', []);
        if (isset($carts[$id])) {
            unset($carts[$id]);
            session()->put('carts', $carts);
            $message = 'Sản phẩm đã được xoá khỏi giỏ hàng.';
        } else {
            $message = 'Sản phẩm không tìm thấy trong giỏ hàng.';
        }
        CartItem::where('id',$id)->delete();

        return redirect()->route('carts.index')->with('success', $message);
    }
    // Tìm kiếm đơn hàng
    public function search(Request $request)
    {
        $query = Cart::query();
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }
        $carts = $query->get();
        return response()->json(['data' => $carts]);
    }
     // Xem lịch sử trạng thái đơn hàng
     public function history($id)
     {
         $carts = Cart::find($id);
         if ($carts) {
             $history = $carts->history;
             return response()->json(['data' => $history]);
         }
         return response()->json(['message' => 'Order not found'], 404);
     }
     // Thay đổi trạng thái đơn hàng
     public function updateStatus(Request $request, $id)
     {
         $carts = Cart::find($id);
         if ($carts) {
             $validated = $request->validate([
                 'status' => 'required|string'
             ]);
             $carts->status = $validated['status'];
             $carts->save();
             return response()->json(['data' => $carts]);
         }
         return response()->json(['message' => 'Order not found'], 404);
     }
     // Lưu giỏ hàng vào cơ sở dữ liệu khi tạo đơn hàng
     public function saveCartToDatabase()
     {
        // $cartItems = session()->get('carts', []);
        // $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);
        // foreach ($cartItems as $cartItemId => $details) {
        //     $detailValues = $details[""];
            // dd($details);
            // $cart->items()->create([
            //     'cart_id' => $details['cart_id'],
            //     'product_id' => $details['product_id'],
            //     'quantity' => $details['quantity'],
            //     'price' => $details['price'],
            // ]);
        // }
     }
}