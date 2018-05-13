<?php

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'amount' => $faker->randomNumber(),
        'email' => $faker->email
    ];
});
