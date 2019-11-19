@extends('layout')

@section('title')
    {{ __('Cart') }}
@endsection

@section('content')
    <h1>{{ __('Cart') }}</h1>

    @if(!$cart)
        @if(request()->has('success'))
            <p>{{ __('Order sent successfully') }}</p>
        @endif

        <p>{{ __('Cart is empty') }}</p>
    @else
        <div class="table">
            <table border="1" cellpadding="3">
                <tr>
                    <th align="middle">{{ __('Image') }}</th>
                    <th align="middle">{{ __('Title') }}</th>
                    <th align="middle">{{ __('Description') }}</th>
                    <th align="middle">{{ __('Price') }}</th>
                    <th align="middle">{{ __('Add') }}</th>
                </tr>

                @foreach ($products as $product)
                    <tr>
                        <td align="middle">

                            @if ($product->image)
                                <img alt="{{ __('Product image') }}" src="{{ asset('storage/' . $product->image) }}" width="70px" height="70px">
                            @else
                                {{ __('No image here') }}
                            @endif

                        </td>
                        <td align="middle">{{ $product->title }}</td>
                        <td align="middle">{{ $product->description }}</td>
                        <td align="middle">{{ $product->price }}</td>
                        <td align="middle"><a href="?id={{ $product->id }}">{{ __('Remove') }}</a></td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" align="middle">
                        {{ __('Price') }}
                    </td>
                    <td colspan="2" align="middle"><strong>{{ $price }}</strong></td>
                </tr>
            </table>
        </div>

        <div>
            <form method="POST">
                @csrf

                <div>
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" >

                    @if ($errors->has('name'))
                        <p>{{ $errors->first('name') }}</p>
                    @endif
                </div>

                <div>
                    <label for="email">{{ __('eMail') }}</label>
                    <input type="text" name="email" value="{{ old('email') }}">

                    @if ($errors->has('email'))
                        <p>{{ $errors->first('email') }}</p>
                    @endif
                </div>

                <div>
                    <label for="comments">{{ __('Comments') }}</label>
                    <textarea name="comments">{{ old('comments') }}</textarea>

                    @if ($errors->has('comments'))
                        <p>{{ $errors->first('comments') }}</p>
                    @endif
                </div>

                <div>
                    <input type="submit" name="submit" value="{{ __('Submit') }}">
                </div>
            </form>
        </div>
    @endif
@endsection