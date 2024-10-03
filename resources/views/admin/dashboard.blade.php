{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4 text-center">Admin Dashboard</h1>
    <p class="text-center">Chào mừng đến với trang quản trị, bạn có thể quản lý sản phẩm, danh mục, và nhiều chức năng khác.</p>
    
    <div class="row mt-4">
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">Quản lý sản phẩm</h5>
                    <p class="card-text">Tạo, cập nhật, và xóa các sản phẩm của cửa hàng.</p>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">Quản lý sản phẩm</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">Quản lý danh mục</h5>
                    <p class="card-text">Tạo, cập nhật, và xóa các danh mục sản phẩm.</p>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Quản lý danh mục</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <h5 class="card-title">Quản lý đơn hàng</h5>
                    <p class="card-text">Xem và xử lý các đơn hàng của khách hàng.</p>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-success">Quản lý đơn hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
