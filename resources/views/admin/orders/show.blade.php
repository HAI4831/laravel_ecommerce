@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Chi Tiết Đơn Hàng: {{ $order->txn_ref }}</h1>

    <div class="mb-4">
        <strong>Khách Hàng:</strong> {{ $order->customer_name }}<br>
        <strong>Email:</strong> {{ $order->user->email }}<br>
        <strong>Số Điện Thoại:</strong> {{ $order->user->phone ?? 'N/A' }}<br>
        <strong>Địa Chỉ:</strong> {{ $order->user->address ?? 'N/A' }}<br>
        <strong>Tổng Tiền:</strong> {{ number_format($order->amount, 0, ',', '.') }} VND<br>
        <strong>Trạng Thái:</strong> {{ ucfirst($order->status) }}<br>
        <strong>Ngày Đặt Hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}<br>
        @if($order->payment_date)
            <strong>Ngày Thanh Toán:</strong> {{ $order->payment_date->format('d/m/Y H:i') }}<br>
        @endif
    </div>

    <h3>Sản Phẩm Đã Đặt</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Hình Ảnh</th> <!-- Thêm cột hình ảnh -->
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Giá (VND)</th>
                <th>Tổng Tiền (VND)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                <tr>
                    <td>
                        <img src="{{ asset('images/' . $item->product->image) }}" alt="{{ $item->product->name }}" width="50"> <!-- Hiển thị hình ảnh -->
                    </td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Quay lại Danh Sách Đơn Hàng</a>
</div>
@endsection
