<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;

class FakePaymentGateway implements PaymentGateway
{
    const TEST_CARD_NUMBER = '4242424242424242';

    private $charges;

    private $tokens;

    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();

        $this->tokens = collect();
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        $token = 'fake-tok_' . str_random(24);

        $this->tokens[$token] = $cardNumber;

        return $token;
    }

    public function charge($amount, $token)
    {
        if (isset($this->beforeFirstChargeCallback)) {
            // How to call a closure that is a class variable?
            // https://stackoverflow.com/questions/7067536/how-to-call-a-closure-that-is-a-class-variable

            // $this->beforeFirstChargeCallback->__invoke($this);
            // ($this->beforeFirstChargeCallback)($this);
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if (!$this->tokens->has($token)) {
            throw new PaymentFailedException;
        }

        return $this->charges[] = new Charge([
            'amount' => $amount,
            'card_last_four' => substr($this->tokens[$token], -4),
        ]);
    }

    public function lastCharge()
    {
        return $this->charges->first();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }

    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }
}
