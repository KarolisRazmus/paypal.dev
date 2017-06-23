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

Route::get('/', function () {
    return view('welcome');
});


//Route::get('paypalform', ['as' => 'paypal.form', 'uses' => 'PaypalController@index']);
//Route::post('paypal/payment', ['as' => 'paypal.payment', 'uses' => 'PaypalController@payment']);
//Route::get('paypal/payment', ['as' => 'paypal.status', 'uses' => 'PaypalController@getPaymentStatus']);

Route::get('paywithpaypal', array('as' => 'addmoney.paywithpaypal','uses' => 'AddMoneyController@payWithPaypal',));
Route::post('paypal', array('as' => 'addmoney.paypal','uses' => 'AddMoneyController@postPaymentWithpaypal',));
Route::get('paypal', array('as' => 'payment.status','uses' => 'AddMoneyController@getPaymentStatus',));

