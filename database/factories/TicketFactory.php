<?php

use Faker\Generator as Faker;
use App\Concert;

$factory->define(App\Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => factory(Concert::class),
    ];
});

$factory->state(App\Ticket::class, 'reserved', function (Faker $faker) {
    return [
        'reserved_at' => Carbon::now(),
    ];
});
