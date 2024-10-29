@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Banner Section -->
    <div role="banner" class="mb-5">
        <h1 class="mb-4 text-center" id="banner" name="banner">Welcome to Perfume Store</h1>
    </div>

    <!-- Search and Sort Form -->
    <form action="{{ route('welcome') }}" method="GET" class="mb-4">
        <div class="input-group mb-3 justify-content-center">
            <input type="text" class="form-control rounded-left" id="search-input" placeholder="Tìm kiếm sản phẩm..." name="search" value="{{ request('search', '') }}" autocomplete="off">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary rounded-right" type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>

        <!-- Sort Options -->
        <div class="row mb-3">
            <div class="col-md-6">
                <select class="form-control" id="sort" name="sort" onchange="this.form.submit()">
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên sản phẩm</option>
                    <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Giá</option>
                    <option value="quantity" {{ request('sort') == 'quantity' ? 'selected' : '' }}>Số lượng trong kho</option>
                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Ngày tạo</option>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-control" id="order" name="order" onchange="this.form.submit()">
                    <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                    <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                </select>
            </div>
        </div>
    </form>

   <!-- Automatic Sliding Dashboard -->
<!-- <div id="productCarousel" class="carousel slide mb-4" data-ride="carousel">
    <div class="carousel-inner">
        @foreach ($products as $index => $product)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                <img src="{{ asset('images/' . $product->image) }}" class="d-block w-100 carousel-image" alt="{{ $product->name }}">
            </div>
        @endforeach
    </div>
    <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div> -->


    <div id="search-results" class="list-group" style="position: absolute; z-index: 1000; width: 100%;"></div>

    <!-- Products Grid -->
    <div class="row">
        @forelse ($products as $index => $product)
            <div class="col-md-3 mb-4 product-col" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
                <div class="card h-100 product-card">
                    <!-- Product Image -->
                    <div class="text-center mb-3">
                        <div class="product-image-container">
                            <img src="{{ asset($product->image ? 'images/' . $product->image : 'images/default.png') }}" alt="{{ $product->name }}" class="card-img-top img-fluid product-image">
                        </div>
                    </div>
                    
                    <!-- Product Details -->
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-truncate"><strong>Price:</strong> {{ number_format($product->price, 0, ',', '.') }} đ</p>
                        <p class="card-text"><strong>Số lượng:</strong> {{ $product->quantity }}</p>
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
                                <button type="submit" id="add_productId_{{ $product->id }}" class="btn btn-success">Add</button>
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
        {{ $products->appends(request()->only(['search', 'sort', 'order']))->links() }}
    </div>
</div>
@endsection

<!-- Additional CSS -->
@push('styles')
<style>
    /* Search Input Styles */
    .input-group {
        max-width: 400px; /* Limit search form width */
        margin: 0 auto; /* Center search form */
    }

    /* Carousel Styles */
/* Container carousel */
.carousel {
    max-width: 100%; /* Đảm bảo carousel không vượt quá chiều rộng của màn hình */
    overflow: hidden; /* Ẩn phần hình ảnh tràn ra ngoài */
}

/* Carousel Item */
.carousel-item {
    height: 400px; /* Chiều cao cố định cho mỗi item */
}

.carousel-item img {
    width: 100%; /* Đảm bảo hình ảnh luôn chiếm toàn bộ chiều rộng */
    height: 100%; /* Đảm bảo hình ảnh luôn chiếm toàn bộ chiều cao */
    object-fit: cover; /* Giữ tỷ lệ khung hình cho hình ảnh */
}



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
    box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Shadow around the image */
    border-radius: 10px; /* Optional: Add rounded corners */
    position: relative; /* Make sure the shadow applies correctly */
    transition: box-shadow 0.3s;
}

/* Add a subtle shadow for hover */
.product-image-container:hover {
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Darker shadow on hover */
}

/* Product Image */
.product-image {
    height: 100%;
    width: 100%;
    object-fit: cover; /* Ensure the image covers the container */
    transition: transform 0.3s;
}

/* Scale image slightly on hover */
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

    /* Adjust Search Results Positioning */
    #search-results {
        position: absolute;
        z-index: 1000;
        width: 100%;
    }

    /* Responsive Adjustments */
    @media (max-width: 767.98px) {
        .product-image-container {
            height: 150px;
        }
        .product-image {
            width: 80px;
            height: 80px;
        }
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

        var searchInput = document.getElementById('search-input');
        var searchResults = document.getElementById('search-results');

        searchInput.addEventListener('input', function() {
            var query = this.value;

            if (query.length > 0) {
                fetch('/search?q=' + query)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        data.forEach(item => {
                            var div = document.createElement('div');
                            div.classList.add('list-group-item');
                            div.innerHTML = item.name;
                            div.onclick = () => {
                                window.location.href = '/products/' + item.id;
                            };
                            searchResults.appendChild(div);
                        });
                    });
            } else {
                searchResults.innerHTML = '';
            }
        });
    });
</script>
@endpush
