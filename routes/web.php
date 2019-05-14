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

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@welcome');

// Install
Route::post('/db-setup', 'SettingsController@dbSetting');
Route::get('/install', 'SettingsController@install')->name('install');
Route::get('/install-success', 'SettingsController@installSuccess')->name('installed');
Route::get('/cache-config', 'SettingsController@cacheConfig');
Route::get('/cache-config-success', 'SettingsController@cacheConfigSuccess')->name('configged');

// Authentication
Auth::routes();

// Account Disable
Route::get('/account-disable', 'HomeController@accountDisable')->middleware('inactive.user');

//Routes only access with authenticated users
Route::middleware('active.user')->group(function () {

    // Common route
    Route::get('/home', 'HomeController@index')->name('home');

    //Profile Settings
    Route::get('/profile', 'UserController@profileInfo');
    Route::get('/profile-edit', 'UserController@profileEdit');
    Route::post('/post-profile', 'UserController@profileUpdate');
    Route::post('/change-password', 'UserController@changePassword');

    // Admin Only
    Route::middleware(['admin'])->group(function () {
        //App Settings
        Route::get('/app-settings', 'SettingsController@setting');
        Route::post('/save-pusher-conf', 'SettingsController@pusherSetting');
        Route::post('/save-s3-conf', 'SettingsController@s3Setting');
        Route::post('/save-mail-conf', 'SettingsController@mailSetting');
        Route::post('/save-timezone', 'SettingsController@timezoneSetting');
        Route::post('/save-currency', 'SettingsController@currencySetting');
    });


    // Admin & Shop Manager Only
    Route::middleware(['manager'])->group(function () {
        // User Management
        Route::resource('employee', 'UserController');
        Route::post('/update-employee/{id}', 'UserController@updateEmployee');

        Route::namespace('Employee')->group(function () {
            Route::get('/delete-employee/{id}', 'EmployeeController@deleteEmployee');
        });

        Route::namespace('Stock')->group(function () {

            // Unit Management
            Route::get('/add-unit', 'UnitController@addUnit');
            Route::get('/all-unit', 'UnitController@allUnit');
            Route::get('/edit-unit/{id}', 'UnitController@editUnit');
            Route::get('/delete-unit/{id}', 'UnitController@deleteUnit');
            Route::get('/cannot-delete-unit/{id}', 'UnitController@cannotDeleteUnit');
            Route::post('/save-unit', 'UnitController@saveUnit');
            Route::post('/update-unit/{id}', 'UnitController@updateUnit');

            // Product Type Management
            Route::get('/add-product-type', 'ProductTypeController@addProductType');
            Route::get('/all-product-type', 'ProductTypeController@allProductType');
            Route::get('/edit-product-type/{id}', 'ProductTypeController@editProductType');
            Route::get('/delete-product-type/{id}', 'ProductTypeController@deleteProductType');
            Route::get('/cannot-delete-product-type/{id}', 'ProductTypeController@cannotDeleteProductType');
            Route::post('/save-product-type', 'ProductTypeController@saveProductType');
            Route::post('/update-product-type/{id}', 'ProductTypeController@updateProductType');


            // Stock Management
            Route::get('/all-item', 'StockController@allStock');
            Route::get('/add-item', 'StockController@addStock');
            Route::get('/edit-item/{id}', 'StockController@editStock');
            Route::get('/view-item/{id}', 'StockController@viewStock');
            Route::get('/delete-item/{id}', 'StockController@deleteStock');
            Route::get('/cannot-delete-item/{id}', 'StockController@cannotDeleteStock');
            Route::post('/save-item', 'StockController@saveStock');
            Route::post('/update-item/{id}', 'StockController@updateStock');

            // Purses
            Route::get('/new-purses', 'PursesController@addPurses');
            Route::get('/all-purses', 'PursesController@allPurses');
            Route::get('/edit-purses/{id}', 'PursesController@editPurses');
            Route::get('/delete-purses/{id}', 'PursesController@deletePurses');
            Route::get('/delete-purses-product/{id}', 'PursesController@deletePursesProduct');
            Route::post('/save-purses', 'PursesController@savePurses');
            Route::post('/save-purses-product/{purses_id}', 'PursesController@savePursesProduct');
            Route::post('/update-purses/{id}', 'PursesController@updatePurses');

            // Purses JSON
            Route::get('/get-purses-details/{id}', 'PursesController@getPursesDetails');
            Route::get('/get-unit-of-product/{id}', 'PursesController@getUnitOfProduct');

            // Purses payment
            Route::get('/purses-payment/{purses_id}', 'PursesController@pursesPayment');
            Route::post('/save-purses-payment/{purses_id}', 'PursesController@savePursesPayment');

            //Supplier
            Route::get('/all-supplier', 'SupplierController@allSupplier');
            Route::get('/add-supplier', 'SupplierController@addSupplier');
            Route::get('/view-supplier/{id}', 'SupplierController@viewSupplier');
            Route::get('/edit-supplier/{id}', 'SupplierController@editSupplier');
            Route::get('/delete-supplier/{id}', 'SupplierController@deleteSupplier');
            Route::post('/save-supplier', 'SupplierController@saveSupplier');
            Route::post('/update-supplier/{id}', 'SupplierController@updateSupplier');
        });

        Route::namespace('Dish')->group(function () {

            // Dish Type Management
            Route::get('/add-dish-type', 'DishTypeController@addDishType');
            Route::get('/all-dish-type', 'DishTypeController@allDishType');
            Route::get('/edit-dish-type/{id}', 'DishTypeController@editDishType');
            Route::get('/delete-dish-type/{id}', 'DishTypeController@deleteDishType');
            Route::post('/save-dish-type', 'DishTypeController@saveDishType');
            Route::post('/update-dish-type/{id}', 'DishTypeController@updateDishType');

            //Dish Management
            Route::get('/add-dish', 'DishController@addDish');
            Route::get('/all-dish', 'DishController@allDish');
            Route::get('/view-dish/{id}', 'DishController@viewDish');
            Route::get('/edit-dish/{id}', 'DishController@editDish');
            Route::get('/delete-dish/{id}', 'DishController@deleteDish');
            Route::post('/save-dish', 'DishController@saveDish');
            Route::post('/update-dish/{id}', 'DishController@updateDish');

            // Dish Report
            Route::get('/dish-stat', 'DishController@dishStat');
            Route::post('/dish-stat-post', 'DishController@postDishStat');
            Route::get('/dish-stat/dish={id}/start={start_date}/end={end_date}', 'DishController@showDishStat');

            // Dish Price
            Route::get('/dish-price/{dish_id}', 'DishController@addDishPrice');
            Route::get('/edit-dish-price/{id}', 'DishController@editDishPrice');
            Route::post('/save-dish-price', 'DishController@saveDishPrice');
            Route::post('/update-dish-price/{id}', 'DishController@updateDishPrice');

            // Dish Image
            Route::get('/dish-image/{dish_id}', 'DishController@addDishImage');
            Route::get('/delete-dish-image/{id}', 'DishController@deleteDishImage');
            Route::post('/save-dish-image', 'DishController@saveDishImage');

            // Dish Recipes
            Route::get('/dish-recipe/{dish_id}', 'RecipeController@addRecipe');
            Route::get('/edit-recipes/{id}', 'RecipeController@editRecipe');
            Route::get('/delete-recipes/{id}', 'RecipeController@deleteRecipe');

            Route::post('/save-recipes/{dish_id}', 'RecipeController@saveRecipe');
            Route::post('/update-recipes/{id}', 'RecipeController@updateRecipe');
        });


        Route::namespace('Order')->group(function () {

            // Table Controller
            Route::get('/all-table', 'TableController@allTable');
            Route::get('/add-table', 'TableController@addTable');
            Route::get('/edit-table/{id}', 'TableController@editTable');
            Route::get('/delete-table/{id}', 'TableController@deleteTable');
            Route::post('/save-table', 'TableController@saveTable');
            Route::post('/update-table/{id}', 'TableController@updateTable');
        });

        Route::namespace('Accountant')->group(function () {

            // AccountantController
            Route::get('/account-summary', 'AccountantController@summary');
            Route::get('/add-expense', 'AccountantController@addExpanse');
            Route::get('/edit-expanse/{id}', 'AccountantController@editExpanse');
            Route::post('/save-expanse', 'AccountantController@saveExpanse');
            Route::post('/update-expanse/{id}', 'AccountantController@updateExpanse');
            Route::get('/delete-expanse/{id}', 'AccountantController@deleteExpanse');
            Route::get('/all-expanse', 'AccountantController@allExpanse');
            Route::get('/all-income', 'AccountantController@allIncome');
        });
    });

    // Kitchen Only (All kitchen access can also access by admin or shop manager)
    Route::middleware(['kitchen'])->group(function () {

        Route::namespace('Order')->group(function () {
            // Kitchen
            Route::get('/kitchen-orders', 'OrderController@kitchenOrderToJSON');
            Route::get('/kitchen-start-cooking/{id}', 'OrderController@kitchenStartCooking');
            Route::get('/kitchen-complete-cooking/{id}', 'OrderController@kitchenCompleteCooking');
            Route::get('/cooking-history', 'KitchenController@myCookingHistory');
            // Live Kitchen
            Route::get('/live-kitchen', 'KitchenController@liveKitchen');
            Route::get('/live-kitchen-admin-json', 'KitchenController@adminLiveKitchenJSON');
            // Kitchen Stat
            Route::get('/kitchen-stat', 'KitchenController@kitchenStat');
            Route::post('/kitchen-stat-post', 'KitchenController@postKitchenStat');
            Route::get('/kitchen-stat/kitchen={id}/start={start_date}/end={end_date}', 'KitchenController@showKitchenStat');
        });
    });

    // Waiter Only
    Route::middleware(['waiter'])->group(function () {
        //Dish
        Route::get('/dish-types/{dish_id}', 'Dish\RecipeController@getTypesOfDish');

        Route::namespace('Order')->group(function () {
            // Orders
            Route::get('/new-order', 'OrderController@newOrder');
            Route::get('/print-order/{id}', 'OrderController@printOrder');
            Route::get('/marked-order/{id}', 'OrderController@markOrder');
            Route::get('/delete-order/{id}', 'OrderController@deleteOrder');
            Route::get('/all-order', 'OrderController@allOrder');
            Route::get('/non-paid-order', 'OrderController@nonPaidOrder');
            Route::get('/get-order-details/{id}', 'OrderController@getOrderDetails');
            Route::get('/edit-order/{id}', 'OrderController@editOrder');
            Route::post('/save-order', 'OrderController@saveOrder');
            Route::post('/update-order/{id}', 'OrderController@updateOrder');
            // Waiter Order
            Route::get('/order-served/{id}', 'OrderController@orderServed');
            // Order By Waiter
            Route::get('/my-orders', 'OrderController@myOrder');

            // Live Kitchen for waiter
            Route::get('/kitchen-status', 'KitchenController@waiterLiveKitchen');
            Route::get('/kitchen-status-waiter-json', 'KitchenController@waiterLiveKitchenJSON');
            // Waiter Stat
            Route::get('/waiter-stat', 'WaiterController@waiterStat');
            Route::post('/waiter-stat-post', 'WaiterController@postWaiterStat');
            Route::get('/waiter-stat/waiter={id}/start={start_date}/end={end_date}', 'WaiterController@showWaiterStat');
        });
    });
});
