<!-- @extends('layouts.app')

@section('title', 'Chi Tiết Đơn Hàng')

@section('content')
<div class="container">
    <h1 class="mb-4">Chi Tiết Đơn Hàng: {{ $order->txn_ref }}</h1>

    <!-- Customer and Order Information -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Thông Tin Đơn Hàng
        </div>
        <div class="card-body">
            <p><strong>Khách Hàng:</strong> {{ $order->customer_name }}</p>
            <p><strong>Email:</strong> {{ $order->user->email }}</p>
            <p><strong>Số Điện Thoại:</strong> {{ $order->user->phone ?? 'N/A' }}</p>
            <p><strong>Địa Chỉ:</strong> {{ $order->user->address ?? 'N/A' }}</p>
            <p><strong>Tổng Tiền:</strong> {{ number_format($order->amount, 0, ',', '.') }} VND</p>
            <p><strong>Trạng Thái:</strong> {{ ucfirst($order->status) }}</p>
            <p><strong>Ngày Đặt Hàng:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
            @if($order->payment_date)
                <p><strong>Ngày Thanh Toán:</strong> {{ $order->payment_date->format('d/m/Y H:i') }}</p>
            @endif
        </div>
    </div>

    <!-- Ordered Products -->
    <h3>Sản Phẩm Đã Đặt</h3>
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Hình Ảnh</th>
                <th>Tên Sản Phẩm</th>
                <th>Số Lượng</th>
                <th>Giá</th>
                <th>Thành Tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $item)
                <tr>
                    <td>
                        <img src="{{ asset('images/' . $item->product->image) }}" alt="{{ $item->product->name }}" width="50">
                    </td>
                    <td>{{ $item->name }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price, 0, ',', '.') }} VND</td>
                    <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VND</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Total Amount -->
    <div class="text-right">
        <h4>Tổng Cộng: {{ number_format($order->amount, 0, ',', '.') }} VND</h4>
    </div>

    <!-- Back Button -->
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary mt-3">Quay Lại Danh Sách Đơn Hàng</a>
</div>
@endsection -->
