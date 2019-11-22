@extends('layout')

@section('content')
    <h1>{{ __('Index') }}</h1>

    <div class="table">
        <table border="1" cellpadding="3">
            <tr>
                <th align="middle">{{ __('Image') }}</th>
                <th align="middle">{{ __('Title') }}</th>
                <th align="middle">{{ __('Description') }}</th>
                <th align="middle">{{ __('Price') }}</th>
                <th align="middle">{{ __('Add') }}</th>
            </tr>

            @forelse ($products as $product)
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
                    <td><a href="?id={{ $product->id }}">{{ __('Add') }}</a></td>
                </tr>

                @empty
                <tr>
                    <td colspan="5">{{ __('All products in cart') }}</td>
                </tr>
            @endforelse
        </table>
    </div>
@endsection