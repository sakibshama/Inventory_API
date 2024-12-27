<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id', 'amount', 'lc_copy', 'status'
    ];


    
    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function allStock()
    {
        return $this->hasMany(AllStock::class);
    }


    public function sellItem()
    {
        return $this->hasMany(SellItem::class);
    }
}

