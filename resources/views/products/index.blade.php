@extends('layout')

@section('title')
    {{ __('Products') }}
@endsection

@section('content')

    <h1>{{ __('Products') }}</h1>

    <div>
        <span><a href="products/create">{{ __('New') }}</a></span>
    </div>

    <div class="table">
        <table border="1" cellpadding="3">
            <tr>
                <th align="middle">{{ __('Id') }}</th>
                <th align="middle">{{ __('Image') }}</th>
                <th align="middle">{{ __('Title') }}</th>
                <th align="middle">{{ __('Description') }}</th>
                <th align="middle">{{ __('Price') }}</th>
                <th align="middle">{{ __('Edit') }}</th>
                <th align="middle">{{ __('Delete') }}</th>
            </tr>

            @forelse ($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>
                    <td>
                        @if ($product->image)
                            <img alt="{{ __('Product image') }}" src="{{ asset('storage/images/' . $product->image) }}" width="70px" height="70px">
                        @else
                            {{ __('No image here') }}
                        @endif
                    </td>
                    <td>{{ $product->title }}</td>
                    <td>{{ $product->description }}</td>
                    <td>{{ $product->price }}</td>
                    <td><a href="products/{{ $product->id }}/edit">{{ __('Edit') }}</a></td>
                    <td>
                        <form method="POST" action="/products/{{ $product->id }}">
                            @method('DELETE')
                            @csrf

                            <input type="submit" name="delete" value="{{ __('Delete') }}">
                        </form>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="5">{{ __('All products in cart') }}</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection