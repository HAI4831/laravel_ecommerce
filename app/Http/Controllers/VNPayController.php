<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VNPayController extends Controller
{
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
    public function pay(Request $request){
        // Lấy thông tin config: 
        $vnp_TmnCode = config('vnpay.vnp_TmnCode'); // Mã website của bạn tại VNPAY 
        $vnp_HashSecret = config('vnpay.vnp_HashSecret'); // Chuỗi bí mật
        $vnp_Url = config('vnpay.vnp_Url'); // URL thanh toán của VNPAY
        $vnp_ReturnUrl = config('vnpay.vnp_Returnurl'); // URL nhận kết quả trả về

       // Lấy thông tin từ đơn hàng phục vụ thanh toán 
       // Dưới đây là thông tin giả định, bạn có thể lấy thông tin đơn hàng của bạn  để thay thế
       $order = (object)[
          "code" => 'ORDER' . rand(100000, 999999),  // Mã đơn hàng
          "total" => 100000, // Số tiền cần thanh toán (VND)
          "bankCode" => 'NCB',   // Mã ngân hàng
          "type" => "billpayment", // Loại đơn hàng
          "info" => "Thanh toán đơn hàng" // Thông tin đơn hàng
       ];
      
         // Thông tin đơn hàng, thanh toán
        $vnp_TxnRef = $order->code;
        $vnp_OrderInfo = $order->info;
        $vnp_OrderType =  $order->type;
        $vnp_Amount = $order->total * 100; 
        $vnp_Locale = 'vn';
        $vnp_BankCode = $order->bankCode;  // Mã ngân hàng
        $vnp_IpAddr = "127.0.1"; // Địa chỉ IP

        // Tạo input data để gửi sang VNPay server
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
         );
         // Kiểm tra nếu mã ngân hàng đã được thiết lập và không rỗng
         if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
         }
       
         // Kiểm tra nếu thông tin tỉnh/thành phố hóa đơn đã được thiết lập và không rỗng
         if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
             $inputData['vnp_Bill_State'] = $vnp_Bill_State; // Gán thông tin tỉnh/thành phố hóa đơn vào mảng dữ liệu input
         }

         // Sắp xếp mảng dữ liệu input theo thứ tự bảng chữ cái của key
         ksort($inputData);
       
         $query = ""; // Biến lưu trữ chuỗi truy vấn (query string)
         $i = 0; // Biến đếm để kiểm tra lần đầu tiên
         $hashdata = ""; // Biến lưu trữ dữ liệu để tạo mã băm (hash data)

         // Duyệt qua từng phần tử trong mảng dữ liệu input
         foreach ($inputData as $key => $value) {
             if ($i == 1) {
                 // Nếu không phải lần đầu tiên, thêm ký tự '&' trước mỗi cặp key=value
                 $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
             } else {
                 // Nếu là lần đầu tiên, không thêm ký tự '&'
                 $hashdata .= urlencode($key) . "=" . urlencode($value);
                 $i = 1; // Đánh dấu đã qua lần đầu tiên
             }
             // Xây dựng chuỗi truy vấn
             $query .= urlencode($key) . "=" . urlencode($value) . '&';
         }
          
         // Gán chuỗi truy vấn vào URL của VNPay
         $vnp_Url = $vnp_Url . "?" . $query;

         // Kiểm tra nếu chuỗi bí mật hash secret đã được thiết lập
         if (isset($vnp_HashSecret)) {
             // Tạo mã băm bảo mật (Secure Hash) bằng cách sử dụng thuật toán SHA-512 với hash secret
             $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
             // Thêm mã băm bảo mật vào URL để đảm bảo tính toàn vẹn của dữ liệu
             $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
         }
         
          return redirect($vnp_Url);
    }
    public function pay1(Request $request)
    {
        // Thông tin đơn hàng
        $vnp_Url = config('vnpay.vnp_Url');
        $vnp_Returnurl = config('vnpay.vnp_ReturnUrl');
        $vnp_TmnCode = config('vnpay.vnp_TmnCode'); // Mã TMN từ VNPay
        $vnp_HashSecret = config('vnpay.vnp_HashSecret'); // Chuỗi bí mật từ VNPay
        // Thông tin đơn hàng từ form
        $vnp_TxnRef = 'REF' . time(); // Mã đơn hàng (unique)
        $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $vnp_TxnRef;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = $request->input('amount') * 100; // VNPay yêu cầu đơn vị là VND và nhân với 100

        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->input('bank_code') ?? '';

        $vnp_IpAddr = $request->ip();

        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes')); // Thời gian hết hạn


        //Billing
        $vnp_Bill_Mobile = $_POST['txt_billing_mobile']??null;
        $vnp_Bill_Email = $_POST['txt_billing_email']??null;
        $fullName = trim($_POST['txt_billing_fullname']??null);
        if (isset($fullName) && trim($fullName) != '') {
            $name = explode(' ', $fullName);
            $vnp_Bill_FirstName = array_shift($name)??null;
            $vnp_Bill_LastName = array_pop($name)??null;
        }
        $vnp_Bill_FirstName = null;
        $vnp_Bill_LastName = null;
        $vnp_Bill_Address=$_POST['txt_inv_addr1']??null;
        $vnp_Bill_City=$_POST['txt_bill_city']??null;
        $vnp_Bill_Country=$_POST['txt_bill_country']??null;
        $vnp_Bill_State=$_POST['txt_bill_state']??null;
        // Invoice
        $vnp_Inv_Phone=$_POST['txt_inv_mobile']??null;
        $vnp_Inv_Email=$_POST['txt_inv_email']??null;
        $vnp_Inv_Customer=$_POST['txt_inv_customer']??null;
        $vnp_Inv_Address=$_POST['txt_inv_addr1']??null;
        $vnp_Inv_Company=$_POST['txt_inv_company']??null;
        $vnp_Inv_Taxcode=$_POST['txt_inv_taxcode']??null;
        $vnp_Inv_Type=$_POST['cbo_inv_type']??null;

        // Tạo mảng tham số để gửi tới VNPay
        $inputData = [
            "vnp_Version"       => "2.1.0",
            "vnp_TmnCode"       => $vnp_TmnCode,
            "vnp_Amount"        => $vnp_Amount,
            "vnp_Command"       => "pay",
            "vnp_CreateDate"    => date('YmdHis'),
            "vnp_CurrCode"      => "VND",
            "vnp_IpAddr"        => $vnp_IpAddr,
            "vnp_Locale"        => $vnp_Locale,
            "vnp_OrderInfo"     => $vnp_OrderInfo,
            "vnp_OrderType"     => $vnp_OrderType,
            "vnp_ReturnUrl"     => $vnp_Returnurl,
            "vnp_TxnRef"        => $vnp_TxnRef,
            "vnp_ExpireDate"    => $vnp_ExpireDate,

            "vnp_Bill_Mobile"=>$vnp_Bill_Mobile,
            "vnp_Bill_Email"=>$vnp_Bill_Email,
            "vnp_Bill_FirstName"=>$vnp_Bill_FirstName,
            "vnp_Bill_LastName"=>$vnp_Bill_LastName,
            "vnp_Bill_Address"=>$vnp_Bill_Address,
            "vnp_Bill_City"=>$vnp_Bill_City,
            "vnp_Bill_Country"=>$vnp_Bill_Country,
            "vnp_Inv_Phone"=>$vnp_Inv_Phone,
            "vnp_Inv_Email"=>$vnp_Inv_Email,
            "vnp_Inv_Customer"=>$vnp_Inv_Customer,
            "vnp_Inv_Address"=>$vnp_Inv_Address,
            "vnp_Inv_Company"=>$vnp_Inv_Company,
            "vnp_Inv_Taxcode"=>$vnp_Inv_Taxcode,
            "vnp_Inv_Type"=>$vnp_Inv_Type
        ];

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
            $inputData['vnp_Bill_State'] = $vnp_Bill_State;
        }

        // Sắp xếp các tham số theo thứ tự alphabet
        ksort($inputData);

        $query = "";
        $i = 0;
        $hashdata = "";
        // dd($inputData);

        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        $returnData = array('code' => '00'
        , 'message' => 'success'
        , 'data' => $vnp_Url);
        if (isset($_POST['redirect'])) {
            header('Location: ' . $vnp_Url);
            die();
        } else {
            echo json_encode($returnData);
        }

        // $query = rtrim($query, "&");
        // $hashdata = rtrim($hashdata, "&");

        // Tạo hash với SHA256
        // $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        // $secureHash = hash_hmac('sha256', $hashdata,$vnp_HashSecret  );
        // $secureHash = hash('sha256', $hashdata,$vnp_HashSecret );
        // dd($secureHash,$vnpSecureHash);
        // $vnp_Url .= "?" . $query . '&vnp_SecureHash=' . $secureHash;
        // Log::info('hashdata: ' . $hashdata);
        // Log::info('secureHash: ' . $secureHash);
        // dd($vnp_Url);
        return redirect()->away("https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Amount=1806000&vnp_Command=pay&vnp_CreateDate=20240930224100&vnp_CurrCode=VND&vnp_IpAddr=127.0.0.1&vnp_Locale=vn&vnp_OrderInfo=Thanh+toan+don+hang+%3A5&vnp_OrderType=other&vnp_ReturnUrl=https%3A%2F%2Fdomainmerchant.vn%2FReturnUrl&vnp_TmnCode=DEMOV210&vnp_TxnRef=5&vnp_Version=2.1.0&vnp_SecureHash=" . hash_hmac('sha512', $hashdata,$vnp_HashSecret  ));
        // return redirect()->away($vnp_Url);
    }

    /**
     * Nhận kết quả thanh toán từ VNPay.
     */
    public function return(Request $request)
    {
        $vnp_SecureHash = $request->input('vnp_SecureHash');

        // Loại bỏ vnp_SecureHash khỏi mảng
        $inputData = $request->all();
        unset($inputData['vnp_SecureHash']);

        // Sắp xếp lại mảng theo thứ tự key tăng dần
        ksort($inputData);

        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($key !== 'vnp_SecureHash' && strlen($value) > 0) {
                $hashData .= $key . "=" . $value . "&";
            }
        }
        $hashData = rtrim($hashData, "&");

        // Tạo hash để so sánh
        $vnp_HashSecret = config('vnpay.vnp_HashSecret');
        $secureHash = hash_hmac('sha256', $hashData, $vnp_HashSecret);

        if ($vnp_SecureHash === $secureHash) {
            if ($request->input('vnp_ResponseCode') == '00') {
                // Thanh toán thành công
                // Cập nhật trạng thái đơn hàng trong database
                $orderId = substr($request->input('vnp_TxnRef'), 3); // Giả sử order ID là sau 'REF'
                $order = \App\Models\Order::find($orderId);
                if ($order) {
                    $order->status = 'paid';
                    $order->payment_date = now();
                    $order->save();
                }

                return view('vnpay.success');
            } else {
                // Thanh toán thất bại
                return view('vnpay.fail');
            }
        } else {
            // Có sự cố trong việc xác thực hash
            return view('vnpay.invalid');
        }
    }
}
