<!DOCTYPE html>
<html>
<head>
    <title>Thanh Toán Thành Công</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 text-center">
    <h2>Thanh Toán Thành Công</h2>
    <p>Mã đơn hàng: {{ $data['vnp_TxnRef'] }}</p>
    <p>Số tiền: {{ number_format($data['vnp_Amount'] / 100, 0, ',', '.') }} VND</p>
    <p>Cảm ơn bạn đã thanh toán. Đơn hàng của bạn đã được xác nhận.</p>
    <a href="{{ route('home') }}" class="btn btn-success">Về Trang Chủ</a>
</div>
</body>
</html>
