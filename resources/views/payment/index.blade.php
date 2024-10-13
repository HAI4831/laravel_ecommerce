@extends('layouts.app') 

@section('content')
    <h1>Thông tin thanh toán</h1>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('payment.process') }}" method="POST">
        @csrf
        <table class="table">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng tiền</th>
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp

                @foreach($cartItemDetails as $cartItemDetail) <!-- Sử dụng $cartItemDetails thay cho $cartDetails -->
                    @php 
                        $quantity = $cartItemDetail['quantity'];
                        $price = $cartItemDetail['price'];
                        $totalPrice = $cartItemDetail['total_price'];
                        $totalAmount += $totalPrice;
                    @endphp

                    <tr>
                        <td>{{ $cartItemDetail['name'] }}</td>
                        <td>
                            <!-- Truyền chi tiết cart item dưới dạng JSON -->
                            <input type="hidden" name="cartItemDetails[{{ $cartItemDetail['id'] }}]" value="{{ json_encode($cartItemDetail) }}" id="product_{{ $cartItemDetail['id'] }}">
                            
                            <!-- Truyền số lượng sản phẩm -->
                            <input type="number" name="quantity[{{ $cartItemDetail['product_id'] }}]" value="{{ $quantity }}" min="1" class="form-control" readonly>
                        </td>
                        <td>{{ number_format($price, 0, ',', '.') }} đ</td>
                        <td>{{ number_format($totalPrice, 0, ',', '.') }} đ</td>
                    </tr>
                @endforeach

                <tr>
                    <td colspan="3" class="text-right"><strong>Tổng tiền:</strong></td>
                    <td><strong>{{ number_format($totalAmount, 0, ',', '.') }} đ</strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Hidden totalAmount để truyền đến server -->
        <input type="hidden" name="totalAmount" value="{{ $totalAmount }}">

        <!-- Mã giảm giá -->
        <div class="form-group">
            <label for="coupon">Mã giảm giá:</label>
            <input type="text" name="coupon" id="coupon" class="form-control" placeholder="Nhập mã giảm giá (nếu có)">
        </div>

        <!-- Phương thức thanh toán -->
        <div class="form-group">
            <label for="payment_method">Chọn phương thức thanh toán:</label>
            <select name="payment_method" id="payment_method" class="form-control" required>
                <option value="pay_now">Thanh toán ngay</option>
                <option value="cash_on_delivery">Thanh toán khi nhận hàng</option>
                <option value="credit_card">Thẻ tín dụng</option>
                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                <option value="paypal">PayPal</option>
                <option value="VNPay">VNPay</option>
            </select>
        </div>

        <!-- Tổng tiền sau khi áp dụng mã giảm giá (nếu có) -->
        <div class="form-group">
            <p><strong>Tổng tiền phải thanh toán: </strong> <span id="final_amount">{{ number_format($totalAmount, 0, ',', '.') }} đ</span></p>
        </div>

        <!-- Nút thanh toán -->
        <button type="submit" class="btn btn-primary">Thanh toán</button>
    </form>

    <a href="{{ route('carts.index') }}" class="btn btn-secondary">Quay lại giỏ hàng</a>

    <script>
        // JavaScript để tính lại tổng tiền khi người dùng nhập mã giảm giá
        document.getElementById('coupon').addEventListener('input', function () {
            let originalAmount = {{ $totalAmount }};
            let couponCode = this.value.trim();

            let finalAmount = originalAmount;
            if (couponCode === 'DISCOUNT10') {
                finalAmount = originalAmount * 0.9;
            }

            document.getElementById('final_amount').innerText = new Intl.NumberFormat().format(finalAmount) + ' đ';
        });
    </script>
@endsection
