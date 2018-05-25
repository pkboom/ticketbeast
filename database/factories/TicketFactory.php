<?php

use Faker\Generator as Faker;
use App\Concert;
use App\Order;

$factory->define(App\Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => factory(Concert::class),
        'order_id' => factory(Order::class),
    ];
});

$factory->state(App\Ticket::class, 'reserved', function (Faker $faker) {
    return [
        'reserved_at' => Carbon::now(),
    ];
});
