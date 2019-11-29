<html>
    <head>
        <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">

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
                    html = [
                        '<table border="1" cellpadding="3">',
                        '<tr>',
                        '<th align="middle">{{ __('Image') }}</th>',
                        '<th align="middle">{{ __('Title') }}</th>',
                        '<th align="middle">{{ __('Description') }}</th>',
                        '<th align="middle">{{ __('Price') }}</th>',
                        '<th align="middle">{{ __('Action') }}</th>',
                        '</tr>'
                    ].join('');

                    var parts = window.location.hash;

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
                                html += '<td align="middle"><button class="remove-from-cart btn-danger" data-id="' + product.id + '">{{ __('Remove') }}</button></td>';
                                break;

                            default:
                                html += '<td align="middle"><button class="add-to-cart btn-primary" data-id="' + product.id + '">{{ __('Add') }}</button></td>';
                                break;
                        }

                        html += '</tr>';
                    });

                    if (parts === '#cart' && price !== 0) {
                        html += [
                            '<tr>',
                            '<td colspan="3" align="middle"><strong>{{ __('Total Price') }}</strong></td>',
                            '<td colspan="2" align="middle">' + price + '</td>',
                            '</tr>',
                            '</table>'
                        ].join('');
                    }

                    return html;
                }

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
                                        $('.cart .checkout-form').css('display', 'none');
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

                        case '#product':
                            var id = 0;
                            if (parts.length > 1) {
                                id = parseInt(parts[1]);
                            }
                            console.log(id);
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

                $.ajax('/cart', {
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        name,
                        email,
                        comments
                    },
                    success: function (result) {
                        $('.submit').prop('disabled', false);

                        alert(result.success);

                        $('.checkout-form').each(function () {
                            this.reset();
                        })

                        window.onhashchange();
                    },
                    error: function (error) {
                        var errorMessage = JSON.parse(error.responseText)

                        if (errorMessage.errors.hasOwnProperty('name')) {
                            var nameError = $('.name-error');

                            nameError.css('display', 'block');
                            nameError.html(errorMessage.errors['name']);
                        }

                        if (errorMessage.errors.hasOwnProperty('email')) {
                            var emailError = $('.email-error');

                            emailError.css('display', 'block');
                            emailError.html(errorMessage.errors['email']);
                        }

                        if (errorMessage.errors.hasOwnProperty('comments')) {
                            var commentsError = $('.comments-error');

                            commentsError.css('display', 'block');
                            commentsError.html(errorMessage.errors['comments']);
                        }
                    }
                })

                $('input[id=name]').keypress(function () {
                    $('.name-error').css('display', 'none');
                });

                $('input[id=email]').keypress(function () {
                    $('.email-error').css('display', 'none');
                });

                $('textarea[id=comments]').keypress(function () {
                    $('.comments-error').css('display', 'none');
                });
            });
        </script>
    </head>
    <body>
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
    </body>
</html>