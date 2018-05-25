<?php

namespace Tests\Unit\Main;

use Tests\TestCase;
use App\Order;
use App\Mail\OrderConfirmationEmail;

class OrderConfirmationEmailTest extends TestCase
{
    /** @test */
    public function email_contains_a_link_to_the_order_confirmation_page()
    {
        $email = (new OrderConfirmationEmail(
                factory(Order::class)->make()
            ))->render();

        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $email);
    }

    /** @test */
    public function email_has_a_subject()
    {
        $email = (new OrderConfirmationEmail(factory(Order::class)->make()));

        $this->assertEquals(
            'Your TicketBeast Order',
            $email->build()->subject
        );
    }
}
