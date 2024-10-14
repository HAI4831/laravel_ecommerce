<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
    protected $paymentController;

    // Thêm Dependency Injection cho PaymentController
    public function __construct(PaymentController $paymentController)
    {
        $this->paymentController = $paymentController;
    }
    /**
     * Hiển thị trang checkout với nút thanh toán.
     */
    public function checkout()
    {
        return view('vnpay.checkout');
    }
    /**
     * Xử lý thanh toán VNPay.
     */
    // // Lấy thông tin config:
        // $vnp_TmnCode = config('vnpay.vnp_TmnCode'); // Mã website của bạn tại VNPAY 
        // $vnp_HashSecret = config('vnpay.vnp_HashSecret'); // Chuỗi bí mật
        // $vnp_Url = config('vnpay.vnp_Url'); // URL thanh toán của VNPAY
        // $vnp_ReturnUrl = config('vnpay.vnp_Returnurl'); // URL nhận kết quả trả về
        #vnpay
        // session(['cost_id' => $request->id]);
        // session(['url_prev' => url()->previous()]);

        // dữ liệu amount là dữ liệu duy nhất bắt buộc cần từ user
        // dd($request);
    // hướng dẫn chuẩn cách thanh toán : https://sandbox.vnpayment.vn/apis/docs/thanh-toan-pay/pay.html
    // thông tin thẻ ngân hàng test : https://sandbox.vnpayment.vn/apis/vnpay-demo/
    public function pay(Request $request)
    {
        $order_id=$request->input('order_id');
        $vnp_Amount = $request->input('amount');
        $vnp_TmnCode = "LWFJ51G0"; //Mã website tại VNPAY 
        $vnp_HashSecret = "YRGQ7D5IV4A7NAA4YB1P4YL3YY3YVA17"; //Chuỗi bí mật
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:8000/vnpay/return";
        // $vnp_TxnRef = date("YmdHis"); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_TxnRef = date("YmdHis") . "?" . $order_id;
        $vnp_OrderInfo = "Thanh toán hóa đơn phí dich vụ";
        $vnp_OrderType = 'ecommerce';
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            // "vnp_BankCode" => 'NCB',
            "vnp_TxnRef" => $vnp_TxnRef,
        );
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHashType=SHA512&vnp_SecureHash=' . $vnpSecureHash;
        }
        return redirect($vnp_Url);
    }


    /**
     * Nhận kết quả thanh toán từ VNPay.
     */
    public function return(Request $request)
{
    $vnp_SecureHash = $request->input('vnp_SecureHash');
    $vnp_TxnRef = $request->input('vnp_TxnRef');
    // Tách order_id từ $vnp_TxnRef
    list(, $order_id) = explode('?', $vnp_TxnRef);
    // dd($vnp_SecureHash);
    // dd($order_id);
    $inputData = array();
            foreach ($_GET as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    $inputData[$key] = $value;
                }
            }
    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    $i = 0;
            $hashData = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }
    
            $secureHash = hash_hmac('sha512', $hashData, "YRGQ7D5IV4A7NAA4YB1P4YL3YY3YVA17");
            if ($secureHash == $vnp_SecureHash) {
                if ($_GET['vnp_ResponseCode'] == '00') {
                    // Kiểm tra xem order_id có hợp lệ không (nên có kiểm tra)
                    if (!empty($order_id)) {
                        return $this->paymentController->confirmPayment($order_id);
                    } else {
                        // Xử lý khi không tìm thấy order_id
                        // Có thể thông báo lỗi hoặc ghi log
                        \Log::error("Không tìm thấy order_id từ vnp_TxnRef: $vnp_TxnRef");
                    }
                    echo "GD Thanh cong";
                } 
                else {
                    echo "GD Khong thanh cong";
                    }
            } else {
                echo "Chu ky khong hop le";
                }

    // // Remove secure hash and hash type from data
    // $inputData = $request->all();
    // unset($inputData['vnp_SecureHash']);
    // unset($inputData['vnp_SecureHashType']); // If present

    // // Sort data by keys
    // ksort($inputData);

    // // Build hash data string
    // $hashData = '';
    // foreach ($inputData as $key => $value) {
    //     if ($key !== 'vnp_SecureHash' && strlen($value) > 0) {
    //         $hashData .= $key . "=" . $value . "&";
    //     }
    // }
    // $hashData = rtrim($hashData, "&");

    // // Retrieve VNPay hash secret
    // $vnp_HashSecret = "YRGQ7D5IV4A7NAA4YB1P4YL3YY3YVA17";

    // // Generate secure hash using HMAC SHA512
    // $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    // // Compare the secure hash
    // if (strcasecmp($vnp_SecureHash, $secureHash) === 0) {
    //     if ($request->input('vnp_ResponseCode') == '00') {
    //         // Payment successful
    //         $orderId = substr($request->input('vnp_TxnRef'), 5); // Adjust based on your TxnRef format
    //         $order = \App\Models\Order::find($orderId);
    //         if ($order) {
    //             $order->status = 'paid';
    //             $order->payment_date = now();
    //             $order->save();
    //         }

    //         return view('vnpay.success');
    //     } else {
    //         // Payment failed
    //         return view('vnpay.fail');
    //     }
    // } else {
    //     Log::error('Hash mismatch: Expected: ' . $secureHash . ' Received: ' . $vnp_SecureHash);
    //     // Invalid hash
    //     return view('vnpay.invalid');
    // }
}

}
