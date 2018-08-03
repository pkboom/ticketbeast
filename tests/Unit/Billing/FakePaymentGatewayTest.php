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

        $this->paymentGateway->charge(2500, $this->paymentGateway->getValidTestToken(), 'test_account_1234');

        $this->assertTrue($callbackRan);

        $this->assertEquals(2500, $this->paymentGateway->lastCharge()->amount());
    }

    /** @test */
    public function it_can_get_total_charges_for_a_user()
    {
        $this->paymentGateway->charge(1000, $this->paymentGateway->getValidTestToken(), 'test_account_0000');
        $this->paymentGateway->charge(1000, $this->paymentGateway->getValidTestToken(), 'test_account_1234');
        $this->paymentGateway->charge(1000, $this->paymentGateway->getValidTestToken(), 'test_account_1234');

        $this->assertEquals(2000, $this->paymentGateway->totalChargesFor('test_account_1234'));
    }

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway;
    }
}
