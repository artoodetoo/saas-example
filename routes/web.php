<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
    Route::get('billing', 'BillingController@index')->name('billing');
    Route::get('checkout/{plan_id}', 'CheckoutController@checkout')->name('checkout');
    Route::post('checkout', 'CheckoutController@processCheckout')->name('checkout.process');
    Route::get('cancel', 'BillingController@cancel')->name('cancel');
    Route::get('resume', 'BillingController@resume')->name('resume');
});
