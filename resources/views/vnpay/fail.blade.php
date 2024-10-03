<!DOCTYPE html>
<html>
<head>
    <title>Thanh Toán Thất Bại</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5 text-center">
    <h2>Thanh Toán Thất Bại</h2>
    <p>Rất tiếc, thanh toán của bạn đã bị hủy. Vui lòng thử lại.</p>
    <p>Mã đơn hàng: {{ $data['vnp_TxnRef'] }}</p>
    <p>Lý do: {{ $data['vnp_ResponseCode'] }}</p>
    <a href="{{ route('checkout') }}" class="btn btn-danger">Thử Lại</a>
</div>
</body>
</html>
