<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use App\Order;
use App\Ticket;

class ViewOrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_order_confirmation()
    {
        $this->withoutExceptionHandling();

        $concert = factory(Concert::class)->create();
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMATION1234',
            'card_last_four' => '1881',
        ]);
        $ticketA = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE123'
        ]);
        $ticketB = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE456'
        ]);
        // $concert = tap(factory(Concert::class)->states('published')->create())
        //     ->addTickets(3);

        // $order = factory(Order::class)->create([
        //     'confirmation_number' => 'ORDERCONFIRMATION1234',
        //     'card_last_four' => '1881'
        // ]);

        // dd($order);

        // $order = $concert->orderTickets('johndoe@example.com', 3, 'ORDERCONFIRMATION1234');

        $this->get("/orders/{$order->confirmation_number}")
            ->assertSee('**** **** **** 1881')
            ->assertSee($ticketA->code)
            ->assertSee($ticketB->code)
            ->assertSee($order->email)
            ->assertSee(number_format($order->amount / 100, 2))
            ->assertSee($order->confirmation_number)
            ->assertSee($concert->title)
            ->assertSee($concert->subtitle)
            ->assertSee($concert->date->format('Y-m-d H:i'))
            ->assertSee($concert->venue)
            ->assertSee($concert->venue_address)
            ->assertSee($concert->city)
            ->assertSee($concert->state)
            ->assertSee($concert->zip);
    }
}
