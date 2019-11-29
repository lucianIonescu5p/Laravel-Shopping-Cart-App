<html>
<head>
    <!-- Load the jQuery JS library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

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
                            html += '<td align="middle"><button class="remove-from-cart" data-id="' + product.id + '">{{ __('Remove') }}</button></td>';
                            break;

                        default:
                            html += '<td align="middle"><button class="add-to-cart" data-id="' + product.id + '">{{ __('Add') }}</button></td>';
                            break;
                    }
                    ;

                    html += '</tr>';
                });

                if (parts === '#cart' && price !== 0) {
                    html += [
                        '<tr>',
                        '<td colspan="3"><strong>{{ __('Total Price') }}</strong></td>',
                        '<td colspan="2">' + price + '</td>',
                        '</tr>',
                        '</table>'
                    ].join('');
                }

                return html;
            }

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

            /**
             * URL hash change handler
             */
            window.onhashchange = function () {
                // First hide all the pages
                $('.page').hide();

                var parts = window.location.hash.split('/');
                console.log(parts);
                switch (parts[0]) {
                    case '#cart':
                        // Show the cart page
                        $('.cart').show();
                        // Load the cart products from the server
                        $.ajax('/cart', {
                            dataType: 'json',
                            success: function (response) {
                                if (response.cart === false) {
                                    // Don't render the products if there is nothing in the cartss
                                    $('.cart .list').html('<p>{{ __('Cart is empty') }}</p>')
                                } else {
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
    </script>
</head>
<body>
<!-- The index page -->
<div class="page index">
    <!-- The index element where the products list is rendered -->
    <div class="list"></div>

    <!-- A link to go to the cart by changing the hash -->
    <a href="#cart" class="button">{{ __('Go to cart') }}</a>
</div>

<!-- The cart page -->
<div class="page cart">
    <!-- The cart element where the products list is rendered -->
    <div class="list"></div>

    <!-- A link to go to the index by changing the hash -->
    <a href="#" class="button">{{ __('Go to index') }}</a>
</div>
</body>
</html>