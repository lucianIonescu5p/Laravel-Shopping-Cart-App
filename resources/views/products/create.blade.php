@extends('layout')

@section('title')
    {{ __('Create product') }}
@endsection

@section('content')
    <form method = "POST" action="/products" enctype="multipart/form-data">
        @csrf

        <div>
            <label for="title">{{ __('Title') }}</label>
            <input type="text" name="title">
        </div>

        <div>
            <label for="description">{{ __('Description') }}</label>
            <textarea name="description" cols="30" rows="10"></textarea>
        </div>

        <div>
            <label for="price">{{ __('Price') }}</label>
            <input type="text" name="price">
        </div>

        <div>
            <label for="image">{{ __('Choose an image') }}</label>
            <input type="file" name="image">
        </div>

        <div>
            <input type="submit" name="submit" value="Submit">
        </div>
    </form>
@endsection