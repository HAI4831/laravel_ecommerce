@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Danh Sách Đơn Hàng</h1>

    @if($orders->count())
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã Đơn Hàng</th>
                    <th>Khách Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Phương thức thanh toán</th>
                    <th>Trạng Thái</th>
                    <th>Ngày Đặt Hàng</th>
                    <th>Hành Động</th>
                    <th>Chi Tiết Sản Phẩm</th> <!-- Thêm cột cho chi tiết sản phẩm -->
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->txn_ref }}</td>
                        <td>
                            @if(Auth::user()->is_admin)
                                {{ $order->user->name }} ({{ $order->user->email }})
                            @else
                                {{ $order->customer_name }}
                            @endif
                        </td>
                        
                        <td>{{ number_format($order->amount, 0, ',', '.') }} đ</td>
                        <td>
                            {{ $order->payment_method}}
                        </td>
                        <!-- <td>{{ ucfirst($order->status) }}</td> -->
                        <td>
                            <!-- Form để thay đổi trạng thái đơn hàng -->
                            <form action="{{ route('admin.orders.updatestatus', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="form-control">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang vận chuyển</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                    <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                                    <option value="failed" {{ $order->status == 'failed' ? 'selected' : '' }}>Thất bại</option>
                                </select>
                            </form>
                        </td>
                        
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem Chi Tiết</a>
                        </td>
                        <td>
                            <ul>
                                @foreach($order->orderItems as $item)
                                    <li>
                                        <img src="{{ asset('images/' . $item->product->image) }}" alt="{{ $item->product->name }}" width="50"> <!-- Hiển thị hình ảnh -->
                                        {{ $item->name }} ({{ $item->quantity }}) - {{ number_format($item->price, 0, ',', '.') }} đ
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $orders->links() }} <!-- Hiển thị pagination -->
    @else
        <p>Không có đơn hàng nào để hiển thị.</p>
    @endif
    <a href="{{ route('welcome') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
</div>
@endsection
