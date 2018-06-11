<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Ticket;
use App\Concert;
use App\Order;
use App\Facades\TicketCode;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_ticket_can_be_reserved()
    {
        $ticket = create(Ticket::class);

        $this->assertNull($ticket->reserved_at);

        $ticket->reserve();

        $this->assertNotNull($ticket->fresh()->reserved_at);
    }

    /** @test */
    public function a_ticket_can_be_release()
    {
        $ticket = create(Ticket::class);

        $ticket->reserve();

        $ticket->release();

        $this->assertNull($ticket->reserved_at);
    }

    /** @test */
    public function a_ticket_can_produce_an_array()
    {
        $concert = factory(Concert::class)->create();

        $order = factory(Order::class)->create();

        $tickets = factory(Ticket::class, 2)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
        ]);

        $this->assertEquals(
            [
                'confirmation_number' => $order->confirmation_number,
                'email' => $order->email,
                'amount' => $order->amount,
                'tickets' => $tickets->map(function ($ticket) {
                    return ['code' => $ticket->code];
                })->all(),
            ],
            $order->toArray()
        );
    }

    /** @test */
    public function a_ticket_can_be_claimed_for_an_order()
    {
        $ticket = factory(Ticket::class)->create();

        $order = factory(Order::class)->create();

        TicketCode::shouldReceive('generateFor')
            ->with($ticket)
            ->andReturn('asdf');

        $ticket->claimFor($order);

        $this->assertEquals('asdf', $ticket->code);
    }

}
