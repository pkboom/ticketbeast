<?php

// auth()->loginUsingId(1);
Route::Redirect('/', '/backstage/concerts');

Route::get('/concerts/{concert}', 'ConcertController@show')->name('concerts.show');
Route::post('/concerts/{concert}/orders', 'ConcertOrderController@store')->name('concerts.store');
Route::get('/orders/{confirmationNumber}', 'OrderController@show');

Auth::routes();

Route::get('/invitations/{code}', 'InvitationController@show')->name('invitations.show');

// Route::group([
//     'middleware' => 'auth', 'prefix' => 'backstage', 'namespace' => 'Backstage'], function () {
Route::middleware('auth')
        ->prefix('backstage')
        ->namespace('Backstage')
        ->name('backstage.')
        ->group(function () {
            Route::middleware('stripe')
                ->group(function () {
                    Route::get('/concerts', 'ConcertController@index')->name('concerts.index');
                    Route::get('/concerts/new', 'ConcertController@create')->name('concerts.new');
                    Route::post('/concerts', 'ConcertController@store')->name('concerts.store');
                    Route::get('/concerts/{concert}/edit', 'ConcertController@edit')->name('concerts.edit');
                    Route::patch('/concerts/{concert}', 'ConcertController@update')->name('concerts.update');

                    Route::post('/published-concerts', 'PublishedConcertController@store')->name('published-concerts.store');
                    Route::get('/published-concerts/{concertId}/orders', 'PublishedConcertOrderController@index')->name('published-concert-orders.index');

                    Route::get('/concerts/{concertId}/messages/new', 'ConcertMessageController@create')->name('concert-messages.new');
                    Route::post('/concerts/{concertId}messages', 'ConcertMessageController@store')->name('concert-messages.store');
                });

            Route::get('/stripe-connect/connect', 'StripeConnectController@connect')->name('stripe-connect.connect');
            Route::get('/stripe-connect/authorize', 'StripeConnectController@authorizeRedirect')->name('stripe-connect.authorize');
            Route::get('/stripe-connect/redirect', 'StripeConnectController@redirect')->name('stripe-connect.redirect');
        });
