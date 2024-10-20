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
        <form id="checkoutForm" action="{{ route('payment.index') }}" method="POST">
            @csrf
            <table class="table">
                <thead>
                    <tr>
                        <th>Chọn</th>
                        <th>Hình ảnh</th>
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
                            <input type="checkbox" id="product_{{ $id }}" name="selected_cartItems[{{ $id }}]" value="{{ json_encode($details) }}">
                        </td>
                        <td>
                            <img src="{{ asset('images/' . $details['image']) }}" alt="{{ $details['name'] }}" style="width: 50px; height: auto;"> 
                        </td>
                        <td id="nameCart">{{ $details['name'] }}</td>
                        <form></form>
                        <td>
                            <form action="{{ route('carts.update', $id) }}" method="POST" class="updateForm" id="updateQuantity_{{ $id }}"> 
                                @csrf
                                @method('PUT')
                                <input type="number" 
                                       name="quantity" 
                                       value="{{ $details['quantity'] }}" 
                                       min="1" 
                                       class="form-control" 
                                       aria-label="Số lượng sản phẩm">
                                <input type="hidden" 
                                name="cartItemId" 
                                value="{{ $id }}">
                                <button type="button" class="btn btn-secondary mt-2" 
                                onClick="handleUpdate(event, {{ $id }})">Cập nhật</button>
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

    <script>
        function handleUpdate(event, id) {
            // Prevent the default button action
            event.preventDefault();

            // Find the form by its ID
            var form = document.getElementById('updateQuantity_' + id);

            // Check if form is found
            if (form) {
                // Submit the form
                form.submit();
            } else {
                console.error('Form not found for item ID:', id);
            }
        }
    </script>
@endsection
