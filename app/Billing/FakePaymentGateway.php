<?php

namespace App\Billing;

use App\Exceptions\PaymentFailedException;

class FakePaymentGateway implements PaymentGateway
{
    private $charges;

    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
    }

    public function getValidTestToken()
    {
        return 'valid token';
    }

    public function charge($amount, $token)
    {
        if ($this->beforeFirstChargeCallback !== null) {
            // How to call a closure that is a class variable?
            // https://stackoverflow.com/questions/7067536/how-to-call-a-closure-that-is-a-class-variable

            // $this->beforeFirstChargeCallback->__invoke($this);
            // ($this->beforeFirstChargeCallback)($this);
            $callback = $this->beforeFirstChargeCallback;
            $this->beforeFirstChargeCallback = null;
            $callback($this);
        }

        if ($token !== $this->getValidTestToken()) {
            throw new PaymentFailedException;
        }

        $this->charges[] = $amount;
    }

    public function totalCharges()
    {
        return $this->charges->sum();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}
