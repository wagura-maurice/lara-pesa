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

Route::get('/c2b/register', 'MpesaController@register_c2b')->name('mpesa.register_c2b');
Route::post('/c2b/simulate', 'MpesaController@simulate_c2b')->name('mpesa.simulate_c2b');
Route::post('/c2b/confirmation', 'MpesaController@confirm_c2b')->name('mpesa.confirm_c2b');
Route::post('/c2b/validation', 'MpesaController@validate_c2b')->name('mpesa.validate_c2b');


