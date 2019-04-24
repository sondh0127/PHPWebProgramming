<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    public function dishType()
    {
        return $this->belongsTo(DishPrice::class,'dish_type_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
