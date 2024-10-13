<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cart;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Hiển thị form đăng ký
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    // Xử lý đăng ký người dùng
    public function register(Request $request)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8', // Tăng độ dài tối thiểu từ 6 lên 8
                'regex:/[A-Z]/', // Yêu cầu ít nhất một chữ cái viết hoa
            ],
            // 'address' => 'nullable|string|max:255', 
            // 'phone' => 'nullable|string|max:20',    
        ], [
            'password.regex' => 'Password phải chứa ít nhất một chữ cái viết hoa.',
            // 'password.min' => 'Password phải có ít nhất :min ký tự.',
            // 'password.confirmed' => 'Password confirmation không khớp.',
            // 'email.unique' => 'Email đã được sử dụng.'
        ]);

        // Tạo người dùng mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address, 
            'phone' => $request->phone,
            'role' => 'user',
            // 'role' => 'admin',
        ]);

        // Đăng nhập người dùng ngay lập tức
        Auth::login($user);

        // Chuyển hướng đến trang chủ hoặc trang khác sau khi đăng ký thành công
        return redirect()->route('home')->with('success', 'Registration successful, you are now logged in.');
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Xử lý đăng nhập
public function login(Request $request)
{
    // Validate dữ liệu đầu vào
    $request->validate([
        'email' => 'required|string|email|max:255', // Removed 'unique:users' from here
        'password' => [
            'required',
            'string',
            'min:8', 
            'regex:/[A-Z]/', 
        ],
    ], [
        'password.regex' => 'Password phải chứa ít nhất một chữ cái viết hoa.',
        'password.min' => 'Password phải có ít nhất :min ký tự.',
    ]);

    // Xác thực người dùng
    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        // Lấy thông tin người dùng đã đăng nhập
        $user = Auth::user();
        // Kiểm tra vai trò của người dùng
        if ($user->role === 'admin') {
            // Nếu là admin, chuyển hướng đến dashboard
            return redirect()->intended(route('admin.dashboard'))->with('success', 'Welcome to Admin Dashboard.');
        }
        // Nếu là user, chuyển hướng đến trang welcome
        return redirect()->intended('home')->with('success', 'You are logged in.');
    }

    // Đăng nhập thất bại
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
}


    // Xử lý đăng xuất
    public function logout(Request $request)
    {
        Auth::logout();
        app(CartController::class)->saveCartToDatabase();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
