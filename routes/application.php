<?php

use App\Models\CanteenPurchase;
use Carbon\Carbon;

//Route::get('/test', function() {
//
////    $canteen_user = \App\Models\CanteenAppUser::find(1);
//    $canteen_user = auth()->guard('application')->user();
//
//    $order = \App\Models\AppOrder::find(18);
//
//    $date = '2024-01-26';
//    $break_num = 2;
//
//    $existing = CanteenPurchase::where('canteen_app_user_id', $canteen_user->id)->where('date', $date)->where('break_num', $break_num)->first();
//
//    if($existing != null && FALSE){
//        $meal_code = $existing->meal_code;
//    }else{
//        $temp = CanteenPurchase::where('date', $date)->where('break_num', $break_num)->max('meal_code');
//
//        $t = new CanteenPurchase();
//
//        $meal_code = $t->extractNumberFromMealCode($temp)+1;
//    }
//
//    dd($meal_code, $t->formatMealCode($meal_code));
//
//    $temp = CanteenPurchase::where('date', $date)->where('break_num', $break_num)->max('meal_code');
//
//    dd($temp);
//
//
//    return view('emails.application_invoice', compact('order') );
//
//});
//Route::get('/debug', function() {
////    \Illuminate\Support\Facades\Session::flash();
//
//    dd(\Illuminate\Support\Facades\Session::all());
//});

Route::get('/canteen-service-worker-routes', 'ServiceWorkerController@getCanteenRoutes');
Route::get('/', 'ApplicationController@login');
Route::get('/offline', 'ApplicationController@offline')->name('application.offline');
Route::get('/login', 'ApplicationController@login')->name('application.login');
Route::post('login', 'Auth\ApplicationLoginController@login')->name('application.auth');
Route::get('/logout', '\App\Http\Controllers\Auth\ApplicationLoginController@logout')->name('application.logout');

Route::group(['middleware' => ['app_user']], function () {
    Route::get('/home', 'ApplicationController@index')->name('application.home');
    Route::get('/choose-snack', 'ApplicationController@choose_snack')->name('application.choose_snack');
    Route::get('/cart', 'ApplicationController@view_cart')->name('application.cart');
    Route::post('/checkout', 'ApplicationController@view_checkout')->name('application.checkout');
    Route::get('/order-success/{order_code}', 'ApplicationController@order_success')->name('application.order_success');
    Route::get('/order-pending/{order_code}', 'ApplicationController@order_pending')->name('application.order_pending');
    Route::get('/history', 'ApplicationController@history')->name('application.history');
    Route::get('/upcoming-meals', 'ApplicationController@upcoming_meals')->name('application.upcoming_meals');
    Route::get('/contact', 'ApplicationController@contact')->name('application.contact');
    Route::get('/account', 'ApplicationController@account')->name('application.account');
    Route::get('/profile', 'ApplicationController@profile')->name('application.profile');
    Route::post('/profile/update_password', 'ApplicationController@update_password')->name('application.update_password');
    Route::get('/available-balance', 'ApplicationController@available_balance')->name('application.available_balance');
    Route::get('/credit-card', 'ApplicationController@credit_card')->name('application.credit_card');

    Route::post('/user/get_week_calendar_view_ajax', 'ApplicationController@get_week_calendar_view')->name('application.get_week_calendar_view');
    Route::post('/cart/add_to_cart', 'ApplicationController@addToCart')->name('application.addToCart');
    Route::post('/cart/remove_from_cart', 'ApplicationController@removeFromCart')->name('application.removeFromCart');
    Route::post('/cart/nav_cart', 'ApplicationController@nav_cart')->name('application.nav_cart');
    Route::post('/history/order_details', 'ApplicationController@get_order_details')->name('application.order_details');

});

return [
     '/application/home', '/application/choose-snack'
];
