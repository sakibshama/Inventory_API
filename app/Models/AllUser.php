<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'gender',
        'phone',
        'c_percentage',
        'c_amount',
        'image',
        'status',
    ];

    // protected $hidden = [
    //     'password',
    // ];
    

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function bill()
    {
        return $this->hasMany(Bill::class);
    }
}
