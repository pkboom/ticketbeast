<?php

use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(App\Concert::class, function (Faker $faker) {
    return [
        'title' => $faker->word,
        'subtitle' => $faker->sentence,
        'date' => $faker->dateTime(),
        'ticket_price' => $faker->randomNumber(),
        'venue' => $faker->streetName,
        'venue_address' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'zip' => $faker->postcode,
        'additional_information' => $faker->paragraph,
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(App\Concert::class, 'published', function ($faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(App\Concert::class, 'unpublished', function ($faker) {
    return [
        'published_at' => null
    ];
});
