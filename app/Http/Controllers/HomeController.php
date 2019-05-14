<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{

    /**
     * Show the root or base view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function welcome()
    {
        if (config('restaurant.hasInstall') == 1) {
            return view('welcome');
        } else {
            return redirect()->to('/install');
        }
    }

    /**
     * Show the dashboard /home
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('home');
    }

    /**
     * View account disable page if user account is disable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function accountDisable()
    {
        return view('other.disable-account');
    }
}
