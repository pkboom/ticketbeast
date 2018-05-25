<?php

use Illuminate\Database\Seeder;
use App\Concert;
use App\User;
use App\Order;
use App\Ticket;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $user = factory(User::class)->create([
            'email' => 'adam@example.com',
            'password' => bcrypt('secret'),
        ]);

        $concert = tap($user->concerts()->save(factory(Concert::class)->make()))->addTickets(10);

        $order = factory(Order::class)->create();

        factory(Ticket::class, 2)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id
        ]);
    }
}
