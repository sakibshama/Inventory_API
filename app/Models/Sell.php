<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sell extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 
        'total_amount', 
        'total_profit', 
        'total_comission', 
        'status'
    ];

    // Define relationship to Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
