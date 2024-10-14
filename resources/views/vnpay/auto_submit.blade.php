<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to VNPay</title>
</head>
<body>
    <p>Redirecting to VNPay, please wait...</p>
    
    <form id="vnpayForm" action="{{ route('vnpay.pay') }}" method="POST">
        @csrf
        <input type="hidden" name="amount" value="{{ $amount }}">
        <input type="hidden" name="order_id" value="{{ $order_id }}">
        <!-- Add other required fields for VNPay -->
    </form>

    <script type="text/javascript">
        document.getElementById('vnpayForm').submit();
    </script>
</body>
</html>
