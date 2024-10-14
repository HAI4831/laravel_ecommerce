<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Hiển thị danh sách các đơn hàng.
     */
    public function index()
    {
        $user = Auth::user();

        // Kiểm tra nếu người dùng là admin
        if ($user->is_admin) {
            // Admin xem tất cả đơn hàng
            $orders = Order::with('orderItems.product', 'user')->orderBy('created_at', 'desc')->paginate(20);
        } else {
            // Người dùng xem đơn hàng của chính họ
            $orders = Order::with('orderItems.product')
                           ->where('user_id', $user->id)
                           ->orderBy('created_at', 'desc')
                           ->paginate(20);
        }

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Hiển thị chi tiết đơn hàng.
     */
    public function show($id)
    {
        $user = Auth::user();

        // Tìm đơn hàng
        $order = Order::with('orderItems.product')->findOrFail($id);

        // Kiểm tra quyền truy cập
        if (!$user->is_admin && $order->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền truy cập đơn hàng này.');
        }

        return view('admin.orders.show', compact('order'));
    }
    public function updateStatus(Request $request, $id){
        $order = Order::findOrFail($id);
        $order->status = $request->status; // status cd the lâ 'paid' hoac 'cancelled'
        $order->save();
        return redirect()->route('admin.orders.index')->with('success', 'Cập nhật trạng thái đơn hàng thành
        công!');
    }
    //in hoá đơn
    public function printInvoice($id)
{
    $order = Order::with('orderItems.product')->findOrFail($id);
    
    // Prepare invoice data
    $invoiceData = [
        'id' => $order->id,
        'customer_name' => $order->customer_name,
        'date' => $order->created_at->format('d/m/Y H:i'),
        'products' => $order->orderItems->map(function($item) {
            return [
                'name' => $item->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        }),
        'total_amount' => $order->amount,
    ];

    // Load the invoice view
    // dd($invoiceData);
    $pdf = Pdf::loadView('invoices.invoice', ['invoiceData' => $invoiceData])
          ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

    // Return the generated PDF
    return $pdf->download('invoice_'.$order->id.'.pdf');
}



    // Các phương thức khác (create, store, edit, update, destroy) có thể được thêm nếu cần
}
