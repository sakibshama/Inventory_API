<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investment extends Model
{
    use HasFactory;

    protected $fillable = [
        'investor_name',
        'payment_id',
        'amount',
        'image',
        'deed_image',
        'status'
    ];

    // Define relationship to PaymentType
    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_id');
    }
}
