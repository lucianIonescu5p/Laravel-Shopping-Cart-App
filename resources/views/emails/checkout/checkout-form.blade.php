@component('mail::message')

    Message from: {{ $data['name'] }}

    Email: {{ $data['email'] }}

    Comments: {{ $data['comments'] }}

    Products:

    @component('mail::table')
        <table border="1" cellpadding="3">
            <tr>
                <th align="middle">{{ __('Image') }}</th>
                <th align="middle">{{ __('Title') }}</th>
                <th align="middle">{{ __('Description') }}</th>
                <th align="middle">{{ __('Price') }}</th>
            </tr>

            @foreach ($products as $product)
                <tr>
                    <td align="middle">
                        @if ($product->image)
                            <img alt="{{ __('Product image') }}" src="{{ asset('storage/images/' . $product->image) }}" width="70px" height="70px">
                        @else
                            {{ __('No image here') }}
                        @endif
                    </td>
                    <td align="middle">{{ $product->title }}</td>
                    <td align="middle">{{ $product->description }}</td>
                    <td align="middle">{{ $product->price }}</td>
                </tr>
            @endforeach

            <tr>
                <td colspan="3" align="middle">{{ __('Price') }}</td>
                <td colspan="1" align="middle"><strong>{{ $price }}</strong></td>
            </tr>
        </table>
    @endcomponent
@endcomponent
