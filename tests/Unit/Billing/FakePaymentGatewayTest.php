<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Billing\FakePaymentGateway;
use App\Exceptions\PaymentFailedException;

class FakePaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(1, $paymentGateway->getValidTestToken());

        $this->assertEquals(1, $paymentGateway->totalCharges());
    }

    /** @test */
    public function charges_with_an_invalid_payment_token_fail()
    {
        $this->expectException(PaymentFailedException::class);

        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(1, 'invalid-payment-token');
    }

    /** @test */
    public function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = new FakePaymentGateway;

        $callbackRan = false;

        $paymentGateway->beforeFirstCharge(function ($gateway) use (&$callbackRan) {
            $callbackRan = true;

            $this->assertEquals(0, $gateway->totalCharges());
        });

        $paymentGateway->charge(1, $paymentGateway->getValidTestToken());

        $this->assertTrue($callbackRan);

        $this->assertEquals(1, $paymentGateway->totalCharges());
    }
}
