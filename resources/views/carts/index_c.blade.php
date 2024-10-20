@extends('layouts.app')

@section('content')
    <h1>Giỏ hàng của bạn</h1>
    
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

    @if(count($carts) > 0)
        <form action="{{ route('payment.index') }}" method="POST">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th>Chọn</th>
                        <th>Hình ảnh</th> <!-- Thêm cột Hình ảnh -->
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Giá</th>
                        <th>Danh mục</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($carts as $id => $details)
                    <tr>
                        <td>
                            <input type="checkbox" id="product_{{ $id }}" name="selected_cartItems[{{ $id }}]" value="{{ json_encode($details) }}" id="product_{{ $id }}">
                        </td>
                        <td>
                            <img src="{{ asset('images/' . $details['image']) }}" alt="{{ $details['name'] }}" style="width: 50px; height: auto;"> Hiển thị hình ảnh
                        </td>
                        <td id="nameCart">{{ $details['name'] }}</td>
                        <td>
                            <form action="{{ route('carts.store', $id) }}" method="POST" style="display:inline;" onsubmit="return handleUpdate(event)">
                                @csrf
                                <input type="number" 
                                        id="quantityCart"
                                       name="quantity" 
                                       value="{{ $details['quantity'] }}" 
                                       min="1" 
                                       class="form-control">
                                <input type="hidden" 
                                    name="quantityCurrent" 
                                    value="{{ $details['quantity'] }}" 
                                    min="1" 
                                    class="form-control">
                                <button type="submit" class="btn btn-secondary mt-2">Cập nhật</button>
                            </form>
                        </td>
                        <td>
                            {{ number_format($details['price'], 0, ',', '.') }} đ
                        </td>
                        <td>{{ $details['category'] }}</td>
                        <td>{{ $details['status'] ?? 'Chưa xác định' }}</td>
                        <td>
                            <a href="{{ route('products.show', $id) }}" class="btn btn-primary">Xem chi tiết</a>
                            <form action="{{ route('carts.destroy', $id) }}" method="POST" style="display:inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                @csrf
                                @method('DELETE')
                                <button id="delCart" type="submit" class="btn btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>    
            </table>
            <div class="text-center">
                <button type="submit" class="btn btn-success">Tiến hành thanh toán</button>
            </div>
        </form>
    @else
        <p>Giỏ hàng của bạn trống.</p>
    @endif

    <a href="{{ route('welcome') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
@endsection
