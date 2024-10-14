@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Banner Section -->
    <div role="banner" class="mb-5">
        <h1 class="mb-4 text-center" id="banner" name="banner">Welcome to Perfume Store</h1>
    </div>

    <!-- Search Form -->
    <form action="{{ route('welcome') }}" method="GET" class="mb-4">
        <div class="input-group mb-3">
            <input type="text" class="form-control" id="search-input" 
            placeholder="Tìm kiếm sản phẩm Nguyễn Văn Hải..." 
            name="search" value="{{ $search ?? '' }}" autocomplete="off">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="submit">Tìm kiếm</button>
            </div>
        </div>
        <!-- Sort Options -->
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sort">Sắp xếp theo:</label>
                    <select class="form-control" id="sort" name="sort" onchange="this.form.submit()">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên sản phẩm</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Giá</option>
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="order">Thứ tự:</label>
                    <select class="form-control" id="order" name="order" onchange="this.form.submit()">
                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                        <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                    </select>
                </div>
            </div>
    </form>
    <div id="search-results" class="list-group" style="position: absolute; z-index: 1000; width: 100%;"></div>

    <!-- Products Grid -->
    <div class="row">
        @forelse ($products as $index => $product)
            <div class="col-md-3 mb-4 product-col">
                <div class="card h-100 product-card">
                    <!-- Product Image -->
                    <div class="text-center mb-3">
                        <div class="product-image-container">
                            @if($product->image)
                                <img src="{{ asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="card-img-top img-fluid product-image" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <img src="{{ asset('images/default.png') }}" alt="No Image" class="card-img-top img-fluid product-image" style="width: 100px; height: 100px; object-fit: cover;">
                            @endif
                        </div>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-truncate"><strong>Price:</strong> {{ number_format($product->price, 0, ',', '.') }} đ</p>
                    </div>
                    
                    <!-- Hover Elements -->
                    <div class="card-footer d-flex justify-content-between align-items-center bg-white product-hover-elements">
                        <!-- Details Link -->
                        <form action="{{ route('products.details', $product->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm">Details</button>
                        </form>
                        <!-- Add to Cart Form -->
                        <form action="{{ route('carts.store', $product->id) }}" method="POST" class="d-flex align-items-center">
                            @csrf
                            <div class="input-group input-group-sm">
                                <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 60px;">
                                <button type="submit" class="btn btn-success">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">No products available.</p>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination Display -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>
@endsection

<!-- Additional CSS -->
@push('styles')
<style>
    /* Product Card Styles */
    .product-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }

    /* Product Image Container */
    .product-image-container {
        height: 200px; /* Fixed height for consistency */
        overflow: hidden;
        background-color: #f9f9f9;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Add shadow for image */
    }
    
    .product-image {
        height: 100%;
        width: 100%;
        object-fit: cover; /* Ensure the image covers the container */
        transition: transform 0.3s;
    }
    
    .product-card:hover .product-image {
        transform: scale(1.05);
    }

    /* Hover Elements */
    .product-hover-elements {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s, visibility 0.3s;
    }

    .product-card:hover .product-hover-elements {
        opacity: 1;
        visibility: visible;
    }
</style>
@endpush

<!-- Additional Scripts for Animation -->
@push('scripts')
<!-- AOS Library -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
            mirror: false
        });

        var searchInput = $('#search-input');
        var searchResults = $('#search-results');

        searchInput.on('input', function() {
            var query = $(this).val();

            if (query.length >= 2) {
                $.ajax({
                    url: '{{ route("search.suggestions") }}',
                    method: 'GET',
                    data: { query: query },
                    success: function(data) {
                        searchResults.empty();
                        if (data.length > 0) {
                            $.each(data, function(index, item) {
                                searchResults.append('<a href="#" class="list-group-item list-group-item-action">' + item.name + '</a>');
                            });
                            searchResults.show();
                        } else {
                            searchResults.hide();
                        }
                    }
                });
            } else {
                searchResults.hide();
            }
        });

        $(document).on('click', '#search-results a', function(e) {
            e.preventDefault();
            searchInput.val($(this).text());
            searchResults.hide();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search-input, #search-results').length) {
                searchResults.hide();
            }
        });
    });
</script>
@endpush
