@component('mail::message')
# Chi Tiết Đơn Hàng

**Mã Đơn Hàng:** {{ $orderData['id'] }}  
**Tên Khách Hàng:** {{ $orderData['customer_name'] }}  
**Ngày:** {{ $orderData['date'] }}

@component('mail::table')
| # | Tên Sản Phẩm       | Số Lượng | Giá (VND) | Thành Tiền (VND) |
|:---|:-------------------|:--------:|:----------:|:----------------:|
@foreach ($orderData['products'] as $index => $product)
| {{ $index + 1 }} | {{ $product['name'] }} | {{ $product['quantity'] }} | {{ number_format($product['price'], 0, ',', '.') }} đ | {{ number_format($product['price'] * $product['quantity'], 0, ',', '.') }} đ |
@endforeach
@endcomponent

**Tổng Cộng:** {{ number_format($orderData['total_amount'], 0, ',', '.') }} đ

@if (!empty($orderData['coupon']))
**Mã Giảm Giá:** {{ $orderData['coupon'] }}
@endif

@component('mail::button', ['url' => $orderData['confirm_url']])
    Xác nhận thanh toán khi nhận hàng
@endcomponent

Cảm ơn,<br>
{{ config('app.name') }}
@endcomponent
