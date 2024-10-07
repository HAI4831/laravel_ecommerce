@extends('layouts.app')

@section('title', 'Báo cáo')

@section('content')
<div class="container">
    <h1>Báo cáo</h1>
    <div class="mt-4">
        <h4>Tổng số đơn hàng: {{ $totalOrders }}</h4>
        <h4>Tổng số khách hàng: {{ $totalCustomers }}</h4>
    </div>

    <h3>Doanh thu theo từng danh mục</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Danh mục</th>
                <th>Tổng doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryRevenue as $revenue)
            <tr>
                <td>{{ $revenue->category_id }}</td>
                <td>{{ number_format($revenue->total_revenue, 2) }} VND</td>
            </tr> 
            @endforeach
        </tbody>
    </table>

    <h3>Doanh thu theo phương thức thanh toán</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Phương thức thanh toán</th>
                <th>Tổng doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentMethodRevenue as $revenue)
            <tr>
                <td>{{ $revenue->payment_method }}</td>
                <td>{{ number_format($revenue->total_revenue, 2) }} VND</td>
            </tr> 
            @endforeach
        </tbody>
    </table>

    <h3>Doanh thu theo ngày</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Ngày</th>
                <th>Tổng doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByDate as $revenue)
            <tr>
                <td>{{ $revenue->date }}</td>
                <td>{{ number_format($revenue->total_revenue, 2) }} VND</td>
            </tr> 
            @endforeach
        </tbody>
    </table>

    <h3>Doanh thu theo tháng</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Tháng</th>
                <th>Tổng doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByMonth as $revenue)
            <tr>
                <td>{{ $revenue->month }}</td>
                <td>{{ number_format($revenue->total_revenue, 2) }} VND</td>
            </tr> 
            @endforeach
        </tbody>
    </table>

    <h3>Doanh thu theo năm</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Năm</th>
                <th>Tổng doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($revenueByYear as $revenue)
            <tr>
                <td>{{ $revenue->year }}</td>
                <td>{{ number_format($revenue->total_revenue, 2) }} VND</td>
            </tr> 
            @endforeach
        </tbody>
    </table>
</div> 
@endsection
