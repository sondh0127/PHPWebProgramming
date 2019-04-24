<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role' => 'integer',
        'active' => 'integer',
        // 'email_verified_at' => 'datetime',
    ];

     /**
     * @return role
     */
    public function role()
    {
        return $this->role;
    }

    public function active()
    {
        return $this->active;
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function waiterOrders()
    {
        return $this->hasMany(Order::class, 'served_by');
    }

    public function waiterOrdersToday()
    {
        return $this->hasMany(Order::class, 'served_by')
            ->where('created_at', 'like',
                \Carbon\Carbon::today()->format('Y-m-d') . '%');
    }

    public function kitchenOrders()
    {
        return $this->hasMany(Order::class, 'kitchen_id');
    }

    public function kitchenOrderToday()
    {
        return $this->hasMany(Order::class, 'kitchen_id')
            ->where('created_at', 'like',
                \Carbon\Carbon::today()->format('Y-m-d') . '%');
    }
}
