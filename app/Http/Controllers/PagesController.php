<?php

namespace App\Http\Controllers;

use App\Mail\CheckoutMail;
use App\Product;
use App\Order;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Config;

class PagesController extends Controller
{
    /**
     * View index page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
        $price = 0;

        foreach ($products as $product) {
            $price += $product->price;
        }

        return view('cart', [
            'products' => $products,
            'price' => $price,
            'cart' => $cart
        ]);
    }

    /**
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
        $price = 0;

        foreach ($products as $product) {
            $price += $product->price;
        }

        $order = new Order();

        $order->name = request()->input('name');
        $order->email = request()->input('email');
        $order->price = $price;

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
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function auth()
    {
        $errorMessage = [];

        if (request()->input('name') !== config('admin.admin_name')) {
           $errorMessage['name'][] = __('Wrong username');
        }

        if (request()->input('password') !== config('admin.admin_pass')) {
            $errorMessage['password'][] = __('Wrong password');
        }

        if (!$errorMessage) {
            session(['auth' => true]);

            return redirect('products');
        } else {
            return view('login', compact('errorMessage'));
        }
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

    /**
     * View product page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function product()
    {
        return view('product');
    }

    /**
     * View orders page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orders()
    {
        return view('orders');
    }
}
