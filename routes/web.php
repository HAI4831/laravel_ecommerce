<?php

use Illuminate\Support\Facades\Route;
// Import controllers
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\VNPayController;
use App\Mail\PaymentConfirmationMail;
use Illuminate\Support\Facades\Auth;



// Đăng ký và đăng nhập người dùng
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('saveCart');

// Route dành cho admin (sử dụng middleware để kiểm tra quyền)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    Route::resources([
        // Route quản lý sản phẩm
        '/products' => ProductController::class,
        // Route quản lý danh mục
        '/categories' => CategoryController::class,
    ]);
    Route::resource('orders', OrderController::class)->only(['index', 'show']);
    Route::patch('/orders/{order}/updatestatus', [OrderController::class, 'updateStatus'])->name('orders.updatestatus');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// Route cho người dùng bình thường
Route::middleware(['auth'])->group(function () {
    // Trang chủ welcome
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/home', [WelcomeController::class, 'index'])->name('home'); // Đã thêm route home
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show_normal'])->name('products.show');
    
    Route::resource('carts', CartController::class)
        ->only(['index','update', 'destroy']);

    //success route model binding
    Route::post('/carts/{product}', [CartController::class, 'store'])->name('carts.store');
    Route::get('/carts', [CartController::class, 'index'])->name('carts.index');

    Route::post('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('/payment/process', [PaymentController::class, 'process'])->name('payment.process');

    Route::get('/payment/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');

    // Route để bắt đầu thanh toán
    Route::post('/vnpay/pay', [VNPayController::class, 'pay'])->name('vnpay.pay');
    // Route để hiển thị nút thanh toán
    Route::get('/vnpay/checkout', [VNPayController::class, 'checkout'])->name('vnpay.checkout');
    // Route để xử lý kết quả trả về từ VNPay
    Route::get('/vnpay/return', [VNPayController::class, 'return'])->name('vnpay.return');

    
});

