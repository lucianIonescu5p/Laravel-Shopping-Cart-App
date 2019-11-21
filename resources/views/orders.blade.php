@extends('layout')

@section('title')
    {{ __('Orders') }}
@endsection

@section('content')
    <h1>{{ __('Orders') }}</h1>

    <table border="1" cellpadding="3">
        <tr>
            <th align="middle">{{ __('ID') }}</th>
            <th align="middle">{{ __('Name') }}</th>
            <th align="middle">{{ __('Price') }}</th>
            <th align="middle">{{ __('Action') }}</th>
            <th align="middle">{{ __('Created at') }}</th>
        </tr>

        @foreach ($orders as $order)
            <tr>
                <td align="middle">{{ $order->id }}</td>
                <td align="middle">{{ $order->name }}</td>
                <td align="middle">{{ $order->products()->sum('price') }}</td>
                <td align="middle"><a href="order?id={{ $order->id }}">{{ __('View') }}</a></td>
                <td align="middle">{{ $order->created_at }}</td>
            </tr>
        @endforeach
    </table>
@endsection