@extends('layouts.app')

@section('title', 'Báo cáo')

@section('content')
<div class="mb-4">
    <h3>Chọn loại báo cáo</h3>
    <form action="{{ route('admin.reports.index') }}" method="GET">
        <select name="report_type" class="form-select" onchange="this.form.submit()">
            <option value="">-- Chọn loại báo cáo --</option>
            <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>Doanh thu theo ngày</option>
            <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>Doanh thu theo tháng</option>
            <option value="category" {{ $reportType == 'category' ? 'selected' : '' }}>Doanh thu theo danh mục</option>
            <option value="payment_method" {{ $reportType == 'payment_method' ? 'selected' : '' }}>Doanh thu theo phương thức thanh toán</option>
        </select>
    </form>
</div>

<div class="container">
    <h1 class="my-4 text-center">Báo cáo</h1>
    
    <div class="text-center mb-4">
        <h4>Tổng số đơn hàng: <span class="text-success">{{ $totalOrders }}</span></h4>
        <h4>Tổng số khách hàng: <span class="text-success">{{ $totalCustomers }}</span></h4>
    </div>

    @if($reportType === 'category')
    <div class="mb-4">
        <h3>Doanh thu theo từng danh mục</h3>
        <table class="table table-bordered">
            <thead class="table-light">
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
    </div>
    @endif

    @if($reportType === 'payment_method')
    <div class="mb-4">
        <h3>Doanh thu theo phương thức thanh toán</h3>
        <table class="table table-bordered">
            <thead class="table-light">
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
    </div>
    @endif

    @if($reportType === 'daily')
    <div class="mb-4">
        <h3>Doanh thu theo ngày</h3>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Ngày</th>
                    <th>Tổng doanh thu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($revenueByDate as $revenue)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($revenue->date)->format('d/m/Y') }}</td>
                    <td>{{ number_format($revenue->total_revenue, 2) }} VND</td>
                </tr> 
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($reportType === 'monthly')
    <div class="mb-4">
        <h3>Doanh thu theo tháng</h3>
        <table class="table table-bordered">
            <thead class="table-light">
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
    </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    .table-bordered {
        border: 1px solid #dee2e6;
    }

    .table-bordered th, .table-bordered td {
        border: 1px solid #dee2e6;
    }
</style>
@endpush
