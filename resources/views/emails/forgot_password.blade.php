{{-- resources/views/auth/forgot_password.blade.php --}}

@extends('layouts.app') {{-- Kế thừa layout chính của bạn --}}

@section('content')
<div class="container">
    <h1>Quên Mật Khẩu</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group">
            <label for="email">Địa Chỉ Email</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Gửi Link Đặt Lại Mật Khẩu</button>
    </form>
</div>
@endsection
