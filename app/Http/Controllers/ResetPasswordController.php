<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    // Phương thức để hiển thị form đặt lại mật khẩu
    public function showResetForm($token)
    {
        return view('auth.reset_password')->with(['token' => $token]);
    }

    // Bạn có thể thêm các phương thức khác cho việc đặt lại mật khẩu tại đây
    public function reset(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required',
        'token' => 'required',
    ]);
    // dd($request);

    $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) {
        $user->forceFill([
            'password' => bcrypt($password),
        ])->save();

        // Bạn có thể thêm logic gửi email thông báo nếu cần
    });

    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', trans($status))
                : back()->withErrors(['email' => trans($status)]);
}

}
