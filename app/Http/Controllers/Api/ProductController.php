<?php

// app/Http/Controllers/Api/ProductController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand'])->get();
        return response()->json($products, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'status' => 'boolean',
        ]);

        $product = Product::create($validated);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand']);
        return response()->json($product, Response::HTTP_OK);
    }

    public function update(Request $request, Product $product)
    {

        Log::info($request);

        $validated = $request->validate([
            'name' => 'string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'category_id' => 'exists:categories,id',
            'brand_id' => 'exists:brands,id',
            'status' => 'boolean',
        ]);

        $product->update($validated);

        return response()->json($product, Response::HTTP_OK);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
