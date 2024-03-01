<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        return Product::all();
    }

    public function store(ProductRequest $request) {
        $validated = $request->validated();

        $product = Product::create($validated);

        return response()->json($product, 200);
    }

    public function show($id) {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }

    public function update(ProductRequest $request, $id) {
        $validated = $request->validated();

        $product = Product::findOrFail($id);

        $product->update($validated);

        return response()->json($product, 200);
    }

    public function destroy(Product $product) {
        $product->delete();
        return response()->json(['response' => 'Deleted'], 200);
    }
}
