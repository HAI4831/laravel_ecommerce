@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Thông Tin Chi Tiết Sản Phẩm -->
    <div class="row">
        <div class="col-md-6">
            <!-- Hình Ảnh Sản Phẩm -->
            <div class="text-center">
                @if($product->image)
                    <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="img-fluid" style="height: 400px; object-fit: cover;">
                @else
                    <img src="{{ asset('images/default.png') }}" alt="No Image" class="img-fluid" style="height: 400px; object-fit: cover;">
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <!-- Thông Tin Sản Phẩm -->
            <h3>{{ $product->name }}</h3>
            <p><strong>Price:</strong> {{ number_format($product->price, 0, ',', '.') }} đ</p>
            <p><strong>Quantity:</strong> {{ $product->quantity }}</p>
            <p><strong>Category:</strong> {{ optional($product->category)->name }}</p>
            <p>{{ $product->description }}</p>

            <!-- Phần Đánh Giá -->
            <div class="rating mb-3">
                <h5>Product Rating</h5>
                <div>
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= round($product->averageRating()))
                            <span class="fa fa-star text-warning"></span>
                        @else
                            <span class="fa fa-star text-secondary"></span>
                        @endif
                    @endfor
                    <span>({{ $product->ratings->count() }} ratings)</span>
                </div>
            </div>

            <!-- Form Đánh Giá -->
            @auth
            <div class="rating-form mb-3">
                <h5>Đánh Giá Sản Phẩm</h5>
                <form action="{{ route('ratings.store', $product->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="rating">Chọn Đánh Giá:</label>
                        <select name="rating" id="rating" class="form-control" required>
                            <option value="">-- Chọn --</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ ($userRating->rating ?? '') == $i ? 'selected' : '' }}>
                                    {{ $i }} - {{ \App\Models\Rating::getRatingLabel($i) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Gửi Đánh Giá</button>
                </form>
            </div>
            @else
                <p>Please <a href="{{ route('login') }}">login</a> to rate this product.</p>
            @endauth

            <!-- Form Thêm Vào Giỏ Hàng -->
            <form action="{{ route('carts.store', $product->id) }}" method="POST">
                @csrf
                <div class="input-group mb-3" style="width: 150px;">
                    <input type="number" name="quantity" value="1" min="1" class="form-control" required>
                    <button type="submit" class="btn btn-success">Add to Cart</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Phần Bình Luận -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h4>Customer Reviews</h4>

            @forelse ($product->comments as $comment)
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $comment->user->name ?? 'Khách' }}</h5>
                        <p class="card-text">{{ $comment->comment }}</p>
                        <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            @empty
                <p>Chưa có bình luận nào.</p>
            @endforelse

            <!-- Form Thêm Bình Luận -->
            @auth
            <div class="mt-4">
                <h5>Add a Comment</h5>
                <form action="{{ route('comments.store', $product->id) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Nhập bình luận của bạn..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Submit Comment</button>
                </form>
            </div>
            @else
                <p>Please <a href="{{ route('login') }}">login</a> to add a comment.</p>
            @endauth
        </div>
    </div>
</div>
@endsection

<!-- Additional CSS -->
@push('styles')
<style>
    /* Đảm bảo phần đánh giá ban đầu hiển thị đúng */
    .rating .fa-star {
        font-size: 1.2em;
    }

    /* Form đánh giá */
    .rating-form select {
        width: 100%;
    }

    /* Styling cho bình luận khi không có hình ảnh người dùng */
    .card-title {
        display: flex;
        align-items: center;
    }

    .card-title::before {
        content: "\f007"; /* Font Awesome user icon */
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        margin-right: 8px;
    }
</style>
@endpush
