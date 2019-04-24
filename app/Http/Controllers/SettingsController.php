<?php

namespace App\Http\Controllers;

use Brotzka\DotenvEditor\DotenvEditor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{

    /**
     * Install this application
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function install()
    {
        if (config('restaurant.hasInstall') == 0) {
            return view('install');
        } else {
            return redirect()->route('installed');
        }
    }

    /**
     * Show install success when install is success
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function installSuccess()
    {
        if (config('restaurant.hasInstall') == 1) {
            return view('install-success');
        } else {
            return redirect()->route('install');
        }
    }

    /**
     * Show app settings page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setting()
    {
        $environment = config('mail.driver');
        return view('user.admin.app-setting', [
            'environment' => $environment,
        ]);
    }

    /**
     * Save mail settings
     * @param Request $request
     */
    public function mailSetting(Request $request)
    {
        $env = new DotenvEditor();
        $env->changeEnv([
            'MAIL_HOST' => $request->get('host'),
            'MAIL_PORT' => $request->get('port'),
            'MAIL_USERNAME' => $request->get('mail_address'),
            'MAIL_PASSWORD' => "'" . $request->get('password') . "'",
            'MAIL_FROM_NAME' => "'" . $request->get('mail_form') . "'",
            'MAIL_ENCRYPTION' => $request->get('encryption') != "" ? $request->get('encryption') : null,
        ]);
    }

    /**
     * Save pusher settings
     * @param Request $request
     */
    public function pusherSetting(Request $request)
    {
        $env = new DotenvEditor();
        $env->changeEnv([
            'PUSHER_APP_ID' => $request->get('app_id'),
            'PUSHER_APP_KEY' => $request->get('key'),
            'PUSHER_APP_SECRET' => $request->get('secret'),
            'PUSHER_OPTION_CLUSTER' => $request->get('cluster'),
            'PUSHER_OPTION_ENCRYPTED' => $request->get('encrypted'),
        ]);
    }

    /**
     * Save database settings
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function dbSetting(Request $request)
    {
        $env = new DotenvEditor();
        $env->changeEnv([
            'APP_URL' =>  'http://' . $request->getHost(),
            'DB_HOST' => $request->get('host'),
            'DB_PORT' => $request->get('port'),
            'DB_DATABASE' => $request->get('mysql_db'),
            'DB_USERNAME' => $request->get('mysql_user'),
            'DB_PASSWORD' => "'" . $request->get('mysql_pass') . "'",
            'HAS_INSTALL' => 1,
        ]);
        Artisan::call('config:clear');
        Artisan::call('migrate');
        return redirect()->route('installed');
    }

    /**
     * Save currency and restaurant info
     * @param Request $request
     */
    public function currencySetting(Request $request)
    {
        $env = new DotenvEditor();
        $env->changeEnv([
            'RESTAURANT_CURRENCY_SYMBOL' => $request->get('symbol'),
            'RESTAURANT_CURRENCY_CURRENCY' => $request->get('currency'),
            'RESTAURANT_VAT_NUMBER' => $request->get('vat_number'),
            'RESTAURANT_VAT_PERCENTAGE' => $request->get('var_percentage'),
            'RESTAURANT_PHONE' => "'" . $request->get('phone') . "'",
            'RESTAURANT_ADDRESS' => "'" . $request->get('address') . "'",
        ]);
    }

    /**
     * Save time zone
     * @param Request $request
     */
    public function timezoneSetting(Request $request)
    {
        $request->validate([
            'timezone' => 'required|timezone',
        ]);

        $env = new DotenvEditor();
        $env->changeEnv([
            'APP_TIMEZONE' => $request->get('timezone'),
            'APP_NAME' => '"' . $request->get('app_name') . '"',
        ]);
    }

    /**
     * Config the application cache
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cacheConfig()
    {
        Artisan::call('config:clear');
        return redirect()->route('configged');
    }

    /**
     * Show a success page that cache has been config
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cacheConfigSuccess()
    {
        return view('cache-config-success');
    }
}