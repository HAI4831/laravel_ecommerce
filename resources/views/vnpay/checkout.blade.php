<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Thanh Toán</h2>
    <form action="{{ route('vnpay.pay') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="amount" class="form-label">Số Tiền (VND)</label>
            <input type="number" class="form-control" id="amount" name="amount" value="100000" required>
        </div>
        <div class="mb-3">
            <label for="bank_code" class="form-label">Ngân Hàng</label>
            <select class="form-select" id="bank_code" name="bank_code">
                <option value="">Không chọn</option>
                <option value="NCB">NCB</option>
                <option value="AGRIBANK">Agribank</option>
                <option value="SCB">SCB</option>
                <option value="SACOMBANK">Sacombank</option>
                <option value="EXIMBANK">Eximbank</option>
                <option value="MB">MB Bank</option>
                <option value="VNMART">VnMart</option>
                <!-- Thêm các ngân hàng khác nếu cần -->
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Thanh Toán Qua VNPay</button>
    </form>
</div>
</body>
</html>
