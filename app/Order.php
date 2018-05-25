<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Facades\OrderConfirmationNumber;

class Order extends Model
{
    protected $guarded = [];

    protected $appends = ['ticket_quantity'];

    public function getRouteKeyName()
    {
        return 'confirmation_number';
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public static function forTickets($tickets, $email, $charge)
    {
        $order = static::create([
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'email' => $email,
            'amount' => $charge->amount(),
            'card_last_four' => $charge->cardLastFour(),
            ]);

        $tickets->each->claimFor($order);

        // $order->tickets()->saveMany($tickets);

        return $order;
    }

    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }

    public function getTicketQuantityAttribute()
    {
        return $this->ticketQuantity();
    }

    public function toArray()
    {
        return [
            'confirmation_number' => $this->confirmation_number,
            'email' => $this->email,
            'amount' => $this->amount,
            'tickets' => $this->tickets->map(function ($ticket) {
                return ['code' => $ticket->code];
            })->all(),
        ];
    }
}
