<?php

Route::get('/concerts/{concert}', 'ConcertController@show');

Route::get('/mockups/order', function () {
    return view('orders.show');
});

Route::get('concerts/{concert}', 'ConcertController@show');
Route::post('concerts/{concert}/orders', 'ConcertOrderController@store');

Route::get('/orders/{order}', 'OrderController@show');
