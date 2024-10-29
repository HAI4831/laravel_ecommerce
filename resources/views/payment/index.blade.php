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
                    <!-- <th>Giá</th> -->
                    <!-- <th>Tổng tiền</th> -->
                </tr>
            </thead>
            <tbody>
                @php $totalAmount = 0; @endphp

                @foreach($cartItemDetails as $cartItemDetail)
                    @php 
                        $quantity = $cartItemDetail['quantity'];
                        $price = $cartItemDetail['price'];
                        $totalPrice = $cartItemDetail['total_price'];
                        $totalAmount += $totalPrice;
                    @endphp

                    <tr>
                        <td>{{ $cartItemDetail['name'] }}</td>
                        <td>
                            <input type="hidden" name="cartItemDetails[{{ $cartItemDetail['id'] }}]" value="{{ json_encode($cartItemDetail) }}" id="product_{{ $cartItemDetail['id'] }}">
                            <input type="number" name="quantity[{{ $cartItemDetail['product_id'] }}]" value="{{ $quantity }}" min="1" class="form-control" readonly>
                        </td>
                        <!-- <td>{{ number_format($price, 0, ',', '.') }} đ</td> -->
                        <!-- <td>{{ number_format($totalPrice, 0, ',', '.') }} đ</td> -->
                    </tr>
                @endforeach

                <!-- <tr>
                    <td colspan="3" class="text-right"><strong>Tổng tiền:</strong></td>
                    <td><strong>{{ number_format($totalAmount, 0, ',', '.') }} đ</strong></td>
                </tr> -->
            </tbody>
        </table>

        <input type="hidden" name="totalAmount" value="{{ $totalAmount }}">

        <!-- Dropdown for discount codes -->
        <div class="form-group">
            <label for="discount_code">Chọn mã giảm giá:</label>
            <select name="discount_code" id="discount_code" class="form-control">
                <option value="">-- Chọn mã giảm giá --</option>
                @foreach($discountCodes as $code)
                    <option value="{{ $code->code }}">{{ $code->code }} ({{ $code->is_percentage ? $code->amount . '%' : number_format($code->amount, 0, ',', '.') . ' đ' }})</option>
                @endforeach
            </select>
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

        <button type="submit" class="btn btn-primary">Thanh toán</button>
    </form>

    <a href="{{ route('carts.index') }}" class="btn btn-secondary">Quay lại giỏ hàng</a>

    <script>
    document.getElementById('discount_code').onchange = function () {
        let originalAmount = {{ $totalAmount }};
        let discountCode = this.value;
        let finalAmount = originalAmount;

        if (discountCode) {
            // Get the selected discount code object
            const selectedCode = @json($discountCodes).find(code => code.code === discountCode);

            if (selectedCode) {
                // Check total quantity of items
                const quantity = Array.from(document.querySelectorAll('input[name^="quantity"]')).reduce((sum, input) => sum + parseInt(input.value), 0);

                // Check conditions for applying discount codes
                if (selectedCode.code === 'DISCOUNT11' && quantity < 200) {
                    alert('Mã giảm giá DISCOUNT11 yêu cầu tổng số lượng phải ít nhất 200.');
                    // Reset the discount code selection
                    this.selectedIndex = 0; // Reset the dropdown to "Chọn mã giảm giá"
                    finalAmount = originalAmount; // Reset to original amount
                } else {
                    // Apply discount based on its type
                    if (selectedCode.is_percentage) {
                        finalAmount *= (1 - (selectedCode.amount / 100)); // Calculate percentage discount
                    } else {
                        finalAmount -= selectedCode.amount; // Calculate fixed amount discount
                    }
                }
            } else {
                alert('Mã giảm giá không hợp lệ.');
                // Reset the discount code selection
                this.selectedIndex = 0; // Reset the dropdown to "Chọn mã giảm giá"
                finalAmount = originalAmount; // Reset to original amount
            }
        }

        // Update the displayed final amount
        document.getElementById('final_amount').innerText = new Intl.NumberFormat().format(finalAmount) + ' đ';
    };
</script>

@endsection
