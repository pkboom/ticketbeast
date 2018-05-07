<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Concert;
use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use Symfony\Component\HttpFoundation\Response;
use App\Exceptions\NotEnoughTicketsException;

class PurchaseTicketsTest extends TestCase
{
    use RefreshDatabase;

    protected $paymentGateway;

    protected $concert;

    protected $purchaseInfo;

    public function setUp()
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;

        $this->app->instance(PaymentGateway::class, $this->paymentGateway);

        $this->concert = tap(factory(Concert::class)->states('published')->create())
                            ->addTickets(3);

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
        $this->postJson('/concerts/1/orders', $this->purchaseInfo)
            ->assertJson([
                'email' => 'john@example.com',
                'ticket_quantity' => 3,
                'amount' => $this->concert->ticket_price * 3,
        ]);

        $this->assertEquals($this->concert->ticket_price * 3, $this->paymentGateway->totalCharges());

        $this->assertTrue($this->concert->hasOrderFor('john@example.com'));

        $this->assertEquals(3, $this->concert->ordersFor('john@example.com')->ticketQuantity());
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
        $this->withoutExceptionHandling();
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
}
