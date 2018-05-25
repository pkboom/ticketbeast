<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Ticket;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_order_confirmation()
    {
        $this->withoutExceptionHandling();

        $ticket = factory(Ticket::class)->create();
        // dd($ticket->order->toArray());

        // $this->get("/orders/{$ticket->order->confirmation_number}")
        //     ->assertSee('**** **** **** 1881')
        //     ->assertSee($ticket->code)
        //     ->assertSee($order->email)
        //     ->assertSee(number_format($order->amount / 100, 2))
        //     ->assertSee($order->confirmation_number)
        //     ->assertSee($concert->title)
        //     ->assertSee($concert->subtitle)
        //     ->assertSee($concert->date->format('Y-m-d H:i'))
        //     ->assertSee($concert->venue)
        //     ->assertSee($concert->venue_address)
        //     ->assertSee($concert->city)
        //     ->assertSee($concert->state)
        //     ->assertSee($concert->zip);
    }
}
