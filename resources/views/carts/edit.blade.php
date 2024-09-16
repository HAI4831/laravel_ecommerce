@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Edit Cart Item</h2>
            </div>
            <div class="pull-right">
                <a class="btn btn-primary" href="{{ route('carts.index') }}"> Back to Cart</a>
            </div>
        </div>
    </div>

    <form action="{{ route('carts.update', $productId) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Product:</strong>
                    <select name="product_id" class="form-control" required>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" {{ $product->id == $productId ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>Quantity:</strong>
                    <input type="number" name="quantity" class="form-control" value="{{ $quantity }}" placeholder="Quantity" min="1" required>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Update Cart</button>
            </div>
        </div>
    </form>
</div>
@endsection
