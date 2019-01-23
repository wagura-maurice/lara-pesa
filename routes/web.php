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

Route::post('/b2c/simulate', 'MpesaController@simulate_b2c')->name('mpesa.simulate_b2c');
Route::post('/b2c/callback', 'MpesaController@callback_b2c')->name('mpesa.callback_b2c');

Route::post('/b2b/simulate', 'MpesaController@simulate_b2b')->name('mpesa.simulate_b2b');
Route::post('/b2b/callback', 'MpesaController@callback_b2b')->name('mpesa.callback_b2b');

Route::get('/c2b/register', 'MpesaController@register_c2b')->name('mpesa.register_c2b');
Route::post('/c2b/simulate', 'MpesaController@simulate_c2b')->name('mpesa.simulate_c2b');
Route::post('/c2b/confirmation', 'MpesaController@confirm_c2b')->name('mpesa.confirm_c2b');
Route::post('/c2b/validation', 'MpesaController@validate_c2b')->name('mpesa.validate_c2b');

Route::post('/lnmo/request', 'MpesaController@lnmo_request')->name('mpesa.lnmo_request');
Route::post('/lnmo/callback', 'MpesaController@lnmo_callback')->name('mpesa.lnmo_callback');
Route::post('/lnmo/query', 'MpesaController@lnmo_query')->name('mpesa.lnmo_query');

Route::get('/check_balance', 'MpesaController@check_balance')->name('mpesa.check_balance');
Route::post('/check_balance/callback', 'MpesaController@check_balance_callback')->name('mpesa.check_balance.callback');


