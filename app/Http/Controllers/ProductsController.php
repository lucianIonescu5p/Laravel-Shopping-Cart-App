<?php

namespace App\Http\Controllers;

use App\Product;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products.index', ['products' => Product::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Product $product)
    {
        $product->create($this->validateRequest());

        $this->storeImage($product);

        return redirect('/products');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return redirect('/products');
    }

    /**
     * Edit record
     *
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Product $product)
    {
        $product->update($this->validateRequest());

        $this->storeImage($product);

        return redirect('/products');
    }

    /**
     * Delete record
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            unlink('storage/' . $product->image);
        }

        $product->delete();

        return redirect('/products');
    }

    /**
     * Validate data
     *
     * @return mixed
     */
    public function validateRequest()
    {
        return request()->validate([
            'title' => 'required|min:5',
            'description' => 'required|min:5',
            'price' => 'required|numeric',
            'image' => 'sometimes|file|image|max:5000'
        ]);
    }

    /**
     * Store image
     *
     * @param $product
     */
    public function storeImage(Product $product)
    {
        if (request()->has('image')) {
            $product->update([
               'image' => request()->image->store('images', 'public')
            ]);
        }
    }
}
