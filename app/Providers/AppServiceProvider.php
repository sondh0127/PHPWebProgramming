<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Blade::if('admin', $this->roleCheck(1));

        Blade::if('manager', $this->roleCheck(2));

        Blade::if('kitchen', $this->roleCheck(3));

        Blade::if('waiter', $this->roleCheck(4));
    }

    private function roleCheck($role)
    {
        return auth()->check() && auth()->user()->role() == $role;
    }
}
