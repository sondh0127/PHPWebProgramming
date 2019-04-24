<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', 'HomeController@welcome');;

// Install
Route::get('/install', 'SettingsController@install');
Route::get('/install-success', 'SettingsController@installSuccess')->name('installed');
Route::get('/cache-config', 'SettingsController@cacheConfig');
Route::post('/db-setup', 'SettingsController@dbSetting');
Route::get('/cache-config-success', 'SettingsController@cacheConfigSuccess');
