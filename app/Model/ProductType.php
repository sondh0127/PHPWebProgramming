<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
