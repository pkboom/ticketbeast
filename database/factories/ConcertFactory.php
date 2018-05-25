<?php

use Faker\Generator as Faker;
use Carbon\Carbon;
use App\User;

$factory->define(App\Concert::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'title' => $faker->word,
        'subtitle' => $faker->sentence,
        'date' => $faker->dateTime(),
        'ticket_price' => $faker->numberBetween(10, 1000),
        'venue' => $faker->streetName,
        'venue_address' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'zip' => $faker->postcode,
        'additional_information' => $faker->paragraph,
        'ticket_quantity' => $faker->randomDigitNotNull
        // 'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(App\Concert::class, 'published', function ($faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});
