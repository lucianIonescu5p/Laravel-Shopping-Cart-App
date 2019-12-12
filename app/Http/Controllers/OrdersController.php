<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    /**
     * View orders page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orders()
    {
        $orders = Order::select("orders.*", DB::raw("SUM(products.price) AS price"))
            ->join('order_product', 'order_product.order_id', '=', 'orders.id')
            ->join('products', 'products.id', '=', 'order_product.product_id')
            ->groupBy('orders.id')
            ->get()
            ;

        if (request()->ajax()) {
            return $orders;
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
