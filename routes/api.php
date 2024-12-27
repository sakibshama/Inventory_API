<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\SellController;
use App\Http\Controllers\Api\SellItemController;
use App\Http\Controllers\Api\BillController;
use App\Http\Controllers\Api\PaymentTypeController;
use App\Http\Controllers\Api\InvestmentController;
use App\Http\Controllers\Api\ExpenseCategoryController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\AllStockController;
use App\Http\Controllers\Api\ProductStockController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AllUserController;
use App\Http\Controllers\Api\ContainerController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/



// Artisan::call('storage:link');
Route::get('/optimize', function () {
    try {
        Artisan::call('optimize');
        return 'Application optimized successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/cache-clear', function () {
    try {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return 'Cache cleared successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::get('/storage-link', function () {
    try {
        Artisan::call('storage:link');
        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('categories', CategoryController::class);
Route::apiResource('brands', BrandController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('stocks', StockController::class);
Route::apiResource('sells', SellController::class);
Route::apiResource('sell-items', SellItemController::class);
Route::apiResource('bills', BillController::class);
Route::apiResource('payment-types', PaymentTypeController::class);
Route::apiResource('investments', InvestmentController::class);
Route::apiResource('expense-categories', ExpenseCategoryController::class);
Route::apiResource('expenses', ExpenseController::class);
Route::apiResource('transactions', TransactionController::class);

Route::apiResource('all-stock', AllStockController::class);
Route::apiResource('roles', RoleController::class);

Route::apiResource('all-users', AllUserController::class);
Route::apiResource('containers', ContainerController::class);


Route::get('products/{productId}/stocks', [ProductStockController::class, 'getStocksByProduct']);

Route::put('transactions/update/{expenseId}', [TransactionController::class, 'updateFromExpense']);