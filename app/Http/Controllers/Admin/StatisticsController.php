<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Carbon\Carbon;
use DB;

class StatisticsController extends Controller
{
    // Apply middleware to ensure only admins can access
    public function __construct()
    {
        $this->middleware(['auth', 'admin']); // Assuming 'admin' middleware is defined
    }

    public function index()
    {
        // 1. Total Sales
        $totalSales = Order::where('status', Order::STATUS_PAID)
            ->sum('amount');

        // 2. Sales Over Time (Last 30 Days)
        $salesOverTime = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
            ->where('status', Order::STATUS_PAID)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

            $salesByCategory = OrderItem::select(
                'products.category_id', 
                DB::raw('SUM(order_items.quantity * order_items.price) as total'),
                'categories.name as category_name' // Select the category name
            )
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('categories', 'products.category_id', '=', 'categories.id') // Join with categories
            ->where('orders.status', Order::STATUS_PAID)
            ->groupBy('products.category_id', 'categories.name') // Group by category name
            ->get()
            ->map(function($item) {
                return [
                    'category' => $item->category_name ?? 'Uncategorized', // Access category_name directly
                    'total' => $item->total,
                ];
            });
        

        // 4. Top-Selling Products (Top 5)
        $topSellingProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
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

        // 5. Products Low in Stock (e.g., quantity <= 5)
        $lowStockProducts = Product::where('quantity', '<=', 5)
            ->get(['id', 'name', 'quantity']);

        // 6. Order Status Distribution
        $orderStatusDistribution = Order::select('status', DB::raw('COUNT(*) as count'))
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
            'salesOverTime',
            'salesByCategory',
            'topSellingProducts',
            'lowStockProducts',
            'orderStatusDistribution'
        ));
    }
}
