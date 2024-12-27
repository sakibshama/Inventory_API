<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductStockController extends Controller
{
   
    public function getStocksByProduct($productId)
    {
        // Retrieve all stocks for the specified product_id
        $stocks = Stock::with(['supplier', 'product'])
            ->where('product_id', $productId)
            ->get();

        if ($stocks->isEmpty()) {
            return response()->json([
                'message' => 'No stocks found for the specified product ID.',
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($stocks, Response::HTTP_OK);
    }
}
