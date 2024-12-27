<?php

// app/Http/Controllers/Api/TransactionController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['paymentType', 'bill', 'expense', 'investment'])->get();
        return response()->json($transactions, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'nullable|string|max:255',
            'payment_type_id' => 'required|exists:payment_types,id',
            'bill_id' => 'nullable|exists:bills,id',
            'expense_id' => 'nullable|exists:expenses,id',
            'stock_id' => 'nullable|exists:stocks,id',
            'investment_id' => 'nullable|exists:investments,id',
            'others' => 'nullable|string|max:255',
            'transaction_type' => 'required|in:in,out',
            'amount' => 'required|numeric',
            'status' => 'nullable|boolean',
        ]);

        $transaction = Transaction::create($validated);
        return response()->json($transaction, Response::HTTP_CREATED);
    }

    public function show(Transaction $transaction)
    {
        return response()->json($transaction->load(['paymentType', 'bill', 'expense', 'investment']), Response::HTTP_OK);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'comment' => 'nullable|string|max:255',
            'payment_type_id' => 'exists:payment_types,id',
            'bill_id' => 'nullable|exists:bills,id',
            'expense_id' => 'nullable|exists:expenses,id',
            'investment_id' => 'nullable|exists:investments,id',
            'others' => 'nullable|string|max:255',
            'transaction_type' => 'in:credit,debit',
            'amount' => 'numeric',
            'status' => 'boolean',
        ]);

        $transaction->update($validated);
        return response()->json($transaction, Response::HTTP_OK);
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }




    public function updateFromExpense(Request $request, $expenseId)
    {
        // Find the transaction using the expense_id
        $transaction = Transaction::where('expense_id', $expenseId)->firstOrFail();

        // Validate the incoming request
        $validated = $request->validate([
            'comment' => 'nullable|string|max:255',
            'payment_type_id' => 'nullable|exists:payment_types,id',
            'bill_id' => 'nullable|exists:bills,id',
            'expense_id' => 'nullable|exists:expenses,id', // Can update expense_id if needed
            'investment_id' => 'nullable|exists:investments,id',
            'others' => 'nullable|string|max:255',
            'transaction_type' => 'nullable|in:credit,debit',
            'amount' => 'required|numeric', // Assuming amount is mandatory
            'status' => 'nullable|boolean',
        ]);

        // Update the transaction with the validated data
        $transaction->update($validated);

        // Return the updated transaction as a JSON response
        return response()->json($transaction, Response::HTTP_OK);
    }


}
