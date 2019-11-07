<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * View index page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('index');
    }

    /**
     * View cart page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cart()
    {
        return view('cart');
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
