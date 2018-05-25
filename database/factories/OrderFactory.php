<?php

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'confirmation_number' => 'ORDERCONFIRMATION1234',
        'amount' => $faker->randomNumber(),
        'email' => $faker->email,
        'card_last_four' => '1234',
    ];
});
