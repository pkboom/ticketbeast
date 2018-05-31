<?php

use Illuminate\Database\Seeder;
use App\Concert;
use App\User;
use App\Factory\ConcertFactory;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $gateway = new \App\Billing\FakePaymentGateway;

        $user = factory(User::class)->create([
            'email' => 'a@a.com',
            'password' => bcrypt('a'),
        ]);

        Carbon::setTestNow(Carbon::now()->subMonths(3));

        $concert = ConcertFactory::createPublished([
            'user_id' => $user,
            'date' => Carbon::today()->addMonths(3)->hour(20),
            'ticket_quantity' => 250,
        ]);

        foreach (range(1, 50) as $value) {
            Carbon::setTestNow(Carbon::instance($faker->dateTimeBetween('-2 months')));

            $concert->reserveTickets(rand(1, 4), $faker->safeEmail)
                ->complete($gateway, $gateway->getValidTestToken($faker->creditCardNumber));
        }

        Carbon::setTestNow();
        // $concert = tap($user->concerts()->save(factory(Concert::class)->make()))->addTickets(10);

        factory(Concert::class)->create();
    }
}
