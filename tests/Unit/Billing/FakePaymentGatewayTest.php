<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\FakePaymentGateway;

class FakePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    public function setUp()
    {
        parent::setUp();

        $this->paymentGateway = $this->getPaymentGateway();
    }

    /** @test */
    public function running_a_hook_before_the_first_charge()
    {
        $callbackRan = false;

        $this->paymentGateway->beforeFirstCharge(function ($gateway) use (&$callbackRan) {
            $callbackRan = true;

            $this->assertEquals(0, $gateway->totalCharges());
        });

        $this->paymentGateway->charge(2500, $this->paymentGateway->getValidTestToken());

        $this->assertTrue($callbackRan);

        $this->assertEquals(2500, $this->paymentGateway->lastCharge()->amount());
    }

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }
}
