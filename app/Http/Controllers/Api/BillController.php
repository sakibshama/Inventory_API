<?php

// app/Http/Controllers/Api/BillController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['sell', 'customer'])->get();
        return response()->json($bills, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sell_id' => 'required|exists:sells,id',
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric',
            'paid_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'status' => 'boolean',
        ]);

        // Calculate due amount if not provided
        if (!isset($validated['due_amount'])) {
            $validated['due_amount'] = $validated['total_amount'] - ($validated['paid_amount'] ?? 0);
        }

        $bill = Bill::create($validated);
        return response()->json($bill, Response::HTTP_CREATED);
    }

    public function show(Bill $bill)
    {
        return response()->json($bill->load(['sell', 'customer']), Response::HTTP_OK);
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'sell_id' => 'exists:sells,id',
            'customer_id' => 'exists:customers,id',
            'total_amount' => 'numeric',
            'paid_amount' => 'nullable|numeric',
            'due_amount' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'status' => 'boolean',
        ]);

        // Recalculate due amount if needed
        if (isset($validated['total_amount']) || isset($validated['paid_amount'])) {
            $validated['due_amount'] = ($validated['total_amount'] ?? $bill->total_amount) - ($validated['paid_amount'] ?? $bill->paid_amount);
        }

        $bill->update($validated);
        return response()->json($bill, Response::HTTP_OK);
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
