<?php

namespace App\Http\Controllers;

use App\Mail\CheckoutMail;
use App\Product;
use App\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use Config;

class ShopController extends Controller
{
    /**
     * View index page
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if ($request->id && !in_array($request->id, $cart)) {
            $request->session()->push('cart', $request->id);

            if ($request->ajax()) {
                return ['success' => true];
            }

            return redirect('/');
        }

        $products = Product::query()->whereNotIn('id', $cart)->get();

        if ($request->ajax()) {
            return $products;
        }

        return view('shop.index', compact('products'));
    }

    /**
     * View cart page
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cart(Request $request)
    {
        $cart = $request->session()->pull('cart', []);

        // check for duplicate array keys
        if(($key = array_search($request->id, $cart)) !== false) {
            unset($cart[$key]);
        }

        session()->put('cart', $cart);

        // query database for product id's stored in $cart
        $products = Product::query()->whereIn('id', $cart)->get();

        $result = [
            'products' => $products,
            'price' => $products->sum('price'),
            'cart' => $cart ? true : false
        ];

        if ($request->ajax()) {
            return $result;
        }

        return view('shop.cart', $result);
    }

    /**
     * Mail the admin with the order details
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function mail()
    {
        // validate user input for checkout form
        $data = request()->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'comments' => 'required'
        ]);

        $cart = request()->session()->pull('cart');
        $products = Product::query()->whereIn('id', $cart)->get();
        $price = $products->sum('price');

        // log an order
        $order = new Order();
        $order->name = request()->input('name');
        $order->email = request()->input('email');
        $order->save();

        $order->products()->attach($cart);

        Mail::to('test@test.com')->send(new CheckoutMail($data, $products, $price));

        if (request()->ajax()) {
            return [
               'success' => 'Mail sent'
            ];
        }

        return redirect('cart?success');
    }
}
