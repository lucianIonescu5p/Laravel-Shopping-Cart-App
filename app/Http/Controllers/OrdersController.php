<?php

namespace App\Http\Controllers;

use App\Order;

class OrdersController extends Controller
{
    /**
     * View orders page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orders()
    {
        $orders = Order::all();
        return view('orders', compact('orders'));
    }

    /**
     * View the individual order
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order()
    {
        $request = request('id');
        $order = Order::findOrFail($request);

       return view('order', compact('request', 'order'));
    }
}
