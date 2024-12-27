<?php

namespace App\Http\Controllers\Api;

use App\Models\AllStock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;



class AllStockController extends Controller
{

    public function index()
    {
        $allStocks = AllStock::with(['product', 'supplier','container'])->get();
        return response()->json($allStocks, 200);
    }

    /**
     * Store a newly created record in the database.
     */
    public function store(Request $request)
    {


        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'container_id' => 'required|exists:containers,id',
            'stock_id' => 'required|exists:stocks,id',
            'quantity' => 'required|integer',
            'buy_price' => 'required|numeric',
            'sell_price' => 'required|numeric',
            'comission' => 'nullable|numeric',
            'stock_date' => 'required|date',
            'dues' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'priority' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'nullable|boolean',
        ]);

        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('allstocks', 'public');
            $validated['image'] = $imagePath;
        }

        $allStock = AllStock::create($validated);
        // $allStock->image_url = $allStock->image ? Storage::url($allStock->image) : null;

        return response()->json($allStock, Response::HTTP_CREATED);
    }

    /**
     * Display the specified record.
     */
    public function show($id)
    {
        $allStock = AllStock::find($id);

        if (!$allStock) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json($allStock, 200);
    }

    /**
     * Update the specified record in the database.
     */
    public function update(Request $request, $id)
    {
        $allStock = AllStock::find($id);

        if (!$allStock) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'nullable|integer',
            'buy_price' => 'nullable|numeric',
            'sell_price' => 'nullable|numeric',
            'comission' => 'nullable|numeric',
            'stock_date' => 'nullable|date',
            'dues' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'priority' => 'nullable|integer',
            'image' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $allStock->update($validated);
        return response()->json($allStock, 200);
    }

    /**
     * Remove the specified record from the database.
     */
    public function destroy($id)
    {
        $allStock = AllStock::find($id);

        if (!$allStock) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        $allStock->delete();
        return response()->json(['message' => 'Record deleted successfully'], 200);
    }
}
