<?php

use Illuminate\Support\Facades\Route;
// Import controllers
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ForgotPasswordController;
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
use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;


// Đăng ký và đăng nhập người dùng
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('saveCart');
Route::get('password/forget', function () {
    return view('emails.forgot_password'); // Tạo view cho form quên mật khẩu
});
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::post('password/forget', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
// Route để xử lý việc đặt lại mật khẩu
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Route dành cho admin (sử dụng middleware để kiểm tra quyền)
Route::middleware(['auth', 'admin','all'])->prefix('admin')->name('admin.')->group(function () {
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
    // Admin Statistics Route
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');
});

// Route cho người dùng bình thường
Route::middleware(['auth'])->group(function () {
    // Trang chủ welcome
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

    Route::get('/payment/email/confirm', [PaymentController::class, 'confirmEmail'])->name('payment.email.confirm');

    // Route để bắt đầu thanh toán
    Route::post('/vnpay/pay', [VNPayController::class, 'pay'])->name('vnpay.pay');
    // Route để hiển thị nút thanh toán
    Route::get('/vnpay/checkout', [VNPayController::class, 'checkout'])->name('vnpay.checkout');
    // Route để xử lý kết quả trả về từ VNPay
    Route::get('/vnpay/return', [VNPayController::class, 'return'])->name('vnpay.return');
    // Xử lí comment
    Route::post('/products/{product}/comments', [CommentController::class, 'store'])->name('comments.store');
    //product details
    Route::post('/products/{product}', [ProductController::class, 'details'])->name('products.details');
    Route::post('/products/{product}/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::get('/search/suggestions', [ProductController::class, 'searchSuggestions'])->name('search.suggestions');
    
    // Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    // Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.update-profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('admin/orders/{id}/print-invoice', [OrderController::class, 'printInvoice'])->name('admin.orders.printInvoice');
   });

