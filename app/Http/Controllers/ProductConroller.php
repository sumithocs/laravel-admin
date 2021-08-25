<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreateRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Gate;
use Illuminate\Http\Request;
use Storage;
use Str;
use Symfony\Component\HttpFoundation\Response;

class ProductConroller extends Controller
{
    public function index()
    {
        Gate::authorize('view', 'products');
        $products = Product::paginate();
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        Gate::authorize('view', 'products');
        $product = Product::find($id);
        return new ProductResource($product);
    }

    public function store(ProductCreateRequest $request)
    {
        Gate::authorize('edit', 'products');
        // $file = $request->file('image');
        // $name = Str::random(10);
        // $url = Storage::putFileAs('images', $file, $name . time() . '.' . $file->extension());
        // $product = Product::create([
        //     'title' => $request->input('title'),
        //     'description' => $request->input('description'),
        //     'price' => $request->input('price'),
        //     'image' => env('APP_URL') . '/' . $url
        // ]);
        $product = Product::create($request->only('title', 'description', 'image', 'price'));
        return response($product, Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        Gate::authorize('edit', 'products');
        $product = Product::find($id);
        $product->update($request->only('title', 'description', 'image', 'price'));
        return response($product, Response::HTTP_ACCEPTED);
    }

    public function destroy($id)
    {
        Gate::authorize('edit', 'products');
        Product::destroy($id);
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
