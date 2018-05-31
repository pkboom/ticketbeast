<?php

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'confirmation_number' => 'ORDERCONFIRMATION1234',
        'amount' => $faker->numberBetween($min = 1000, $max = 10000),
        'email' => $faker->email,
        'card_last_four' => '1234',
    ];
});
