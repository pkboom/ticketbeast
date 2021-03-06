<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Reservation;
use App\Ticket;
use App\Factory\ConcertFactory;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_the_toal_cost()
    {
        $tickets = collect([
            (object) ['price' => 1],
            (object) ['price' => 1],
            (object) ['price' => 1],
        ]);

        $reservation = new Reservation($tickets, 'johndoe@example.com', 'test_account_1234');

        $this->assertEquals(3, $reservation->totalCost());
    }

    /** @test */
    public function a_customer_can_cancel_reserved_tickets()
    {
        $concert = ConcertFactory::createPublished(['ticket_quantity' => 3]);

        $reservation = $concert->reserveTickets(2, 'johndoe@example.com');

        $this->assertEquals(1, $concert->ticketsRemaining());

        $reservation->cancel();

        $this->assertEquals(3, $concert->ticketsRemaining());
    }

    /** @test */
    public function a_customer_can_cancel_reserved_tickets2()
    {
        $ticket1 = \Mockery::mock(Ticket::class);

        $ticket1->shouldReceive('release')->once();

        $tickets = collect([$ticket1]);

        $reservation = new Reservation($tickets, 'johndoe@example.com', 'test_account_1234');

        $reservation->cancel();
    }

    /** @test */
    public function a_customer_can_cancel_reserved_tickets3()
    {
        $tickets = collect([
            \Mockery::spy(Ticket::class),
            \Mockery::spy(Ticket::class),
        ]);

        $reservation = new Reservation($tickets, 'johndoe@example.com', 'test_account_1234');

        $reservation->cancel();

        foreach ($tickets as $ticket) {
            $ticket->shouldHaveReceived('release');
        }
    }
}
