<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sell_id',
        'product_id',
        'stock_id',
        'container_id',
        'quantity',
        'price',
        'comission',
        'total_price',
        'profit',
        'status'
    ];

    // Define relationship to Sell
    public function sell()
    {
        return $this->belongsTo(Sell::class);
    }

    // Define relationship to Stock
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    // Define relationship to Container
    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    // Define relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}
