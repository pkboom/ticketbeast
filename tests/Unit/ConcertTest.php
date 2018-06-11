<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use Carbon\Carbon;
use App\Ticket;
use App\Factory\ConcertFactory;
use App\Order;

class ConcertTest extends TestCase
{
    use RefreshDatabase;

    protected $concert;

    public function setUp()
    {
        parent::setUp();

        $this->concert = ConcertFactory::createPublished(['ticket_quantity' => 3]);
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
    public function can_add_tickets()
    {
        $this->assertEquals(3, Ticket::count());
    }

    /** @test */
    public function can_reserve_available_tickets()
    {
        $reservation = $this->concert->reserveTickets(2, 'johndoe@example.com');

        $this->assertCount(2, $reservation->tickets());

        $this->assertEquals(1, $this->concert->fresh()->ticketsRemaining());
    }

    /** @test */
    public function can_count_sold_tickets()
    {
        $this->concert->tickets()->whereId(1)->update(['order_id' => 1]);
        $this->concert->tickets()->whereId(2)->update(['order_id' => 1]);

        $this->assertEquals(2, $this->concert->ticketsSold());
    }

    /** @test */
    public function it_calculates_the_revenue_in_dollars()
    {
        $order = factory(Order::class)->create();

        $this->concert->tickets()->whereId(1)->update(['order_id' => $order->id]);
        $this->concert->tickets()->whereId(2)->update(['order_id' => $order->id]);

        $this->assertEquals($order->amount / 100, $this->concert->revenueInDollars());
    }

}
