<?php

// app/Http/Controllers/Api/SellController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SellController extends Controller
{
    public function index()
    {
        $sells = Sell::with('customer')->get();
        return response()->json($sells, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'nullable|numeric',
            'status' => 'boolean',
        ]);

        $sell = Sell::create($validated);
        return response()->json($sell, Response::HTTP_CREATED);
    }

    public function show(Sell $sell)
    {
        return response()->json($sell->load('customer'), Response::HTTP_OK);
    }

    public function update(Request $request, Sell $sell)
    {
        $validated = $request->validate([
            'customer_id' => 'exists:customers,id',
            'total_amount' => 'nullable|numeric',
            'status' => 'boolean',
        ]);

        $sell->update($validated);
        return response()->json($sell, Response::HTTP_OK);
    }

    public function destroy(Sell $sell)
    {
        $sell->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
