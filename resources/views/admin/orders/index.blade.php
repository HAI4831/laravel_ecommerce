@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Danh Sách Đơn Hàng</h1>
    <style>
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }

        /* Điều chỉnh chiều rộng của cột Trạng Thái */
        table td select.form-control {
            width: 180px;
            padding-left: 10px;
        }

        /* Chỉnh sửa hiển thị của biểu tượng */
        .status-icon {
            margin-right: 5px;
        }

        /* Style cho danh sách sản phẩm */
        .order-item-list li {
            list-style: none;
            margin-bottom: 10px;
        }
        
        .order-item-image {
            margin-right: 10px;
        }
    </style>

    <!-- Script để cập nhật icon trạng thái -->
    <script>
        function updateStatusIcon(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const iconClass = selectedOption.getAttribute('data-icon');
            const iconElement = selectElement.previousElementSibling;
            iconElement.className = `fas ${iconClass} status-icon`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const selectElements = document.querySelectorAll('select[name="status"]');
            selectElements.forEach(selectElement => {
                updateStatusIcon(selectElement);
                selectElement.addEventListener('change', function () {
                    updateStatusIcon(this);
                });
            });
        });
    </script>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('message'))
        <div class="alert alert-info">
            {{ session('message') }}
        </div>
    @endif

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
                    <th>Chi Tiết Sản Phẩm</th>
                    <th>In Hóa Đơn</th>
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
                        <td>{{ $order->payment_method }}</td>

                        <td>
                            <form action="{{ route('admin.orders.updatestatus', $order->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <i class="status-icon" style="inline"></i>
                                <select name="status" onchange="this.form.submit()" class="form-control">
                                    <option data-icon="fa-clock" value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Đang chờ</option>
                                    <option data-icon="fa-check-circle" value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                    <option data-icon="fa-money-check-alt" value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option data-icon="fa-truck" value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang vận chuyển</option>
                                    <option data-icon="fa-check-double" value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                    <option data-icon="fa-times-circle" value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                                    <option data-icon="fa-exclamation-circle" value="failed" {{ $order->status == 'failed' ? 'selected' : '' }}>Thất bại</option>
                                </select>
                            </form>
                        </td>

                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm">Xem Chi Tiết</a>
                        </td>
                        <td>
                            <ul class="order-item-list">
                                @foreach($order->orderItems as $item)
                                    <li>
                                        <img src="{{ asset('images/' . str_replace(' ', '_', $item->product->image)) }}" alt="{{ $item->product->name }}" width="50" class="order-item-image"> 
                                        {{ $item->name }} ({{ $item->quantity }}) - {{ number_format($item->price, 0, ',', '.') }} đ
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            @if($order->status === 'paid')
                                <form action="{{ route('admin.orders.printInvoice', $order->id) }}" method="GET">
                                    @csrf
                                    <button type="submit" id="buttonOrderId_{{$order->id}}" class="btn btn-success btn-sm">In Hóa Đơn</button>
                                </form>
                            @else
                                <span class="text-muted">Không thể in</span>
                            @endif
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
