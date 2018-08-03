<?php

namespace Tests\Unit\Billing;

use App\Exceptions\PaymentFailedException;

trait PaymentGatewayContractTests
{
    abstract protected function getPaymentGateway();

    /** @test */
    public function charges_with_a_valid_payment_token_are_successful()
    {
        $charge = $this->paymentGateway->charge(
            2500,
            $this->paymentGateway->getValidTestToken($this->paymentGateway::TEST_CARD_NUMBER),
            'test_account_1234'
        );

        $this->assertEquals(2500, $this->paymentGateway->lastCharge()->amount());
    }

    /** @test */
    public function charges_with_an_invalid_payment_token_fail()
    {
        $this->expectException(PaymentFailedException::class);

        $this->paymentGateway->charge(2500, 'invalid-payment-token', 'test_account_1234');
    }

    /** @test */
    public function it_can_get_details_about_a_successful_charge()
    {
        $charge = $this->paymentGateway->charge(
            2500,
            $this->paymentGateway->getValidTestToken($this->paymentGateway::TEST_CARD_NUMBER),
            'test_account_1234'
        );

        $this->assertEquals(2500, $charge->amount());
        $this->assertEquals('4242', $charge->cardLastFour());
        $this->assertEquals('test_account_1234', $charge->destination());
    }
}
