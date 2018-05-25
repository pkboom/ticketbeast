<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Order;
use App\Billing\Charge;
use App\Ticket;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function creating_an_order_from_tickets_email_and_charge()
    {
        $charge = new Charge(['amount' => 2500, 'card_last_four' => '4242']);

        $tickets = collect([
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class),
        ]);

        $order = Order::forTickets($tickets, 'johndoe@example.com', $charge);

        $this->assertEquals('johndoe@example.com', $order->email);
        $this->assertEquals(2500, $order->amount);
        $tickets->each->shouldHaveReceived('claimFor', [$order]);
    }
}
