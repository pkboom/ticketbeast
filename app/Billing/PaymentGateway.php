<?php

namespace App\Billing;

interface PaymentGateway
{
    public function charge($amount, $token, $destination);

    public function getValidTestToken();
}
