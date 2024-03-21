<?php

use Carbon\Carbon;

Route::get('/', 'CanteenCashierController@login');
Route::get('/login', 'CanteenCashierController@login')->name('canteen_cashier.login');

Route::group(['middleware' => ['canteen_cashier']], function() {

    Route::get('/select_location', 'CanteenCashierController@select_location' )->name('canteen_cashier.select_location');
    Route::post('/get_canteen_locations', 'OrganisationController@get_canteen_locations')->name('canteen_cashier.get_canteen_locations');
    Route::post('/location_selection', 'CanteenCashierController@location_selection')->name('canteen_cashier.location_selection');

    Route::group([ 'middleware' => ['canteen_cashierSelectedLocation']], function() {

        Route::get('/select_operation', 'CanteenCashierController@select_operation')->name('canteen_cashier.select_operation');
        Route::post('/canteen_report_export', 'CanteenCashierController@report_export' )->name('canteen_cashier.report_export');
        Route::get('/rfid_scanning', 'CanteenCashierController@dashboard' )->name('canteen_cashier.dashboard');
        Route::post('/unscheduled', 'CanteenCashierController@unscheduled' )->name('canteen_cashier.unscheduled');

        Route::post('/unscheduled/delivery', 'CanteenCashierController@unscheduled_delivery' )->name('canteen_cashier.unscheduled_delivery');
        Route::post('/current_break_scanning', 'CanteenCashierController@current_break_scanning' )->name('canteen_cashier.current_break_scanning');
        Route::post('/current_break_scanning/delivery', 'CanteenCashierController@current_break_delivery' )->name('canteen_cashier.current_break_delivery');
        Route::post('/view_order', 'CanteenCashierController@view_order' )->name('canteen_cashier.view_order');
        Route::post('/view_order/undo_delivery', 'CanteenCashierController@undo_delivery' )->name('canteen_cashier.undo_delivery');



//        Route::get('/buffet_scanning', 'CanteenCashierController@buffet_scanning')->name('canteen_cashier.buffet_scanning');
//        Route::get('/buffet_serving/{type}', 'CanteenCashierController@buffet_serving')->name('canteen_cashier.buffet_serving');
//
//        Route::post('/serve_meal_type', 'CateringPlanPurchaseController@serve_meal_type')->name('catering_plan_purchase.serve_meal_type');
//
//        Route::get('/get_card_today_plan', 'CateringPlanPurchaseController@get_card_today_plan')->name('catering_plan_purchase.get_card_today_plan');
//        Route::post('/submit_card_meal', 'CateringPlanPurchaseController@submit_card_meal')->name('catering_plan_purchase.submit_card_meal');
//
//        Route::post('/cancel_meal/{card_id}/{card_usage_id}', 'CateringPlanPurchaseController@cancel_meal')->name('card_usage.cancel_meal');
    });



//    Route::post('/card_plan/{card_id}', 'CateringPlanPurchaseController@get_card_today_plan' )->name('catering_plan_purchase.get_card_today_plan');

//    Route::get('/dashboard', 'HomeController@cashier_dashboard' )->name('canteen_cashier.dashboard');
});
