<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    public function dishPrices()
    {
        return $this->hasMany(DishPrice::class);
    }

    public function dishImages()
    {
        return $this->hasMany(DishInfo::class);
    }

    public function dishRecipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function orderDish()
    {
        return $this->hasMany(OrderDetails::class);
    }

    public function todaysOrderDish()
    {
        return $this->hasMany(OrderDetails::class)
            ->where('created_at','like',
                \Carbon\Carbon::today()->format('Y-m-d').'%');
    }
}
