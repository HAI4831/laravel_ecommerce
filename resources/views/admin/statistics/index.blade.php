@extends('layouts.app')

@section('title', 'Statistics Dashboard')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Statistics Dashboard</h1>

    <!-- Date Range Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <form id="dateRangeForm">
                <div class="form-row">
                <div class="form-group col-md-5">
                    <label for="startDate">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="start_date" 
                        value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    <small class="form-text text-muted">
                        {{ \Carbon\Carbon::parse(request('start_date', now()->startOfMonth()))->format('d/m/Y') }}
                    </small>
                </div>
                    <div class="form-group col-md-5">
                        <label for="endDate">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="end_date" 
                            value="{{ request('end_date', now()->endOfMonth()->format('Y-m-d')) }}">
                        <small class="form-text text-muted">
                            {{ \Carbon\Carbon::parse(request('end_date', now()->endOfMonth()))->format('d/m/Y') }}
                        </small>
                    </div>
                    <div class="form-group col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Sales -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Sales</h5>
                    <p class="card-text" style="font-size: 1.5rem;">{{ number_format($totalSales, 0, ',', '.') }} VND</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Orders</h5>
                    <p class="card-text" style="font-size: 1.5rem;">{{ number_format($totalOrders, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Total Customers</h5>
                    <p class="card-text" style="font-size: 1.5rem;">{{ number_format($totalCustomers, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Order Status Distribution
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Over Time -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Sales Over the Last 30 Days
                </div>
                <div class="card-body">
                    <canvas id="salesOverTimeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales by Category and Top-Selling Products -->
    <div class="row mb-4">
        <!-- Sales by Category -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Sales by Category
                </div>
                <div class="card-body">
                    <canvas id="salesByCategoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top-Selling Products -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Top 5 Selling Products
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($topSellingProducts as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item['product'] }}
                                <span class="badge badge-primary badge-pill">{{ $item['total_quantity'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Low in Stock -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    Products Low in Stock
                </div>
                <div class="card-body">
                    @if($lowStockProducts->isEmpty())
                        <p>No products are low in stock.</p>
                    @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->quantity }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Optional: Add custom styles here -->
@endpush

@push('scripts')
<!-- Include Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Order Status Distribution Chart
        var orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        var orderStatusChart = new Chart(orderStatusCtx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($orderStatusDistribution->pluck('status')) !!},
                datasets: [{
                    data: {!! json_encode($orderStatusDistribution->pluck('count')) !!},
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#17a2b8',
                        '#6f42c1',
                        '#fd7e14',
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });

        // Sales Over Time Chart
        var salesOverTimeCtx = document.getElementById('salesOverTimeChart').getContext('2d');
        var salesOverTimeChart = new Chart(salesOverTimeCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salesOverTime->pluck('date')->toArray()) !!},
                datasets: [{
                    label: 'Total Sales (VND)',
                    data: {!! json_encode($salesOverTime->pluck('total')->toArray()) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor:'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Total Sales: ' + context.parsed.y.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });

        // Sales by Category Chart
        var salesByCategoryCtx = document.getElementById('salesByCategoryChart').getContext('2d');
        var salesByCategoryChart = new Chart(salesByCategoryCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($salesByCategory->pluck('category')) !!},
                datasets: [{
                    label: 'Sales (VND)',
                    data: {!! json_encode($salesByCategory->pluck('total')) !!},
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor:'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return value.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Sales: ' + context.parsed.y.toLocaleString('vi-VN') + ' đ';
                            }
                        }
                    }
                }
            }
        });
    });

    // Submit Date Range Form
    document.getElementById('dateRangeForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        // Redirect to the same page with date range as query params
        window.location.href = `{{ route('admin.reports.index') }}?start_date=${startDate}&end_date=${endDate}`;
    });
</script>
@endpush
