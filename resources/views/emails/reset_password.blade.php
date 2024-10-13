{{-- resources/views/emails/reset_password.blade.php --}}

@component('mail::message')
# Đặt Lại Mật Khẩu

Chào bạn,

Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng nhấp vào nút bên dưới để đặt lại mật khẩu của bạn:

@component('mail::button', ['url' => url('password/reset', $token)])
    Đặt Lại Mật Khẩu
@endcomponent

Nếu bạn không yêu cầu đặt lại mật khẩu, bạn có thể bỏ qua email này.

Cảm ơn,<br>
{{ config('app.name') }}
@endcomponent
