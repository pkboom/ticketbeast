<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\NotEnoughTicketsException;
use App\OrderConfirmationNumberGenerator;
use App\Facades\OrderConfirmationNumber;
use App\Facades\TicketCode;
use App\Ticket;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationEmail;
use App\Factory\ConcertFactory;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;

        $this->app->instance(PaymentGateway::class, $this->paymentGateway);

        $this->concert = ConcertFactory::createPublished(['ticket_quantity' => 3]);

        $this->purchaseInfo = [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ];
    }

    /** @test */
    public function customer_may_not_purchase_tickets_to_unpublished_concert()
    {
        $this->post('/concerts/2/orders')
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function customer_can_purchase_tickets_to_a_published_concert()
    {
        // $orderConfirmationNumberGenerator = \Mockery::mock(OrderConfirmationNumberGenerator::class, [
        //     'generate' => 'ORDERCONFIRMATION1234',
        // ]);

        // app()->instance(OrderConfirmationNumberGenerator::class, $orderConfirmationNumberGenerator);

        Mail::fake();

        OrderConfirmationNumber::shouldReceive('generate')
            ->andReturn('ORDERCONFIRMATION1234');

        TicketCode::shouldReceive('generateFor')
            ->with(Ticket::class)
            ->andReturn('TICKETCODE1', 'TICKETCODE2', 'TICKETCODE3');

        $this->postJson('/concerts/1/orders', $this->purchaseInfo)
            ->assertJson([
                'confirmation_number' => 'ORDERCONFIRMATION1234',
                'email' => $this->purchaseInfo['email'],
                'amount' => $this->concert->ticket_price * $this->purchaseInfo['ticket_quantity'],
                'tickets' => [
                    ['code' => 'TICKETCODE1'],
                    ['code' => 'TICKETCODE2'],
                    ['code' => 'TICKETCODE3'],
                ],
        ]);

        $this->assertEquals(
            $this->concert->ticket_price * $this->purchaseInfo['ticket_quantity'],
            $this->paymentGateway->lastCharge()->amount()
        );

        $this->assertTrue($this->concert->hasOrderFor($this->purchaseInfo['email']));

        $this->assertEquals(
            $this->purchaseInfo['ticket_quantity'],
            $this->concert->ordersFor($this->purchaseInfo['email'])->ticketQuantity()
        );

        $order = $this->concert->ordersFor($this->purchaseInfo['email']);

        Mail::assertSent(OrderConfirmationEmail::class, function ($mail) use ($order) {
            return $mail->hasTo($this->purchaseInfo['email']) &&
                $mail->order->id == $order->id;
        });
    }

    /** @test */
    public function email_is_required_to_purchase_a_ticket()
    {
        $this->orderTickets(['email' => ''])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function ticket_quantity_is_required_to_purchase_a_ticket()
    {
        $this->orderTickets(['ticket_quantity' => ''])
            ->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    public function payment_token_is_required_to_purchase_a_ticket()
    {
        $this->orderTickets(['payment_token' => ''])
            ->assertSessionHasErrors('payment_token');
    }

    /** @test */
    public function email_must_be_valid_to_purchase_a_ticket()
    {
        $this->orderTickets(['email' => 'not an email'])
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function ticket_quantity_must_be_integer_to_purchase_a_ticket()
    {
        $this->orderTickets(['ticket_quantity' => 'not an integer'])
            ->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    public function ticket_quantity_must_be_more_than_0_to_purchase_a_ticket()
    {
        $this->orderTickets(['ticket_quantity' => 0])
            ->assertSessionHasErrors('ticket_quantity');
    }

    /** @test */
    public function an_order_is_not_created_if_payment_fails()
    {
        $this->orderTickets(['payment_token' => 'invalid token'])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function customer_may_not_purchase_more_tickets_than_remain()
    {
        $this->withoutExceptionHandling();

        $this->expectException(NotEnoughTicketsException::class);

        $this->orderTickets(['ticket_quantity' => 4]);

        $this->orderTickets(['ticket_quantity' => 2]);

        $this->assertEquals(1, $this->concert->ticketsRemaining());

        $this->orderTickets(['ticket_quantity' => 2])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function customer_may_not_reserve_tickets_that_have_been_purchased()
    {
        $this->expectException(NotEnoughTicketsException::class);

        $this->orderTickets();

        $this->concert->reserveTickets(1, 'johndoe@example.com');
    }

    /** @test */
    public function customer_may_not_reserve_tickets_that_have_been_reserved()
    {
        $this->expectException(NotEnoughTicketsException::class);

        $this->concert->reserveTickets(3, 'johndoe@example.com');

        $this->concert->reserveTickets(3, 'johndoe@example.com');
    }

    public function orderTickets($param = [])
    {
        return $this->post('/concerts/1/orders', array_merge($this->purchaseInfo, $param));
    }

    /** @test */
    public function t()
    {
        // $this->f(1, 2, 3);
        // $this->ff(1, 2, 3);
    }

    public function f()
    {
        // dd(func_get_args());
        $this->f2(func_get_args());
    }

    public function ff(...$args)
    {
        dd($args);
    }

    public function f2(...$arg)
    {
        dd($arg);
    }
}
