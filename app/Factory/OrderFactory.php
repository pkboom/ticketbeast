<?php

namespace App\Factory;

use App\Order;
use App\Ticket;

class OrderFactory
{
    public static function createForConcert($concert, $overrides = [], $ticketQuantity = 1)
    {
        $tickets = factory(Ticket::class, $ticketQuantity)->create(['concert_id' => $concert->id]);

        $order = factory(Order::class)->create($overrides);

        $order->tickets()->saveMany($tickets);

        return $order;
    }
}
