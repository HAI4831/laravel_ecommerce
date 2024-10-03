<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Mail\PaymentConfirmationMailMD;
use App\Mail\PaymentConfirmationMail;
use App\Jobs\DeletePendingOrderJob;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class PaymentController extends Controller
{
    /**
     * Xử lý quá trình đặt hàng và gửi email xác nhận thanh toán
     */
    public function process(Request $request)
    {  
        $quantities = $request->input('quantity');
        $coupon = $request->input('coupon');
        $paymentMethod = $request->input('payment_method');
        $cartItemDetails = $request->input('cartItemDetails');
        $totalAmount = $request->input('totalAmount');
        // Kiểm tra xem có sản phẩm nào được chọn không
        if (empty($quantities) || empty($cartItemDetails)) {
            return redirect()->route('carts.index')->with('error', 'Bạn chưa chọn sản phẩm nào.');
        }

        // Áp dụng mã giảm giá nếu có
        if ($coupon == 'DISCOUNT10') {
            $totalAmount *= 0.9; // Giảm giá 10%
        }

        // Sử dụng transaction để đảm bảo tính toàn vẹn của dữ liệu
        DB::beginTransaction();

        try {
            $products = [];
            foreach ($cartItemDetails as $cartItemId => $cartItemDetailJson) {
                $cartItemDetail = json_decode($cartItemDetailJson, true);
                $productId = $cartItemDetail['product_id'];
                $quantity = $quantities[$productId] ?? 0; // Sử dụng product_id để lấy quantity

                if ($quantity <= 0) {
                    throw new \Exception('Số lượng sản phẩm không hợp lệ.');
                }

                $price = $cartItemDetail['price'];
                $totalPrice = $quantity * $price;

                // Giảm số lượng sản phẩm trong database
                $product = Product::find($productId);
                if ($product) {
                    if ($product->quantity >= $quantity) {
                        $product->quantity -= $quantity;
                        $product->save();
                    } else {
                        throw new \Exception('Số lượng sản phẩm "' . $product->name . '" không đủ trong kho.');
                    }
                } else {
                    throw new \Exception('Sản phẩm không tồn tại.');
                }

                $products[] = [
                    'id' => $productId,
                    'name' => $cartItemDetail['name'],
                    'description' => $cartItemDetail['description'] ?? '',
                    'price' => $price,
                    'quantity' => $quantity,
                    'image' => $cartItemDetail['image'] ?? '',
                    'category_id' => $cartItemDetail['category_id'] ?? null,
                    'cart_item_id' => $cartItemId, // Thêm cart_item_id
                ];
            }

            // Tạo id hóa đơn dựa trên thời gian hiện tại
            $invoiceId = 'invoice-' . now()->format('YmdHis');

            // Lấy tên khách hàng từ thông tin người dùng đã xác thực
            $customerName = Auth::user()->name;

            // Tạo bản ghi đơn hàng trong DB
            $order = new Order();
            $order->txn_ref = uniqid(); // Tạo mã giao dịch duy nhất
            $order->user_id = auth()->id(); // Lấy ID người dùng đã đăng nhập
            $order->amount = $totalAmount;
            $order->customer_name = $customerName; // Lấy tên khách hàng
            $order->status = 'pending'; // Trạng thái đơn hàng
            // Lưu đơn hàng và kiểm tra xem có thành công không
            if (!$order->save()) {
                throw new \Exception('Lỗi khi lưu đơn hàng.');
            }
            // Gửi job sau 2 phút
            \Log::info('Dispatching DeletePendingOrderJob for order ID: ' . $order->id);
            DeletePendingOrderJob::dispatch($order->id)->delay(now()->addMinutes(2));
            \Log::info('Dispatched DeletePendingOrderJob successfully.');
            // Lưu OrderItem với cart_item_id
            foreach ($products as $product) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'cart_item_id' => $product['cart_item_id'], // Lưu cart_item_id
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'total_price' => $product['price'] * $product['quantity'],
                ]);
            }

            // Commit transaction trước khi gửi email
            DB::commit(); // Commit transaction

            // Tạo URL xác nhận thanh toán được ký
            $confirmUrl = URL::signedRoute('payment.confirm', [
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
            ]);

            $orderData = [
                'id' => $invoiceId,
                'order_id' => $order->id,
                'customer_name' => $customerName,
                'products' => $products,
                'total_amount' => $totalAmount,
                'coupon' => $coupon,
                'payment_method' => $paymentMethod,
                'date' => now()->format('d/m/Y'),
                'confirm_url' => $confirmUrl,
            ];

            // Sử dụng switch case để xử lý phương thức thanh toán
        switch ($paymentMethod) {
            case 'cash_on_delivery':
                // Gửi email xác nhận thanh toán
                Mail::to($request->user()->email)->send(new PaymentConfirmationMailMD($orderData));
                // Mail::to($request->user()->email)->send(new PaymentConfirmationMail($orderData));
                break;

            case 'VNPay':
                $order->status = 'confirmed'; 
                DB::commit(); // Commit transaction trước khi redirect
                return redirect()->route('vnpay.checkout');
                // return redirect()->route('vnpay.checkout', ['order_id' => $order->id]);

            case 'credit_card':
                $order->status = 'confirmed'; 
                return redirect()->route('home')->with('success', 'Thanh toán qua credit_card');
                break;
            case 'bank_transfer':
                $order->status = 'confirmed'; 
                return redirect()->route('home')->with('success', 'Thanh toán qua bank_transfer');
                break;
            case 'paypal':
                $order->status = 'confirmed'; 
                return redirect()->route('home')->with('success', 'Thanh toán qua paypal');
                break;
            default:
                throw new \Exception('Phương thức thanh toán không hợp lệ.');
        }

            // Chỉ xóa giỏ hàng trong session nếu bạn muốn giữ các mục trong DB cho confirmPayment
            // session()->forget('carts'); // Có thể giữ lại để xử lý trong confirmPayment

            // Không tạo PDF hóa đơn và không xoá cart tại đây
            // Chỉ trả về thông báo thành công cho người dùng
            return redirect()->route('carts.index')->with('success', 'Đơn hàng đã được tạo. Vui lòng kiểm tra email để xác nhận thanh toán.');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return redirect()->route('carts.index')->with('error', $e->getMessage());
        }
    }
        

    /**
     * Xử lý xác nhận thanh toán từ email qua GET request với Signed Route
     */
    public function confirmPayment(Request $request)
    {
        // Kiểm tra xem URL có hợp lệ và chưa hết hạn không
        if (!$request->hasValidSignature()) {
            return redirect()->route('home')->with('error', 'Liên kết xác nhận không hợp lệ hoặc đã hết hạn.');
        }

        // Lấy order_id và payment_method từ request
        $orderId = $request->query('order_id');
        $paymentMethod = $request->query('payment_method');

        // Sử dụng transaction để đảm bảo tính toàn vẹn của dữ liệu
        DB::beginTransaction();

        try {
            // Tìm đơn hàng trong database bằng order_id
            $order = Order::find($orderId);

            // Kiểm tra xem đơn hàng có tồn tại không
            if (!$order) {
                throw new \Exception('Đơn hàng không tồn tại.');
            }

            // Kiểm tra trạng thái đơn hàng
            if ($order->status !== 'pending') {
                throw new \Exception('Đơn hàng đã được xử lý.');
            }

            // Cập nhật trạng thái dựa trên phương thức thanh toán
            if ($paymentMethod === 'cash_on_delivery') {
                $order->status = 'confirmed'; // Nếu là "cash_on_delivery"
            } else {
                $order->status = 'paid'; // Nếu là các phương thức khác
                $order->payment_date = now(); // Cập nhật ngày thanh toán
            }

            $order->save();

            // Xóa các mục trong giỏ hàng theo `cart_item_id`
            foreach ($order->orderItems as $orderItem) {
                CartItem::where('id', $orderItem->cart_item_id)->delete();
            }

            // Xóa giỏ hàng trong session
            session()->forget('carts'); // Hoặc session()->flush(); để xóa tất cả

            // Tạo PDF hóa đơn
            $invoiceData = [
                'id' => 'invoice-' . $order->created_at->format('YmdHis'),
                'order_id' => $order->id,
                'customer_name' => $order->customer_name,
                'products' => $order->orderItems->map(function($item) {
                    return [
                        'name' => $item->name,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total_price' => $item->total_price,
                    ];
                }),
                'total_amount' => $order->amount,
                'coupon' => $order->coupon ?? '',
                'payment_method' => $order->payment_method ?? '',
                'date' => $order->created_at->format('d/m/Y'),
                'confirm_url' => '', // Không cần tại đây
            ];

            $pdf = Pdf::loadView('invoices.invoice', ['invoiceData' => $invoiceData]);

            // Commit transaction trước khi tải PDF
            DB::commit();

            // Tải hóa đơn dưới dạng PDF
            return $pdf->download('invoice.pdf');

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction nếu có lỗi
            return redirect()->route('home')->with('error', $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách giỏ hàng
     */
    public function index(Request $request)
    {
        $selectedCartItems = $request->input('selected_cartItems');

        // Kiểm tra xem có sản phẩm nào được chọn không
        if (empty($selectedCartItems) || !is_array($selectedCartItems)) {
            return redirect()->route('carts.index')->with('error', 'Bạn chưa chọn sản phẩm nào.');
        }

        // Lấy cart_id của người dùng một lần
        $cartId = Cart::where('user_id', auth()->id())->value('id');

        if (!$cartId) {
            return redirect()->route('carts.index')->with('error', 'Giỏ hàng của bạn không tồn tại.');
        }

        // Khởi tạo thông tin giỏ hàng
        $cartItemDetails = collect($selectedCartItems)->map(function ($cartItemJson, $id) use ($cartId) {
            $cartItem = json_decode($cartItemJson, true);
            $quantity = $cartItem['quantity'];
            $price = $cartItem['price'];
            $totalPrice = $quantity * $price;

            return [
                'id' => $id,
                'cart_id' => $cartId,
                'product_id' => $cartItem['product_id'],
                'name' => $cartItem['name'],
                'quantity' => $quantity,
                'price' => $price,
                'total_price' => $totalPrice,
            ];
        });

        // Truyền dữ liệu vào view
        return view('payment.index', ['cartItemDetails' => $cartItemDetails]);
    }

    // Các phương thức khác trong Controller không thay đổi
}
