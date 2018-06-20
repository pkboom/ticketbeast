<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\NotEnoughTicketsException;

class Concert extends Model
{
    protected $guarded = [];

    protected $dates = ['date', 'published_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        // Don't use belongsToMnay.
        // Because it will try to access empty orders.
        return Order::whereIn('Id', $this->tickets()->pluck('order_id'));
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function attendeeMessages()
    {
        return $this->hasMany(AttendeeMessage::class);
    }

    public function isPublished()
    {
        return $this->published_at !== null;
    }

    public function publish()
    {
        $this->update(['published_at' => $this->freshTimestamp()]);

        $this->addTickets($this->ticket_quantity);
    }

    public function getFormattedDateAttribute()
    {
        return $this->date->format('F L, Y');
    }

    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function reserveTickets($quantity, $email)
    {
        return new Reservation(
            $this->findTickets($quantity)->each->reserve(),
            $email
        );
    }

    public function findTickets($ticketQuantity)
    {
        if ($this->ticketsRemaining() < $ticketQuantity) {
            throw new NotEnoughTicketsException;
        }

        return $this->tickets()->available()->take($ticketQuantity)->get();
    }

    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;
    }

    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }

    public function hasOrderFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->count() > 0;
    }

    public function ordersFor($customerEmail)
    {
        return $this->orders()->where('email', $customerEmail)->first();
    }

    public function ticketsSold()
    {
        return $this->tickets()->sold()->count();
    }

    public function percentSoldOut()
    {
        return number_format(($this->ticketsSold() / $this->tickets_quantity) * 100, 2);
    }

    public function revenueInDollars()
    {
        return $this->orders()->sum('amount') / 100;
    }

    public function hasPoster()
    {
        return $this->poster_image_path !== null;
    }

    public function posterUrl()
    {
        return Storage::disk('public')->url($this->poster_image_path);
    }
}
