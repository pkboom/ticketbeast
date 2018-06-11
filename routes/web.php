<?php

Route::Redirect('/', '/backstage/concerts');

Route::get('/concerts/{concert}', 'ConcertController@show')->name('concerts.show');
Route::post('/concerts/{concert}/orders', 'ConcertOrderController@store');
Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Auth::routes();

Route::group(['middleware' => 'auth', 'prefix' => 'backstage', 'namespace' => 'Backstage'], function () {
    Route::get('/concerts', 'ConcertController@index')->name('backstage.concerts.index');
    Route::get('/concerts/new', 'ConcertController@create')->name('backstage.concerts.new');
    Route::post('/concerts', 'ConcertController@store');
    Route::get('/concerts/{concert}/edit', 'ConcertController@edit')->name('backstage.concerts.edit');
    Route::patch('/concerts/{concert}', 'ConcertController@update')->name('backstage.concerts.update');

    Route::post('/published-concerts', 'PublishedConcertController@store')->name('backstage.published-concerts.store');
    Route::get('/published-concerts/{concertId}/orders', 'PublishedConcertOrderController@index')->name('backstage.published-concert-orders.index');

    Route::get('/concerts/{concertId}/messages/new', 'ConcertMessageController@create')->name('backstage.concert-messages.new');
    Route::post('/concerts/{concertId}messages', 'ConcertMessageController@store')->name('backstage.concert-messages.store');
});
