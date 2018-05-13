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

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public static function forTickets($tickets, $email, $amount)
    {
        $order = static::create([
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'email' => $email,
            'amount' => $tickets->sum('price'),
            ]);

        $order->tickets()->saveMany($tickets);

        // foreach ($tickets as $ticket) {
        //     $order->tickets()->save($ticket);
        // }

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
}
