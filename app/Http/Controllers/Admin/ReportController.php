<?php
namespace App\Http\Controllers\Admin;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        // Thống kê tổng doanh thu theo từng danh mục
        $categoryRevenue = DB::table('order_items')
            ->select('products.category_id', DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'paid')
            ->groupBy('products.category_id')
            ->get();

        // Thống kê doanh thu theo từng phương thức thanh toán
        $paymentMethodRevenue = DB::table('orders')
            ->select('payment_method', DB::raw('SUM(amount) as total_revenue'))
            ->where('status', 'paid')
            ->groupBy('payment_method')
            ->get();

        // Tổng số đơn hàng
        $totalOrders = Order::count();

        // Tổng số khách hàng
        $totalCustomers = DB::table('users')->where('role', 'user')->count();

        // Doanh thu theo ngày, tháng, năm
        $revenueByDate = Order::select(DB::raw('DATE(created_at) as date, SUM(amount) as total_revenue'))
            ->where('status', 'paid')
            ->groupBy('date')
            ->get();

        $revenueByMonth = Order::select(DB::raw('MONTH(created_at) as month, SUM(amount) as total_revenue'))
            ->where('status', 'paid')
            ->groupBy('month')
            ->get();

        $revenueByYear = Order::select(DB::raw('YEAR(created_at) as year, SUM(amount) as total_revenue'))
            ->where('status', 'paid')
            ->groupBy('year')
            ->get();

        // Get the selected report type
        $reportType = request('report_type');

        return view('admin.reports.index', compact(
            'categoryRevenue',
            'paymentMethodRevenue',
            'totalOrders',
            'totalCustomers',
            'revenueByDate',
            'revenueByMonth',
            'revenueByYear',
            'reportType'
        ));
    }
}
