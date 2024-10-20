<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // Date filter
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->subDays(30);
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : now();

        // Total Sales
        $totalSales = Order::where('status', Order::STATUS_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Total Orders
        $totalOrders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Total Customers
        $totalCustomers = Order::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');

        // Sales Over Time
        $salesOverTime = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->where('status', Order::STATUS_PAID)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Sales by Category
        $salesByCategory = OrderItem::select(
            'products.category_id', 
            DB::raw('SUM(order_items.quantity * order_items.price) as total'),
            'categories.name as category_name'
        )
        ->join('products', 'order_items.product_id', '=', 'products.id')
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->where('orders.status', Order::STATUS_PAID)
        ->whereBetween('orders.created_at', [$startDate, $endDate])
        ->groupBy('products.category_id', 'categories.name')
        ->get()
        ->map(function($item) {
            return [
                'category' => $item->category_name ?? 'Uncategorized',
                'total' => $item->total,
            ];
        });

        // Top-Selling Products
        $topSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', Order::STATUS_PAID)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->groupBy('product_id')
            ->orderBy('total_quantity', 'DESC')
            ->with('product')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'product' => $item->product->name ?? 'Unknown Product',
                    'total_quantity' => $item->total_quantity,
                ];
            });

        // Products Low in Stock
        $lowStockProducts = Product::where('quantity', '<=', 5)
            ->get(['id', 'name', 'quantity']);

        // Order Status Distribution
        $orderStatusDistribution = Order::select('status', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('status')
            ->get()
            ->map(function($item) {
                return [
                    'status' => ucfirst($item->status),
                    'count' => $item->count,
                ];
            });

        return view('admin.statistics.index', compact(
            'totalSales',
            'totalOrders',
            'totalCustomers',
            'salesOverTime',
            'salesByCategory',
            'topSellingProducts',
            'lowStockProducts',
            'orderStatusDistribution',
            'startDate',
            'endDate'
        ));
    }
}
