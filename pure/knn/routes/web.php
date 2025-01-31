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
Route::get('/customers', 'CustomerController@index');

// Маршрут для отображения страницы customers
Route::get('/customers', 'CustomerController@index')->name('customers.index');

// Маршрут для обработки данных формы
Route::post('/customers/submit', 'CustomerController@submitForm')->name('customers.submit');