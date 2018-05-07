<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use Carbon\Carbon;
use App\Ticket;

class ConcertTest extends TestCase
{
    use RefreshDatabase;

    protected $concert;

    public function setUp()
    {
        parent::setUp();

        $this->concert = tap(factory(Concert::class)->states('published')->create())
                            ->addTickets(3);
    }

    /** @test */
    public function can_get_formatted_date_time()
    {
        $concert = make(Concert::class, [
            'date' => Carbon::parse('2016-12-01 17:00'),
            ]);

        $this->assertEquals('December 1, 2016', $concert->formattedDate);
        $this->assertEquals('5:00pm', $concert->formattedStartTime);
    }

    /** @test */
    public function can_get_ticket_price_in_dollars()
    {
        $concert = create(Concert::class, [
            'ticket_price' => 1234
        ]);

        $this->assertEquals(12.34, $concert->ticketpriceindollars);
    }

    /** @test */
    public function can_get_published_concerts()
    {
        create(Concert::class, ['published_at' => null]);

        $this->assertEquals(1, Concert::published()->count());
    }

    /** @test */
    public function can_order_concert_tickets()
    {
        $order = $this->concert->orderTickets('johndoe@example.com', 3);

        $this->assertDatabaseHas('orders', ['email' => 'johndoe@example.com']);

        $this->assertEquals(3, $order->ticketQuantity());
    }

    /** @test */
    public function can_add_tickets()
    {
        $this->assertEquals(3, Ticket::count());
    }

    /** @test */
    public function tickets_remaining_are_not_associated_with_existing_orders()
    {
        $order = $this->concert->orderTickets('johndoe@example.com', 2);

        $this->assertEquals(1, $this->concert->ticketsRemaining());
    }

    /** @test */
    public function can_reserve_available_tickets()
    {
        $reservation = $this->concert->reserveTickets(2, 'johndoe@example.com');

        $this->assertCount(2, $reservation->tickets());

        $this->assertEquals(1, $this->concert->fresh()->ticketsRemaining());
    }
}
