<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyShop')</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* Light background color */
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Shadow for navbar */
        }
        .nav-link {
            transition: color 0.3s, transform 0.3s; /* Add transform transition */
        }
        .nav-link:hover {
            color: #007bff; /* Hover color */
            transform: scale(1.05); /* Scale up on hover */
        }
        .modal-content {
            border-radius: 10px; /* Rounded corners for modal */
        }
        .list-group-item {
            background-color: #e9ecef; /* Background color for list items */
        }
        .list-group-item:hover {
            background-color: #d3d3d3; /* Darker background on hover */
        }
        .icon-animation {
            transition: transform 0.3s; /* Transition for icons */
        }
        .icon-animation:hover {
            transform: rotate(360deg); /* Rotate on hover */
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="{{ url('/') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Shop Electronics" style="height: 160px;" class="mr-2"> 
        <!-- Shop Electronics -->
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" 
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            @auth
                @if(Auth::user()->role !== 'admin')
                    <li class="nav-item active">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="fas fa-home"></i> Home <span class="sr-only">(current)</span>
                        </a>
                    </li>
                @endif
                
                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'all')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt icon-animation"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.products.index') }}">
                            <i class="fas fa-box icon-animation"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.categories.index') }}">
                            <i class="fas fa-tags icon-animation"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.reports.index') }}">
                            <i class="fas fa-chart-line icon-animation"></i> Reports
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.orders.index') }}">
                            <i class="fas fa-shopping-cart icon-animation"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.statistics.index') }}">
                            <i class="fas fa-chart-bar icon-animation"></i> Statistics
                        </a>
                    </li>
                @endif
                
                @if(Auth::user()->role === 'user' || Auth::user()->role === 'all')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('carts.index') }}">
                            <i class="fas fa-shopping-basket icon-animation"></i> Cart
                        </a>
                    </li>
                @endif
            @else
                <li class="nav-item active">
                    <a class="nav-link" href="{{ url('/') }}">
                        <i class="fas fa-home"></i> Home <span class="sr-only">(current)</span>
                    </a>
                </li>
            @endauth
        </ul>
        <ul class="navbar-nav">
            @guest
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('register') }}">
                        <i class="fas fa-user-plus icon-animation"></i> Register
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt icon-animation"></i> Login
                    </a>
                </li>
            @else
                <li class="nav-item">
                    <a class="nav-link" href="#" data-toggle="modal" data-target="#profileModal">
                        <i class="fas fa-user icon-animation"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt icon-animation"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </li>
            @endguest
        </ul>
    </div>
</nav>


    <div class="container mt-4">
        @yield('content')
    </div>

    @auth
        <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="profileModalLabel">Your Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            <li class="list-group-item"><strong>Name:</strong> {{ Auth::user()->name }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ Auth::user()->email }}</li>
                            @if(Auth::user()->phone)
                                <li class="list-group-item"><strong>Phone:</strong> {{ Auth::user()->phone }}</li>
                            @endif
                            @if(Auth::user()->address)
                                <li class="list-group-item"><strong>Address:</strong> {{ Auth::user()->address }}</li>
                            @endif
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>
            </div>
        </div>
    @endauth
    
    @stack('scripts')   
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>
