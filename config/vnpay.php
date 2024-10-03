<?php

return [
    'vnp_TmnCode'    => env('VNPAY_TMN_CODE'), // Mã thương nhân (Terminal Code)
    'vnp_Url'        => env('VNPAY_API_URL'), // URL thanh toán VNPay
    'vnp_ReturnUrl'  => env('VNPAY_RETURN_URL'), // URL nhận kết quả thanh toán
    'vnp_HashSecret' => env('VNPAY_HASH_SECRET'), // Chuỗi bí mật
];
