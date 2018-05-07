<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

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

    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
        ];
    }
}