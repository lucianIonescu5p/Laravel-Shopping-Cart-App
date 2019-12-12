<html>
    <head>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

        <!-- Load the jQuery JS library -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <meta name="csrf-token" content="{{ csrf_token() }}"/>

        <!-- Custom JS script -->
        <script type="text/javascript">
            $(document).ready(function () {
                // Create a variable that holds the product ID
                let editId;

                $(document).on('click', '.edit-product', function () {
                    editId = $(this).data('id');

                    window.location.hash = '#product/' + editId + '/edit';
                })

                // Create a variable that holds the order ID
                let orderId;

                $(document).on('click', '.view-order', function () {
                    orderId = $(this).data('id');

                    $('.order .order-view').empty();
                    $('.order .order-list').empty();

                    window.location.hash = '#order/' + orderId;
                });

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
                    let parts = window.location.hash;

                    html = [
                        '<table border="1" cellpadding="3">',
                        '<tr>',
                        '<th align="middle"><center>{{ __('Image') }}</center></th>',
                        '<th align="middle"><center>{{ __('Title') }}</center></th>',
                        '<th align="middle"><center>{{ __('Description') }}</center></th>',
                        '<th align="middle"><center>{{ __('Price') }}</center></th>'].join('')

                    if (parts === '#products') {
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
                            case '#cart':
                                html += '<td align="middle"><button class="remove-from-cart btn btn-danger" data-id="' + product.id + '">{{ __('Remove') }}</button></td>';
                                break;

                            case '#products':
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

                // A function that takes an orders array and renders it's html
                function renderOrders (orders) {
                    let html = [
                        '<table border="1" cellpadding="3">',
                        '<tr>',
                        '<th align="middle"><center>{{ __('Order ID') }}</center></th>',
                        '<th align="middle"><center>{{ __('Name') }}</center></th>',
                        '<th align="middle"><center>{{ __('Price') }}</center></th>',
                        '<th align="middle"><center>{{ __('Action') }}</center></th>'
                    ].join('')

                    $.each(orders, function (key, order) {
                        let price = 0

                        $.each(order.products, function(key, product) {
                            price += product.price;
                        })

                        html += [
                            '<tr>',
                            '<td align="middle">' + order.id + '</td>',
                            '<td align="middle">' + order.name + '</td>',
                            '<td align="middle">' + price + '</td>',
                            '<td align="middle"><button class="view-order btn btn-success" data-id="' + order.id + '">{{ __('View') }}</button></td>',
                            '</tr>',
                        ].join('');
                    })

                    html += '</table>';

                    return html;
                }

                // A function that renders the individual order's products
                function renderOrderView (order) {
                    html = [
                        '<table border="1" cellpadding="3">',
                        '<tr>',
                        '<th align="middle"><center>{{ __('Image') }}</center></th>',
                        '<th align="middle"><center>{{ __('Title') }}</center></th>',
                        '<th align="middle"><center>{{ __('Description') }}</center></th>',
                        '<th align="middle"><center>{{ __('Price') }}</center></th>'].join('')

                    $.each(order, function(key, product) {
                        html += [
                            '<tr>',
                            '<td align="middle"><img src="storage/images/' + product.image + '" width="70px" height="70px"></td>',
                            '<td align="middle">' + product.title + '</td>',
                            '<td align="middle">' + product.description + '</td>',
                            '<td align="middle">' + product.price + '</td>',
                            '</tr>'
                        ].join('');
                    })

                    html += '</table>'

                    return html;
                }

                // Middleware handler
                function redirectUnauthorised (response) {
                    if (response.unauthorised) {
                        window.location.hash = '#login';
                    }
                };

                /**
                 * URL hash change handler
                 */
                window.onhashchange = function () {
                    // First hide all the pages
                    $('.page').hide();

                    let parts = window.location.hash.split('/');

                    switch (parts[0]) {
                        case '#cart':
                            // Load the cart products from the server
                            $.ajax('/cart', {
                                dataType: 'json',
                                success: function (response) {
                                    if (response.cart === false) {
                                        $('.cart').show();
                                        // Hide the checkout form
                                        $('.cart .checkout-form').hide();
                                        // Don't render the products if there is nothing in the cart
                                        $('.cart .list').html('<p class="mt-4">{{ __('Cart is empty') }}</p>')
                                    } else {
                                        $('.cart').show();
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
                            //Log out
                            $.ajax('/logout', {
                                success: function () {
                                    window.location = '/spa';
                                }
                            })
                            break;

                        case '#products':
                            $.ajax('/products', {
                                dataType: 'json',
                                success: function (response) {
                                    // If not logged in, redirect to login page
                                    redirectUnauthorised(response);

                                    $('.products').show();
                                    $('.products .list').html(renderList(response));
                                },
                                error: function (error) {
                                  cosole.log('{{ __('Something bad happened') }}' + '' + error);
                            }
                            })
                            break;

                        case '#product':
                            if (parts.length > 1) {
                                $('.page').hide();

                               if (parts[1] === 'create') {
                                   $('.product-form').each(function () {
                                       this.reset();
                                   })

                                   $.ajax('/products/create', {
                                       dataType: 'json',
                                       success: function (response) {
                                           redirectUnauthorised(response);

                                           $('.products-manipulate').show();
                                           $('.product-update').hide();
                                           $('.product-create').show();
                                       }
                                   })
                               }
                            }

                            if (parts[1] == editId && parts[2] == 'edit') {
                                $('.product-form').each(function () {
                                    this.reset();
                                })

                                $.ajax('/products/' + editId + '/edit', {
                                    dataType: 'json',
                                    success: function (response) {
                                        redirectUnauthorised(response);

                                        $('.products-manipulate').show();
                                        $('.product-update').show();
                                        $('.product-create').hide();

                                        $('#title').val(response.title);
                                        $('#description').val(response.description);
                                        $('#price').val(response.price);
                                        $('#product-id').val(response.id);
                                    }
                                })
                            }
                            break;

                        case '#orders':
                            $.ajax('/orders', {
                                dataType: 'json',
                                success: function (response) {
                                    redirectUnauthorised(response);

                                    $('.orders').show();
                                    $('.orders .orders-list').html(renderOrders(response.orders, response.price))
                                },
                                error: function (error) {
                                    console.log(error);
                                }
                            })
                            break;

                        case '#order':
                            // Show the individual order page
                            $('.order').show();

                            if (parts.length > 1) {
                                if (parts[1] == orderId) {
                                    $.ajax('/order', {
                                        dataType: 'json',
                                        data: {
                                            id: orderId
                                        },
                                        success: function (response) {
                                            redirectUnauthorised(response);

                                            $('.order .order-view').append('<p><strong>{{ __('Order ID') . ': ' }}</strong>' + response.order.id + '</p>')
                                            $('.order .order-view').append('<p><strong>{{ __('Order name') . ': ' }}</strong>' + response.order.name + '</p>')
                                            $('.order .order-view').append('<p><strong>{{ __('Order Email') . ': ' }}</strong>' + response.order.email + '</p>')
                                            $('.order .order-list').html(renderOrderView(response.products))
                                        },
                                        error: function (error) {
                                            console.log(error);
                                        }
                                    })
                                }
                            }
                            break;

                        default:
                            // If all else fails, always default to index
                            // Load the index products from the server
                            $.ajax('/', {
                                dataType: 'json',
                                success: function (response) {
                                    if (!response.length) {
                                        $('.index').show();
                                        // Don't render the products table if all products in cart
                                        $('.index .list').html('<p class="mt-4">{{ __('All products in cart') }}</p>')
                                    } else {
                                        $('.index').show();
                                        // Render the products in the index list
                                        $('.index .list').html(renderList(response));
                                    }
                                }
                            });
                            break;
                    }
                }

                window.onhashchange();

                // Validating CSRF Token
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
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

                // Delete product from database functionality
                $(document).on('click', '.delete-from-database', function () {
                    let id = $(this).data('id');

                    $.ajax('/products/' + id, {
                        type: 'DELETE',
                        data: {
                            id
                        },
                        dataType: 'json',
                        success: function () {
                            window.onhashchange();
                        },
                        error: function (error) {
                            console.log(error);
                        }
                    });
                })

                // Checkout form
                $(document).on('click', '.submit', function (e) {
                    e.preventDefault();

                    let data = new FormData();
                    data.append('name', $('input[id=name]').val());
                    data.append('email', $('input[id=email]').val());
                    data.append('comments', $('textarea[id=comments]').val());

                    $('.submit').prop('disabled', true);
                    $('.submit').html('{{ __('Please wait') . '...' }}')

                    $.ajax('/cart', {
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
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

                            let errorMessage = JSON.parse(error.responseText)

                            if (errorMessage.errors.hasOwnProperty('name')) {
                                let nameError = $('.name-error');
                                nameError.css('display', 'block');
                                nameError.html(errorMessage.errors['name']);

                                $('#name').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('email')) {
                                let emailError = $('.email-error');
                                emailError.css('display', 'block');
                                emailError.html(errorMessage.errors['email']);

                                $('#email').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('comments')) {
                                let commentsError = $('.comments-error');
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

                    let data = new FormData();
                    data.append('username', $('input[id=username]').val());
                    data.append('password', $('input[id=password]').val());

                    $.ajax('/login', {
                        type: 'POST',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function () {
                            $('#login-btn').attr('href', '#logout');
                            $('#login-btn').attr('id', 'logout-btn');
                            $('#logout-btn').html(`<strong>{{ __('Log Out') }}</strong>`);

                            window.location.hash = '#products';
                        },
                        error: function (error) {
                            let errorMessage = JSON.parse(error.responseText)

                            if (errorMessage.errors.hasOwnProperty('username')) {
                                let usernameError = $('.username-error');
                                usernameError.css('display', 'block');
                                usernameError.html(errorMessage.errors['username']);

                                $('#username').addClass('is-invalid')
                            }

                            if (errorMessage.errors.hasOwnProperty('password')) {
                                let passwordError = $('.password-error');
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

                // Product create functionality
                $(document).on('click', '.product-create', function (e) {
                    e.preventDefault();

                    let data = new FormData();
                    data.append('title', $('input[id=title]').val());
                    data.append('description', $('input[id=description]').val());
                    data.append('price', $('input[id=price]').val());

                    if ($('#image').get(0).files.length !== 0) {
                        data.append('image', $('#image')[0].files[0]);
                    }

                    $('.product-create').prop('disabled', true);
                    $('.product-create').html('{{ __('Please wait') . '...' }}')

                    $.ajax('/products', {
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function () {
                            $('.product-create').prop('disabled', false);
                            $('.product-create').html('{{ __('Create') }}');

                            $('.product-form').each(function () {
                                this.reset();
                            })

                            window.location.hash='#products';
                        },
                        error: function (error) {
                            $('.product-create').prop('disabled', false);
                            $('.product-create').html('{{ __('Create') }}');

                            let errorMessage = JSON.parse(error.responseText)

                            if (errorMessage.errors.hasOwnProperty('title')) {
                                let titleError = $('.title-error');
                                titleError.css('display', 'block');
                                titleError.html(errorMessage.errors['title']);

                                $('#title').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('description')) {
                                let descriptionError = $('.description-error');
                                descriptionError.css('display', 'block');
                                descriptionError.html(errorMessage.errors['description']);

                                $('#description').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('price')) {
                                let priceError = $('.price-error');
                                priceError.css('display', 'block');
                                priceError.html(errorMessage.errors['price']);

                                $('#price').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('image')) {
                                let imageError = $('.image-error');
                                imageError.css('display', 'block');
                                imageError.html(errorMessage.errors['image']);

                                $('#image').addClass('is-invalid');
                            }
                        }
                    })

                    $('input[id=title]').keypress(function () {
                        $('.title-error').hide();
                        $('#title').removeClass('is-invalid');
                    });

                    $('input[id=description]').keypress(function () {
                        $('.description-error').hide();
                        $('#description').removeClass('is-invalid');
                    });

                    $('input[id=price]').keypress(function () {
                        $('.price-error').hide();
                        $('#price').removeClass('is-invalid');
                    });
                });

                // Product update functionality
                $(document).on('click', '.product-update', function (e) {
                    e.preventDefault();

                    let data = new FormData();
                    data.append('_method', 'PUT');
                    data.append('title', $('input[id=title]').val());
                    data.append('description', $('input[id=description]').val());
                    data.append('price', $('input[id=price]').val());

                    if ($('#image').get(0).files.length !== 0) {
                        data.append('image', $('#image')[0].files[0]);
                    }

                    $('.product-update').prop('disabled', true);
                    $('.product-update').html('{{ __('Please wait') . '...' }}')

                    $.ajax('/products/' + parseInt($('input[id=product-id]').val()), {
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function () {
                            $('.product-update').prop('disabled', false);
                            $('.product-update').html('{{ __('Update') }}');

                            $('.product-form').each(function () {
                                this.reset();
                            })

                            window.location.hash='#products';
                        },
                        error: function (error) {
                            $('.product-update').prop('disabled', false);
                            $('.product-update').html('{{ __('Update') }}');

                            let errorMessage = JSON.parse(error.responseText)

                            if (errorMessage.errors.hasOwnProperty('title')) {
                                let titleError = $('.title-error');
                                titleError.css('display', 'block');
                                titleError.html(errorMessage.errors['title']);

                                $('#title').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('description')) {
                                let descriptionError = $('.description-error');
                                descriptionError.css('display', 'block');
                                descriptionError.html(errorMessage.errors['description']);

                                $('#description').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('price')) {
                                let priceError = $('.price-error');
                                priceError.css('display', 'block');
                                priceError.html(errorMessage.errors['price']);

                                $('#price').addClass('is-invalid');
                            }

                            if (errorMessage.errors.hasOwnProperty('image')) {
                                let imageError = $('.image-error');
                                imageError.css('display', 'block');
                                imageError.html(errorMessage.errors['image']);

                                $('#image').addClass('is-invalid');
                            }
                        }
                    })

                    $('input[id=title]').keypress(function () {
                        $('.title-error').hide();
                        $('#title').removeClass('is-invalid');
                    });

                    $('input[id=description]').keypress(function () {
                        $('.description-error').hide();
                        $('#description').removeClass('is-invalid');
                    });

                    $('input[id=price]').keypress(function () {
                        $('.price-error').hide();
                        $('#price').removeClass('is-invalid');
                    });
                });
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

                    <p class="error name-error text-danger" style="display: none"></p>
                </div>

                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input type="text" class="form-control" id="email" placeholder="{{ __('Enter Email') }}">

                    <p class="error email-error text-danger" style="display: none"></p>
                </div>

                <div class="form-group">
                    <label for="comments">{{ __('Comments') }}</label>
                    <textarea class="form-control" id="comments" rows="3"></textarea>

                    <p class="error comments-error text-danger" style="display: none"></p>
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

            <a href="#product/create" class="btn btn-primary">{{ __('Add new product') }}</a>
        </div>

        <!-- The product create/edit page -->
        <div class="page products-manipulate container">
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

                    <input type="hidden" id="product-id" value="">

                    <button type="button" class="product-create btn btn-primary">{{ __('Create') }}</button>
                    <button type="button" class="product-update btn btn-warning">{{ __('Update') }}</button>
                </form>
            </div>
        </div>

        <!-- The Orders page -->
        <div class="page orders container">
            <!-- The order element where the products list is rendered -->
            <div class="orders-list"></div>
        </div>

        <!-- The Orders page -->
        <div class="page order container">
            <div class="order-view"></div>

            <!-- The order element where the products list is rendered -->
            <div class="order-list"></div>
        </div>
    </body>
</html>