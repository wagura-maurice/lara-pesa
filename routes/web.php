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

Route::get('/test', 'MpesaController@test')->name('mpesa.test');

Route::get('/setCred', 'MpesaController@setCred')->name('mpesa.setCred');
Route::get('/generateAccessToken', 'MpesaController@generateAccessToken')->name('mpesa.generateAccessToken');

Route::post('/b2c/simulate', 'MpesaController@simulate_b2c')->name('mpesa.simulate.b2c');
Route::post('/b2c/callback', 'MpesaController@callback_b2c')->name('mpesa.callback.b2c');

Route::post('/b2b/simulate', 'MpesaController@simulate_b2b')->name('mpesa.simulate.b2b');
Route::post('/b2b/callback', 'MpesaController@callback_b2b')->name('mpesa.callback.b2b');

Route::get('/c2b/register', 'MpesaController@register_c2b')->name('mpesa.register.c2b');
Route::post('/c2b/simulate', 'MpesaController@simulate_c2b')->name('mpesa.simulate.c2b');
Route::post('/c2b/confirmation', 'MpesaController@confirm_c2b')->name('mpesa.confirm.c2b');
Route::post('/c2b/validation', 'MpesaController@validate_c2b')->name('mpesa.validate.c2b');

Route::post('/lnmo/request', 'MpesaController@lnmo_request')->name('mpesa.lnmo.request');
Route::post('/lnmo/callback', 'MpesaController@lnmo_callback')->name('mpesa.lnmo.callback');
Route::post('/lnmo/query', 'MpesaController@lnmo_query')->name('mpesa.lnmo.query');

Route::get('/check/balance', 'MpesaController@check_balance')->name('mpesa.check.balance');
Route::post('/check/balance/callback', 'MpesaController@check_balance_callback')->name('mpesa.check.balance.callback');

Route::post('/status/request', 'MpesaController@status_request')->name('mpesa.status.request');
Route::post('/status/callback', 'MpesaController@status_request_callback')->name('mpesa.status.request.callback');


