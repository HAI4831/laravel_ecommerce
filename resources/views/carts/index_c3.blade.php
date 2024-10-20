<!-- @extends('layouts.app')

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
        <!-- Form thanh toán -->
        <form action="{{ route('payment.index') }}" method="POST">
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
                            <input type="checkbox" name="selected_cartItems[{{ $id }}]" value="{{ json_encode($details) }}">
                        </td>
                        <td>
                            <img src="{{ asset('images/' . $details['image']) }}" alt="{{ $details['name'] }}" style="width: 50px; height: auto;"> Hiển thị hình ảnh
                        </td>
                        <td id="nameCart">{{ $details['name'] }}</td>
                        <td>
                            <input type="number" 
                                   name="quantity_{{ $id }}" 
                                   id="quantity_{{ $id }}" 
                                   value="{{ $details['quantity'] }}" 
                                   min="1" 
                                   class="form-control" 
                                   aria-label="Số lượng sản phẩm">
                            <button type="button" class="btn btn-secondary mt-2" 
                                    onClick="handleUpdate(event, {{ $id }})">
                                Cập nhật
                            </button>
                        </td>
                        <td>
                            {{ number_format($details['price'], 0, ',', '.') }} đ
                        </td>
                        <td>{{ $details['category'] }}</td>
                        <td>{{ $details['status'] ?? 'Chưa xác định' }}</td>
                        <td>
                            <a href="{{ route('products.show', $id) }}" class="btn btn-primary">Xem chi tiết</a>
                            <button type="button" class="btn btn-danger" onClick="handleDelete(event, {{ $id }})">Xóa</button>
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

    @push('scripts')
    <script>
        function handleUpdate(event, id) {
            event.preventDefault();

            // Lấy giá trị số lượng mới
            var quantity = document.querySelector(`#quantity_${id}`).value;

            var formData = new FormData();
            formData.append('quantity', quantity);
            formData.append('cartItemId', id);

            // Gửi yêu cầu bằng Fetch API
            fetch(`{{ url('/carts') }}/${id}`, {
                method: 'PUT',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(response => {
                console.log('Phản hồi từ server:', response);
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.indexOf('application/json') !== -1) {
                    return response.json();
                } else {
                    throw new Error('Server không trả về JSON, có thể là lỗi phía server.');
                }
            }).then(data => {
                console.log('Dữ liệu phản hồi:', data);
                if (data.success) {
                    alert('Cập nhật thành công');
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            }).catch(error => {
                console.error('Lỗi khi cập nhật số lượng:', error);
            });
        }

        function handleDelete(event, id) {
    event.preventDefault();
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        fetch(`{{ url('/carts') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(response => {
            console.log('Phản hồi từ server khi xóa:', response);
            // Kiểm tra phản hồi
            if (response.ok) {
                return response.json();
            } else {
                throw new Error('Lỗi khi xóa sản phẩm');
            }
        }).then(data => {
            console.log('Dữ liệu phản hồi khi xóa:', data);
            if (data.success) {
                alert('Xóa thành công');
                location.reload(); // Tải lại trang để cập nhật giỏ hàng
            } else {
                alert('Có lỗi xảy ra khi xóa: ' + data.message);
            }
        }).catch(error => {
            console.error('Lỗi khi xóa sản phẩm:', error);
        });
    }
}

    </script>
    @endpush
@endsection -->
