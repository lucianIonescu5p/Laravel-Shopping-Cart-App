<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();

        if (request()->ajax()) {
            return $products;
        }

        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (request()->ajax()) {
            return response()->json([
               'product' => true
            ]);
        }
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Product $product, Request $request)
    {
        $this->validateRequest();

        $fileNameToStore = $this->imageToUpload();

        $product->title = $request->input('title');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->image = $fileNameToStore;
        $product->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => 'Product created'
            ]);
        }

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
        if (request()->ajax()) {
            return $product;
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function show()
    {
        return redirect('/products');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Product $product
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Product $product, Request $request)
    {
        $this->validateRequest();
        $fileNameToStore = $this->imageToUpload();

        $product->title = $request->input('title');
        $product->description = $request->input('description');
        $product->price = $request->input('price');

        if (request()->hasFile('image')) {
            if ($product->image && $product->image !== $fileNameToStore) {
                unlink('storage/images/' . $product->image);
            }

            $product->image = $fileNameToStore;
        }

        $product->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => 'Product updated'
            ]);
        }

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
            unlink('storage/images/' . $product->image);
        }

        $product->delete();

        if (request()->ajax()) {
            return response()->json([
                'product' => 'destroyed'
            ]);
        }

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
            'image' => 'image|mimes:jpg,jpeg,png,bmp,tiff|max:1999'
        ]);
    }

    /**
     * Store the image
     *
     * @param Request $request
     * @return string|null
     */
    public function imageToUpload()
    {
        if (request()->hasFile('image')) {
            $filenameWithExt = request()->file('image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = request()->file('image')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;

            request()->file('image')->storeAs('public/images', $fileNameToStore);
        } else {
            $fileNameToStore = null;
        }

        return $fileNameToStore;
    }
}
