@extends('Layouts.layout')

@section('title')
    {{ __('Edit product') }}
@endsection

@section('content')
    <form method="POST" action="/products/{{ $product->id }}" enctype="multipart/form-data">
        @method('PATCH')
        @csrf

        @include('products.form')
    </form>

@endsection