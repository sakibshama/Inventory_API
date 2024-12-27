<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment',
        'payment_type_id',
        'bill_id',
        'expense_id',
        'investment_id',
        'stock_id', 
        'others',
        'transaction_type',
        'amount',
        'status'
    ];

    // Define relationship to PaymentType
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class);
    }

    // Define relationship to Bill (optional)
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Define relationship to Expense (optional)
    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    // Define relationship to Investment (optional)
    public function investment()
    {
        return $this->belongsTo(Investment::class);
    }


    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    
}
