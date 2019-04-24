<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Purse extends Model
{

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pursesProducts()
    {
        return $this->hasMany(PursesProduct::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pursesPayments()
    {
        return $this->hasMany(PursesPayment::class);
    }
}
