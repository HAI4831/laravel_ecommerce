@extends('layout.app')

@section('content')
    <h1>Edit Profile</h1>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Example of profile fields -->
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ $user->name }}" class="form-control">
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
@endsection
