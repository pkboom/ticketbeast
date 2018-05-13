<?php

use Illuminate\Database\Seeder;
use App\Ticket;
use App\Concert;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        // factory(Ticket::class, 30)->create();
        factory(Concert::class)->create()->addTickets(10);
    }
}
