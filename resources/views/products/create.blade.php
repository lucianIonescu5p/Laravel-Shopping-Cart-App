@extends('layout')

@section('title')
    {{ __('Create product') }}
@endsection

@section('content')
    <form method = "POST" action="/products" enctype="multipart/form-data">
        @csrf

        @include('products.form')
    </form>
@endsection