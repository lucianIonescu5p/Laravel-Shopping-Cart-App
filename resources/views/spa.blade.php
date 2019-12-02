<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <!-- Load the jQuery JS library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}"/>

        <!-- Custom JS script -->
        <script type="text/javascript">
            $(document).ready(function () {

                /**
                 * A function that takes a products array and renders it's html
                 *
                 * The products array must be in the form of
                 * [{
                 *     "title": "Product 1 title",
                 *     "description": "Product 1 desc",
                 *     "price": 1
                 * },{
                 *     "title": "Product 2 title",
                 *     "description": "Product 2 desc",
                 *     "price": 2
                 * }]
                 */
                function renderList(products, price) {
                    var parts = window.location.hash;

                    html = [
                        '<table border="1" cellpadding="3">',
                        '<tr>',
                        '<th align="middle"><center>{{ __('Image') }}</center></th>',
                        '<th align="middle"><center>{{ __('Title') }}</center></th>',
                        '<th align="middle"><center>{{ __('Description') }}</center></th>',
                        '<th align="middle"><center>{{ __('Price') }}</center></th>'].join('')

                    if (parts[0] = '#products') {
                        html += [
                            '<th colspan="3" align="middle"><center>{{ __('Action') }}</center></th>',
                            '</tr>'
                        ].join('');
                    } else {
                        html += [
                            '<th align="middle"><center>{{ __('Action') }}</center></th>',
                            '</tr>'
                        ].join('');
                    }

                    $.each(products, function (key, product) {
                        html += '<tr>';

                        if (!product.image) {
                            html += '<td align="middle">{{ __('No image available') }}</td>'
                        } else {
                            html += '<td align="middle"><img src="storage/images/' + product.image + '" width="70px" height="70px"></td>';
                        }

                        html += [
                            '<td align="middle">' + product.title + '</td>',
                            '<td align="middle">' + product.description + '</td>',
                            '<td align="middle">' + product.price + '</td>'].join('');

                        switch (parts) {
                            case('#cart'):
                                html += '<td align="middle"><button class="remove-from-cart btn btn-danger" data-id="' + product.id + '">{{ __('Remove') }}</button></td>';
                                break;

                            case('#products'):
                                html += '<td align="middle"><button class="edit-product btn btn-warning" data-id="' + product.id + '">{{ __('Edit') }}</button></td>';
                                html += '<td align="middle"><button class="delete-from-database btn btn-danger" data-id="' + product.id + '">{{ __('Delete') }}</button></td>';
                                break;

                            default:
                                html += '<td align="middle"><button class="add-to-cart btn btn-primary" data-id="' + product.id + '">{{ __('Add') }}</button></td>';
                                break;
                        }

                        html += '</tr>';
                    });

                    if (parts === '#cart' && price) {
                        html += [
                            '<tr>',
                            '<td colspan="3" align="middle"><strong>{{ __('Total Price') }}</strong></td>',
                            '<td colspan="2" align="middle">' + price + '</td>',
                            '</tr>',
                            '</table>'
                        ].join('');
                    }

                    return html;
                };

                // Middleware handler
                function redirectUnauthorised (response) {
                    if (response.unauthorised) {
                        window.location = '/spa#login';
                    }
                };
                /**
                 * URL hash change handler
                 */
                window.onhashchange = function () {
                    // First hide all the pages
                    $('.page').hide();

                    var parts = window.location.hash.split('/');

                    switch (parts[0]) {
                        case '#cart':
                            // Show the cart page
                            $('.cart').show();
                            // Load the cart products from the server
                            $.ajax('/cart', {
                                dataType: 'json',
                                success: function (response) {
                                    if (response.cart === false) {
                                        // Hide the checkout form
                                        $('.cart .checkout-form').hide();
                                        // Don't render the products if there is nothing in the cart
                                        $('.cart .list').html('<p>{{ __('Cart is empty') }}</p>')
                                    } else {
                                        // Render the checkout form
                                        $('.cart .checkout-form').css('display', 'block');
                                        // Render the products in the cart list
                                        $('.cart .list').html(renderList(response.products, response.price));
                                    }
                                }
                            });
                            break;

                        case '#login':
                            //Show the login page
                            $('.login').show();
                            break;

                        case '#logout':
                            //Log off and set auth to false
                            $.ajax('/logout', {
                                success: function () {
                                    window.location = '/spa';
                                }
                            })
                            break;

                        case '#products':
                            // Show the products page
                            $('.products').show();

                            if (parts.length > 1) {
                                switch (parts[1]) {
                                    case 'create':
                                        $.ajax('/products/create', {
                                            dataType: 'json',
                                            success: function (response) {
                                                redirectUnauthorised(response);

                                                $('.products').hide();
                                                $('.products-create').show();
                                            }
                                        })
                                        break;
                                }
                            } else {
                                $.ajax('/products', {
                                    dataType: 'json',
                                    success: function (response) {
                                        // If not logged in, redirect to login page
                                        redirectUnauthorised(response);

                                        $('.products .list').html(renderList(response));
                                    }
                                })
                            }
                            break;

                        default:
                            // If all else fails, always default to index
                            // Show the index page
                            $('.index').show();
                            // Load the index products from the server
                            $.ajax('/', {
                                dataType: 'json',
                                success: function (response) {
                                    if (!response.length) {
                                        // Don't render the products table if all products in cart
                                        $('.index .list').html('<p>{{ __('All products in cart') }}</p>')
                                    } else {
                                        // Render the products in the index list
                                        $('.index .list').html(renderList(response));
                                    }
                                }
                            });
                            break;
                    }
                }

                window.onhashchange();
            });

            // add products to cart
            $(document).on('click', '.add-to-cart', function () {
                $.ajax('/', {
                    data: {
                        id: $(this).data('id')
                    },
                    dataType: 'json',
                    success: function () {
                        window.onhashchange();
                    }
                });
            });

            // remove products from cart
            $(document).on('click', '.remove-from-cart', function () {
                $.ajax('/cart', {
                    data: {
                        id: $(this).data('id')
                    },
                    dataType: 'json',
                    success: function () {
                        window.onhashchange();
                    }
                });
            });

            // Validating the CSRF Token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Checkout form
            $(document).on('click', '.submit', function (e) {
                e.preventDefault();

                let name = $('input[id=name]').val();
                let email = $('input[id=email]').val();
                let comments = $('textarea[id=comments]').val();

                $('.submit').prop('disabled', true);
                $('.submit').html('{{ __('Please wait') . '...' }}')

                $.ajax('/cart', {
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        name,
                        email,
                        comments
                    },
                    success: function (result) {
                        alert(result.success);

                        $('.checkout-form').each(function () {
                            this.reset();
                        })

                        $('.submit').prop('disabled', false);
                        $('.submit').html('{{ __('Checkout') }}');

                        window.onhashchange();
                    },
                    error: function (error) {
                        $('.submit').prop('disabled', false);
                        $('.submit').html('{{ __('Checkout') }}');

                        var errorMessage = JSON.parse(error.responseText)

                        if (errorMessage.errors.hasOwnProperty('name')) {
                            var nameError = $('.name-error');
                            nameError.css('display', 'block');
                            nameError.html(errorMessage.errors['name']);

                            $('#name').addClass('is-invalid');
                        }

                        if (errorMessage.errors.hasOwnProperty('email')) {
                            var emailError = $('.email-error');
                            emailError.css('display', 'block');
                            emailError.html(errorMessage.errors['email']);

                            $('#email').addClass('is-invalid');
                        }

                        if (errorMessage.errors.hasOwnProperty('comments')) {
                            var commentsError = $('.comments-error');
                            commentsError.css('display', 'block');
                            commentsError.html(errorMessage.errors['comments']);

                            $('#comments').addClass('is-invalid');
                        }
                    }
                })

                $('input[id=name]').keypress(function () {
                    $('.name-error').hide();
                    $('#name').removeClass('is-invalid');

                });

                $('input[id=email]').keypress(function () {
                    $('.email-error').hide();
                    $('#email').removeClass('is-invalid');
                });

                $('textarea[id=comments]').keypress(function () {
                    $('.comments-error').hide();
                    $('#comments').removeClass('is-invalid');
                });
            });

            // Handle admin login functionality
            $(document).on('click', '.login-submit', function (e) {
                e.preventDefault();

                let username = $('input[id=username]').val();
                let password = $('input[id=password]').val();

                $.ajax('/login', {
                    type: 'POST',
                    data: {
                        username,
                        password
                    },
                    success: function () {
                        $('#login-btn').attr('href', '#logout');
                        $('#login-btn').attr('id', 'logout-btn');
                        $('#logout-btn').html(`<strong>{{ __('Log Out') }}</strong>`);

                        $('#admin-prompt').css('display', 'block');

                        window.location.hash = '#products';
                    },
                    error: function (error) {
                        var errorMessage = JSON.parse(error.responseText)

                        if (errorMessage.errors.hasOwnProperty('username')) {
                            var usernameError = $('.username-error');
                            usernameError.css('display', 'block');
                            usernameError.html(errorMessage.errors['username']);

                            $('#username').addClass('is-invalid')
                        }

                        if (errorMessage.errors.hasOwnProperty('password')) {
                            var passwordError = $('.password-error');
                            passwordError.css('display', 'block');
                            passwordError.html(errorMessage.errors['password']);

                            $('#password').addClass('is-invalid')
                        }
                    }
                })

                $('input[id=username]').keypress(function () {
                    $('.username-error').hide();
                    $('#username').removeClass('is-invalid');
                });

                $('input[id=password]').keypress(function () {
                    $('.password-error').hide();
                    $('#password').removeClass('is-invalid');
                });
            });

            // Product manipulation functionality

            $(document).on('click', '.product-submit', function (e) {
                e.preventDefault();

                let title = $('input[id=title]').val();
                let description = $('input[id=description]').val();
                let price = $('input[id=price]').val();
                let image = $('#image')[0].files[0].name;

                $.ajax('/products', {
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        title,
                        description,
                        price,
                        image
                    },
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (error) {

                    }
                })

            });

        </script>
    </head>
    <body>
        {{--Navigation bar--}}
        <nav class="navbar navbar-light mb-2" style="background-color: #e3f2fd;">
            <!-- Navbar content -->
            <span class="float-right" style="color: white;">
                <a class="list-group-item-action p-1 mr-2" href="#"><strong>{{ __('Home') }}</strong></a>

                @if (request()->session()->has('auth') && session('auth'))
                    <a class="list-group-item-action p-1" id="logout-btn" href="#logout"><strong>{{ __('Log Out') }}</strong></a>
                @else
                    <a class="list-group-item-action p-1" id="login-btn" href="#login"><strong>{{ __('Log In') }}</strong></a>
                @endif

                <a class="list-group-item-action p-1" href="#products"><strong>{{ __('Products') }}</strong></a>
                <a class="list-group-item-action p-1" href="#orders"><strong>{{ __('Orders') }}</strong></a>
            </span>

            @if (request()->session()->has('auth') && !session('auth'))
            <span class="float-left" id="admin-prompt" style="display: none">
                <strong>{{ __('Hi there') }}</strong>
            </span>
            @endif
        </nav>

        <!-- The index page -->
        <div class="page index container">
            <!-- The index element where the products list is rendered -->
            <div class="list"></div>

            <!-- A link to go to the cart by changing the hash -->
            <a href="#cart" class="button">{{ __('Go to cart') }}</a>
        </div>

        <!-- The cart page -->
        <div class="page cart container">
            <!-- The cart element where the products list is rendered -->
            <div class="list" style="margin-bottom: 10px;"></div>

            {{-- Checkout form --}}
            <form class="checkout-form">
                <div class="form-group">
                    <label for="name">{{ __('Name') }}</label>
                    <input type="text" class="form-control" id="name" placeholder="{{ __('Enter Name') }}">

                    <p class="name-error text-danger" style="display: none"></p>
                </div>

                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input type="text" class="form-control" id="email" placeholder="{{ __('Enter Email') }}">

                    <p class="email-error text-danger" style="display: none"></p>
                </div>

                <div class="form-group">
                    <label for="comments">{{ __('Comments') }}</label>
                    <textarea class="form-control" id="comments" rows="3"></textarea>

                    <p class="comments-error text-danger" style="display: none"></p>
                </div>

                <button type="submit" class="submit btn btn-primary">{{ __('Checkout') }}</button>
            </form>
            <!-- A link to go to the index by changing the hash -->
            <a href="#" class="button">{{ __('Go to index') }}</a>
        </div>

        <!-- The login page -->
        <div class="page login container">
            <!-- The login element where the products list is rendered -->
            <div class="container">
                <form class="login-form">
                    <div class="form-group">
                        <label for="name">{{ __('Username') }}</label>
                        <input type="text" class="form-control" id="username" placeholder="{{ __('Username') }}">

                        <p class="username-error text-danger" style="display: none"></p>
                    </div>

                    <div class="form-group">
                        <label for="email">{{ __('Password') }}</label>
                        <input type="password" class="form-control" id="password" placeholder="{{ __('Password') }}">

                        <p class="password-error text-danger" style="display: none"></p>
                    </div>

                    <button type="submit" class="login-submit btn btn-primary">{{ __('Login') }}</button>
                </form>
            </div>
        </div>

        <!-- The products page -->
        <div class="page products container">
            <div class="list mb-1"></div>

            <a href="#products/create" class="btn btn-primary">{{ __('Add new product') }}</a>
        </div>

        <!-- The product create/edit page -->
        <div class="page products-create container">
            <div class="container">
                <form class="product-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="title">{{ __('Product Title') }}</label>
                        <input type="text" class="form-control" id="title" placeholder="{{ __('Enter product title') }}">

                        <p class="title-error text-danger" style="display: none"></p>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ __('Product description') }}</label>
                        <input type="text" class="form-control" id="description" placeholder="{{ __('Enter product description') }}">

                        <p class="description-error text-danger" style="display: none"></p>
                    </div>

                    <div class="form-group">
                        <label for="price">{{ __('Product price') }}</label>
                        <input type="text" class="form-control" id="price" placeholder="{{ __('Enter product price') }}">

                        <p class="price-error text-danger" style="display: none"></p>
                    </div>

                    <div>
                        <label for="image">{{ __('Product image') }}</label>
                        <input type="file" id="image">

                        <p class="image-error text-danger" style="display: none"></p>
                    </div>

                    <button type="submit" class="product-submit btn btn-primary">{{ __('Submit') }}</button>
                </form>
            </div>
        </div>
    </body>
</html>