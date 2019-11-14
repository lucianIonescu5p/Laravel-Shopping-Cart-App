@extends('layout')

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
            <input type="text" name="name">

            @if (isset($errorMessage['name']))
                @foreach ($errorMessage['name'] as $error)
                    <p>{!!   $error !!}</p>
                @endforeach
            @endif
        </div>

        <div>
            <label for="password">{{ __('Password') }}</label>
            <input type="password" name="password">

            @if (isset($errorMessage['password']))
                @foreach ($errorMessage['password'] as $error)
                    <p>{!!   $error !!}</p>
                @endforeach
            @endif
        </div>

        <div>
            <input type="submit" name="submit" value="{{ __('Log in') }}">
        </div>
    </form>
@endsection