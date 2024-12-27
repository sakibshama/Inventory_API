<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 
        'supplier_id', 
        'container_id',
        'quantity', 
        'buy_price', 
        'sell_price', 
        'comission',
        'paid_amount',
        'dues',
        'total',
        'stock_date', 
        'priority', 
        'image', 
        'status'
    ];

    // Define relationship to Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Define relationship to Supplier
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function container()
    {
        return $this->belongsTo(Container::class);
    }


    public function allStock()
    {
        return $this->hasOne(AllStock::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }


    public function sellItem()
    {
        return $this->hasMany(SellItem::class);
    }


}
