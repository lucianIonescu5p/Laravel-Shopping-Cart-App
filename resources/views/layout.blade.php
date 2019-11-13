<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>@yield('title', 'Index')</title>
    </head>
    <body>
        <div>
            <span><a href="{{ '/' }}">{{ __('Home') }}</a></span>
            <span><a href="{{ '/cart' }}">{{ __('Cart') }}</a></span>
            <span>
                <a href="{{ session('auth') ? '/logout' : '/login' }}">
                    {{ session('auth') ? __('Log Out') : __('Log In') }}
                </a>
            </span>
            <span><a href="{{ '/products' }}">{{ __('Products') }}</a></span>
            <span><a href="{{ '/orders' }}">{{ __('Orders') }}</a></span>
        </div>

        @yield('content')
    </body>
</html>