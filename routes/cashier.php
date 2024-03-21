<?php

/*
  |--------------------------------------------------------------------------
  | Cashier Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register admin routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

Route::get('/cashier', 'HomeController@cashier_dashboard' )->name('cashier.dashboard')->middleware([ 'cashier', 'cashierSelectedLocation']);

Route::get('/cashier/login', 'HomeController@login_cashier')->name('cashier.login');

Route::group(['prefix' => 'cashier', 'middleware' => ['cashier']], function() {

    Route::get('/select_location', 'HomeController@select_location' )->name('cashier.select_location');
    Route::post('/get_organisation_locations', 'OrganisationController@get_organisation_locations')->name('cashier.get_organisation_locations');
    Route::post('/location_selection', 'HomeController@location_selection')->name('cashier.location_selection');

    Route::group([ 'middleware' => ['cashierSelectedLocation']], function() {


        Route::get('/buffet_scanning', 'CashierController@buffet_scanning')->name('cashier.buffet_scanning');
        Route::get('/buffet_serving/{type}', 'CashierController@buffet_serving')->name('cashier.buffet_serving');

        Route::post('/serve_meal_type', 'CateringPlanPurchaseController@serve_meal_type')->name('catering_plan_purchase.serve_meal_type');

        Route::get('/get_card_today_plan', 'CateringPlanPurchaseController@get_card_today_plan')->name('catering_plan_purchase.get_card_today_plan');
        Route::post('/submit_card_meal', 'CateringPlanPurchaseController@submit_card_meal')->name('catering_plan_purchase.submit_card_meal');

        Route::post('/cancel_meal/{card_id}/{card_usage_id}', 'CateringPlanPurchaseController@cancel_meal')->name('card_usage.cancel_meal');
    });



//    Route::post('/card_plan/{card_id}', 'CateringPlanPurchaseController@get_card_today_plan' )->name('catering_plan_purchase.get_card_today_plan');

//    Route::get('/dashboard', 'HomeController@cashier_dashboard' )->name('cashier.dashboard');
});
