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

Route::get('/pubkey', function () {
	\Mpesa::pubkey();
});

Route::post('/c2b/confirmation', function () {
	\Mpesa::c2b_confirmation();
});

Route::post('/c2b/validation', function () {
	\Mpesa::c2b_validation();
});


