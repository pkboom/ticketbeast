<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Billing\PaymentGateway;

class Reservation extends Model
{
    protected $tickets;

    protected $email;

    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;

        $this->email = $email;
    }

    public function complete(PaymentGateway $paymentGateway, $token)
    {
        $paymentGateway->charge($this->totalCost(), $token);

        return Order::forTickets($this->tickets, $this->email, $this->totalCost());
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
