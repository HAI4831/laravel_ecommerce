@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Thêm div với role="banner" -->
    <div role="banner">
        <h1 class="mb-4 text-center" id="banner" name="banner">Welcome to Perfume Store</h1>
    </div>
    
    <div class="row">
        @foreach ($products as $index => $product)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex align-items-stretch mb-4">
                <div class="card h-100 shadow-sm border-0 position-relative product-card" style="transition: transform 0.3s, box-shadow 0.3s;">
                    <div class="text-center" style="height: 200px; overflow: hidden;">
                        @if($product->image)
                            <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top img-fluid" style="height: 100%; width: auto; object-fit: cover;">
                        @else
                            <img src="{{ asset('images/default.png') }}" alt="No Image" class="card-img-top img-fluid" style="height: 100%; width: auto; object-fit: cover;">
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-truncate" style="height: 40px; overflow: hidden;">{{ Str::limit($product->description, 50) }}</p>
                        <p class="card-text"><strong>Quantity:</strong> {{ $product->quantity }}</p>
                        <p class="card-text"><strong>Price:</strong> {{ number_format($product->price, 0, ',', '.') }} đ</p>
                        <p class="card-text"><strong>Category:</strong> {{ optional($product->category)->name }}</p>
                    </div>
                    <!-- Hover elements start -->
                    <div class="card-footer d-flex justify-content-between align-items-center bg-white mt-auto product-hover-elements" style="height: 100px;">
                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-primary btn-sm">View Details</a>
                        <!-- Add to Cart Form -->
                        <form action="{{ route('carts.store', $product->id) }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <div class="input-group" style="width: 150px;">
                                <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 70px;">
                                <button type="submit" class="btn btn-success btn-sm">Add to Cart</button>
                            </div>
                        </form>
                    </div>

                    <!-- Hover elements end -->
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection

<!-- Additional CSS -->
<style>
    /* Ensure hover elements are hidden initially */
    .product-hover-elements {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }

    /* Show hover elements when the parent card is hovered */
    .product-card:hover .product-hover-elements {
        opacity: 1;
        visibility: visible;
    }

    /* Add some margin to the quantity input */
    .input-group {
        margin-left: 5px; /* Adjust margin for spacing */
    }
</style>
