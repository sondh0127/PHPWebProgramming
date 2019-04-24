<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class DishInfo extends Model
{
    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
