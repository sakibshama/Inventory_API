<?php

// app/Http/Controllers/Api/SellItemController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SellItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SellItemController extends Controller
{
    public function index()
    {
        $sellItems = SellItem::with(['sell', 'product'])->get();
        return response()->json($sellItems, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sell_id' => 'required|exists:sells,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'total_price' => 'nullable|numeric', // can be calculated
            'profit' => 'nullable|numeric',
            'status' => 'boolean',
        ]);

        // Calculate total_price if not provided
        if (empty($validated['total_price'])) {
            $validated['total_price'] = $validated['quantity'] * $validated['price'];
        }

        $sellItem = SellItem::create($validated);
        return response()->json($sellItem, Response::HTTP_CREATED);
    }

    public function show(SellItem $sellItem)
    {
        return response()->json($sellItem->load(['sell', 'product']), Response::HTTP_OK);
    }

    public function update(Request $request, SellItem $sellItem)
    {
        $validated = $request->validate([
            'sell_id' => 'exists:sells,id',
            'product_id' => 'exists:products,id',
            'quantity' => 'integer',
            'price' => 'numeric',
            'total_price' => 'numeric',
            'profit' => 'nullable|numeric',
            'status' => 'boolean',
        ]);

        // Calculate total_price if not provided
        if (isset($validated['quantity']) && isset($validated['price'])) {
            $validated['total_price'] = $validated['quantity'] * $validated['price'];
        }

        $sellItem->update($validated);
        return response()->json($sellItem, Response::HTTP_OK);
    }

    public function destroy(SellItem $sellItem)
    {
        $sellItem->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
