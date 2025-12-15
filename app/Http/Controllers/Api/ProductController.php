<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        return ProductResource::collection($products)->additional([
            'message' => $products->isEmpty() ? 'No products found' : 'Products retrieved',
            'status' => 'success',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:1',
        ]);


        $product = Product::create($validated);

        return new ProductResource($product)->additional([
            'message' => 'Product created successfully',
            'status' => 'success',
        ])->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product)->additional([
            'message' => 'Product fetched successfully',
            'status' => 'success',
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:1',
        ]);

        $product->update($validated);


        return new ProductResource($product)->additional([
            'message' => 'Product updated successfully',
            'status' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'data' => null,
            'message' => 'Product deleted successfully',
            'status' => 'success',
        ], 204);
    }

    public function lowStock()
    {
        // Use the query scope defined in the model
        $products = Product::lowStock()->get();

        return ProductResource::collection($products)->additional([
            'message' => 'Low stock products retrieved',
            'status' => 'success',
        ]);
    }
}
