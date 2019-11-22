@extends('layout')

@section('title')
    {{ __('Order') }}
@endsection

@section('content')
    <p>{{ __('Order name') . ': ' . $order->name }}</p>
    <p>{{ __('Order email') . ': ' . $order->email }}</p>

    <table border="1" cellpadding="3">
        <tr>
            <th align="middle">{{ __('Image') }}</th>
            <th align="middle">{{ __('Title') }}</th>
            <th align="middle">{{ __('Description') }}</th>
            <th align="middle">{{ __('Price') }}</th>
        </tr>

        @foreach ($order->products as $product)
            <tr>
                <td>
                    @if ($product->image)
                        <img
                                alt="{{ __('Product image') }}"
                                src="{{ asset('storage/images/' . $product->image) }}"
                                width="70px"
                                height="70px">
                    @else
                        {{ __('No image here') }}
                    @endif
                </td>
                <td>{{ $product->title }}</td>
                <td>{{ $product->description }}</td>
                <td>{{ $product->price }}</td>
            </tr>
        @endforeach
    </table>
@endsection