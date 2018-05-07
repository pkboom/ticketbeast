<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('concerts/{concert}', 'ConcertController@show');
Route::post('concerts/{concert}/orders', 'ConcertOrderController@store');
