<?php
// app/Http/Controllers/Auth/ForgotPasswordController.php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        // Tạo token đặt lại mật khẩu
        $status = Password::sendResetLink(
            $request->only('email')
        );
        // if ($status !== Password::RESET_LINK_SENT) {
        //     \Log::error('Error sending reset link: ' . trans($status));
        //     return back()->withErrors(['email' => trans($status)]);
        // }

        // // Gửi email nếu thành công
        // if ($status === Password::RESET_LINK_SENT) {
        //     // Tìm bản ghi trong bảng password_resets
        //     $resetRecord = \DB::table('password_resets')->where('email', $request->email)->first();
        //     // Kiểm tra xem bản ghi có tồn tại hay không
        //     if ($resetRecord) {
        //         $token = $resetRecord->token; // Truy cập token
        //         Mail::to($request->email)->send(new ResetPasswordMail($token));
        //     } else {
        //         // Nếu không tìm thấy bản ghi, có thể bạn muốn xử lý theo cách khác
        //         return back()->withErrors(['email' => 'Không tìm thấy yêu cầu đặt lại mật khẩu.']);
        //     }

        //     return back()->with('status', trans($status));
        // }

        return back()->withErrors(['email' => trans($status)]);
    }
}
