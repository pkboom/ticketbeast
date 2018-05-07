<?php

use Faker\Generator as Faker;
use App\Concert;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'concert_id' => factory(Concert::class)
    ];
});
