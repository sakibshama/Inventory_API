<?php

// app/Http/Controllers/Api/ExpenseController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['expenseCategory', 'paymentType'])->get();
        return response()->json($expenses, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'description' => 'nullable|string|max:255',
            'expenser_name' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'payment_type_id' => 'required|exists:payment_types,id',
            'status' => 'nullable|boolean',
        ]);

        $expense = Expense::create($validated);
        return response()->json($expense, Response::HTTP_CREATED);
    }

    public function show(Expense $expense)
    {
        return response()->json($expense->load(['expenseCategory', 'paymentType']), Response::HTTP_OK);
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'expense_category_id' => 'exists:expense_categories,id',
            'description' => 'nullable|string|max:255',
            'expenser_name' => 'string|max:255',
            'amount' => 'numeric',
            'payment_type_id' => 'exists:payment_types,id',
            'status' => 'boolean',
        ]);

        $expense->update($validated);
        return response()->json($expense, Response::HTTP_OK);
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}

