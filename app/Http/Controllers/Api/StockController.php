<?php

// app/Http/Controllers/Api/StockController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    public function index()
    {
        $stocks = Stock::with(['product', 'supplier'])->get();
        return response()->json($stocks, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'container_id' => 'required|exists:containers,id',
            'quantity' => 'required|integer',
            'buy_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'comission' => 'nullable|numeric',
            'total' => 'required|numeric',
            'paid_amount' => 'required|numeric',
            'dues' => 'required|numeric',
            'stock_date' => 'nullable|date',
            'priority' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('stocks', 'public');
            $validated['image'] = $imagePath;
        }

        $stock = Stock::create($validated);

        return response()->json($stock, Response::HTTP_CREATED);
    }

    public function show(Stock $stock)
    {
        $stock->image_url = $stock->image ? Storage::url($stock->image) : null;
        return response()->json($stock, Response::HTTP_OK);
    }

    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'product_id' => 'exists:products,id',
            'supplier_id' => 'exists:suppliers,id',
            'quantity' => 'integer',
            'buy_price' => 'numeric',
            'sell_price' => 'numeric',
            'comission' => 'numeric',
            'stock_date' => 'date',
            'priority' => 'integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($stock->image) {
                Storage::disk('public')->delete($stock->image);
            }

            $imagePath = $request->file('image')->store('stocks', 'public');
            $validated['image'] = $imagePath;
        }

        $stock->update($validated);
        $stock->image_url = $stock->image ? Storage::url($stock->image) : null;

        return response()->json($stock, Response::HTTP_OK);
    }

    public function destroy(Stock $stock)
    {
        if ($stock->image) {
            Storage::disk('public')->delete($stock->image);
        }

        $stock->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
