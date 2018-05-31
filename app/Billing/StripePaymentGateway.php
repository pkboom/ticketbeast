<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;

class StripePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    public function __construct($apiKey)
    {
        \Stripe\Stripe::setApiKey($apiKey);
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        return \Stripe\Token::create([
            'card' => [
                'number' => $cardNumber,
                'exp_month' => 5,
                'exp_year' => date('Y') + 1,
                'cvc' => '314'
            ]
        ])->id;
    }

    /**
     * @return Stripe\Charge
     */
    public function charge($amount, $token)
    {
        try {
            $stripeCharge = \Stripe\Charge::create([
                'amount' => $amount,
                'currency' => 'usd',
                'source' => $token
            ]);

            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
            ]);
        } catch (\Stripe\Error\InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }

    public function lastCharge()
    {
        return array_first(\Stripe\Charge::all(
            ['limit' => 1]
        )->data);
    }
}
