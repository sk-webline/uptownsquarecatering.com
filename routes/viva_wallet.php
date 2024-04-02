<?php

    /*
    |--------------------------------------------------------------------------
    | Viva Wallet Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your viva wallet gateway system
    |
    */

    Route::post('/pay_order', 'VivaController@pay_order')->name('viva.pay_order');

    Route::get('/success', 'VivaController@success')->name('viva.success');

    Route::get('/failed', 'VivaController@failed')->name('viva.failed');

    Route::get('/verify_website', 'VivaController@verify_website_on_viva_wallet');

    Route::any('callback', 'VivaController@callback');

    /* ToDo: Test Routes */
    Route::get('/get_transaction/{transaction_id}', 'VivaController@getTransaction');

    Route::post('/create_card_token', 'VivaController@save_card_for_future_use')->name('viva.save_card');

    Route::post('/edit_credit_card', 'VivaController@edit_credit_card')->name('viva.edit_credit_card');

    Route::get('/test', 'VivaController@test')->name('viva.test');

//    Application Routes For Viva

    Route::post('application/pay_order', 'AppVivaController@pay_order')->name('app_viva.pay_order');
    Route::post('application/preauth_pay_order', 'AppVivaController@preauth_pay_order')->name('app_viva.preauth_pay_order');
    Route::get('app/success', 'AppVivaController@success')->name('app_viva.success');
    Route::get('app/failed', 'AppVivaController@failed')->name('app_viva.failed');
    Route::get('app/verify_website', 'AppVivaController@verify_website_on_viva_wallet');

    Route::post('application/refund_order', 'AppVivaController@cancel_order')->name('app_viva.refund_order');

//    Route::get('application/test', 'AppVivaController@test')->name('app_viva.test');





