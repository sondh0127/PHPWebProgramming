<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;
class Supplier extends Model
{
    public function purses()
    {
        return $this->hasMany(Purse::class);
    }

    public function payment()
    {
        return $this->hasMany(PursesPayment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
