<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_category_id',
        'description',
        'expenser_name',
        'amount',
        'payment_type_id',
        'status'
    ];

    // Define relationship to ExpenseCategory
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    // Define relationship to PaymentType
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }
}
