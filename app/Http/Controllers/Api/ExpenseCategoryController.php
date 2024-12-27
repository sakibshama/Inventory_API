<?php

// app/Http/Controllers/Api/ExpenseCategoryController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $expenseCategories = ExpenseCategory::all();
        return response()->json($expenseCategories, Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        $expenseCategory = ExpenseCategory::create($validated);
        return response()->json($expenseCategory, Response::HTTP_CREATED);
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        return response()->json($expenseCategory, Response::HTTP_OK);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        // Log::info($expenseCategory);
        // Log::info($request);
        $validated = $request->validate([
            'name' => 'string|max:255',
            'type' => 'string|max:255',
            'status' => 'nullable|boolean',
        ]);

        $expenseCategory->update($validated);
        return response()->json($expenseCategory, Response::HTTP_OK);
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
