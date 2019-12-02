@extends('Layouts.layout')

@section('title')
    {{ __('Login') }}
@endsection

@section('content')
    <h1>{{ __('Login') }}</h1>

    @if (request()->has('unauthorized'))
        <p>{{ __('You must be logged in to access that page') }}</p>
    @endif

    <form method="POST">
        @csrf

        <div>
            <label for="name">{{ __('Username') }}</label>
            <input type="text" name="username" value="{{ old('name') }}">

            @if ($errors->has('name'))
                <p>{{ $errors->first('name') }}</p>
            @endif
        </div>

        <div>
            <label for="password">{{ __('Password') }}</label>
            <input type="password" name="password">

            @if ($errors->has('password'))
                <p>{{ $errors->first('password') }}</p>
            @endif
        </div>

        <div>
            <input type="submit" name="submit" value="{{ __('Log in') }}">
        </div>
    </form>
@endsection