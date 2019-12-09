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
        $orders = Order::with([
            'products' => function ($query) {
                $query->select('price');
            }
        ])->get();

        $result = [
            'orders' => $orders
        ];


        if (request()->ajax()) {
            return $result;
        }
        return view('orders.orders', compact('orders'));
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

        $result = [
            'order' => $order,
            'products' => $order->products()->get()
        ];

        if (request()->ajax()) {
            return $result;
        }

       return view('orders.order', compact('request', 'order'));
    }
}
