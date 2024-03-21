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
