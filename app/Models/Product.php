<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'category_id', 'brand_id', 'quantity','status'];

    // Define relationship to Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Define relationship to Brand
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
