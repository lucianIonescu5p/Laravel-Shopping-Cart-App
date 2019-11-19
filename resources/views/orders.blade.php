@extends('layout')

@section('title')
    {{ __('Orders') }}
@endsection

@section('content')
    <h1>{{ __('Orders') }}</h1>

    <ul>
        @foreach ($orders as $order)
            <li>{{ __('Order: ') . $order->id .
                   __(' Name: ') . $order->name .
                   __(' email: ') . $order->email .
                   __(' price: ' . $order->price) }}</li>
        @endforeach
    </ul>
@endsection