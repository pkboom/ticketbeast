<?php

namespace Tests\Unit\Billing;

use Tests\TestCase;
use App\Billing\StripePaymentGateway;

/**
 * @group integration
 */
class StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;

    public function setUp()
    {
        parent::setUp();

        $this->paymentGateway = $this->getPaymentGateway();
    }

    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }
}
