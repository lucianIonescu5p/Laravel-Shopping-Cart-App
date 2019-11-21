<?php

namespace App\Http\Controllers;

use App\Mail\CheckoutMail;
use App\Product;
use App\Order;
use App\Rules\AdminName;
use App\Rules\AdminPassword;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use Config;

class PagesController extends Controller
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

            return redirect('/');
        }

        return view('index', ['products' => Product::query()->whereNotIn('id', $cart)->get()]);
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

        $products = Product::query()->whereIn('id', $cart)->get();
        $price = Product::query()->whereIn('id', $cart)->sum('price');

        return view('cart', [
            'products' => $products,
            'cart' => $cart,
            'price' => $price
        ]);
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
        $price = Product::query()->whereIn('id', $cart)->sum('price');

        // log an order
        $order = new Order();

        $order->name = request()->input('name');
        $order->email = request()->input('email');

        $order->save();

        $order->products()->attach($cart);

        Mail::to('test@test.com')->send(new CheckoutMail($data, $products, $price));

        return redirect('cart?success');
    }

    /**
     * View login page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Log in
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function auth(Request $request)
    {
        $request->validate([
            'name' => ['required', new AdminName],
            'password' => ['required', new AdminPassword]
        ]);

        session(['auth' => true]);

        return redirect('products');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout()
    {
        session()->pull('auth');
        session()->put(['auth' => false]);

        return redirect('/');
    }
}
