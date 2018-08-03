<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Billing\PaymentGateway;

class Reservation extends Model
{
    protected $tickets;

    protected $email;

    protected $accountId;

    public function __construct($tickets, $email, $accountId)
    {
        $this->tickets = $tickets;

        $this->email = $email;

        $this->accountId = $accountId;
    }

    public function complete(PaymentGateway $paymentGateway, $token)
    {
        $charge = $paymentGateway->charge($this->totalCost(), $token, $this->accountId);

        return Order::forTickets($this->tickets(), $this->email(), $charge);
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function cancel()
    {
        return $this->tickets->each->release();
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }
}
